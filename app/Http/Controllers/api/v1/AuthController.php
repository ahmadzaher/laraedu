<?php

namespace App\Http\Controllers\api\v1;
use App\Http\Controllers\Controller;
use App\Role;
use App\Rules\Nospaces;
use App\Traffic;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use function React\Promise\map;

class AuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', 'min:4'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8', new Nospaces],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->phone_number,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'is_activated' => 0
        ]);

        $role = Role::Where(['slug' => 'student'])->get();
        $user->roles()->attach($role);

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;
        $traffic = new Traffic([
            'user_id' => $user->id,
            'type' => 'register'
        ]);
        $traffic->save();

        $user['link'] = Str::random(6);
        DB::table('users_activation')->insert(['id_user'=>$user['id'],'token'=>$user['link']]);
        $user->sendEmailCodeVerificationNotification($user['link']);

        return response()->json(['token' => $token], 200);
    }

    /**

     * Check for user Activation Code

     *

     */

    public function userActivation($token)
    {
        $check = DB::table('users_activation')->where('token',$token)->first();
        if(!is_null($check)){
            $user = User::find($check->id_user);

            if(!$user || $user->id != auth()->user()->id)
                return response(['message' => 'Something went wrong.'], 403);

            if($user->hasVerifiedEmail()){
                return response(['message' => 'User has already activated.'], 403);
            }else{
                $user->markEmailAsVerified();
                DB::table('users_activation')->where('token',$token)->delete();
                return response(['message' => 'User activated successfully.']);
            }
        }
        return response(['message' => 'Your token is invalid.'], 403);
    }


    public function resend() {
        $user = auth()->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(["message" => "Email already verified."], 403);
        }

        $user['link'] = Str::random(6);
        DB::table('users_activation')->insert(['id_user'=>$user['id'],'token'=>$user['link']]);
        $user->sendEmailCodeVerificationNotification($user['link']);

        return response()->json(["message" => "Email verification link sent on your email id"]);
    }

    /**
     * Change password
     */

    public function change_password(Request $request)
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'old_password' => ['required', 'string', 'min:8'],
        ]);
        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL )
            ? 'email'
            : 'username';
        $request->merge([
            $login_type => $request->input('login')
        ]);
        $user = auth()->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['message' => 'Password changed successfully!'], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 403);
        }
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL )
            ? 'email'
            : 'username';
        $request->merge([
            $login_type => $request->input('login')
        ]);

        if (Auth::attempt($request->only($login_type, 'password'))) {
            $traffic = new Traffic([
                'user_id' => auth()->user()->id,
                'type' => 'login'
            ]);
            $traffic->save();
            // Student can sign in with only one device
//            if(\auth()->user()->hasRole('student'))
//                DB::table('oauth_access_tokens')->where('user_id', auth()->user()->id)->delete();
            $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function google(Request $request)
    {
        $token = $request->token;
        try {
            $user = Socialite::driver('google')->stateless()->userFromToken($token);
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

        $user_exist = User::where('email', $user->email)->first();
        $user = User::firstOrCreate([
            'email' => $user->email
        ], [
            'username' => $user->email,
            'name' => $user->name != null ? $user->name : $user->nickname,
            'is_verified' => true,
            'is_activated' => 0,
            'password' => Hash::make(Str::random(24)),
        ]);

        if(!$user_exist)
        {
            $role = Role::Where(['slug' => 'student'])->get();
            $user->roles()->attach($role);
        }

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;
        $traffic = new Traffic([
            'user_id' => $user->id,
            'type' => $user_exist ? 'login' : 'register'
        ]);
        $traffic->save();
        return response()->json(['token' => $token], 200);

    }

    public function facebook(Request $request)
    {
        $token = $request->token;
        // Attempt to query the graph:
        $graph_url = "https://graph.facebook.com/me?"
            . "access_token=" . $token;
        $response = [];
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $graph_url);
        $contents = curl_exec($c);
        $err = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($contents)
            $response = $contents;
        $decoded_response = json_decode($response);

        if (isset($decoded_response->error)) {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        $user = Socialite::driver('facebook')->stateless()->userFromToken($token);
        $user_exist = User::where('email', $user->email)->first();
        $user = User::firstOrCreate([
            'email' => $user->email
        ], [
            'username' => $user->email,
            'name' => $user->name != null ? $user->name : $user->nickname,
            'is_verified' => true,
            'is_activated' => 0,
            'password' => Hash::make(Str::random(24)),
        ]);

        if(!$user_exist)
        {
            $role = Role::Where(['slug' => 'student'])->get();
            $user->roles()->attach($role);
        }

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;
        $traffic = new Traffic([
            'user_id' => $user->id,
            'type' => $user_exist ? 'login' : 'register'
        ]);
        $traffic->save();
        return response()->json(['token' => $token], 200);

    }

    public function userinfo(Request $request)
    {

        $user = auth()->user();

        $user_info = User::find($user->id);
        $avatar = $user_info->getFirstMediaUrl('avatars', 'thumb') ? url($user_info->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        $phone_number = $user->number;
        unset($user->number);
        $user->avatar = $avatar;

        $user_roles = [];

        $user_permissions = [];

        foreach($user->roles as $role){
//            if($role->slug == 'student' or $role->slug == 'teacher'){
//                return redirect('/user')->with('warning', 'Something went Wrong');
//            }
            $user_roles[] = [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug
            ];
            foreach ( $role->permissions as $key => $permission )
            {
                $permission_data = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug
                ];
                if(in_array($permission_data, $user_permissions))
                    continue;
                $user_permissions[] = $permission_data;
            }
        }

        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'is_verified' => $user->email_verified_at ? true : false,
            'coins' => $user->coins,
            'branch_id' => $user->branch_id,
            'year' => $user->year,
            'seller_id' => $user->seller_id,
            'subject_id' => $user->subject_id,
            'direction' => $user->direction,
            'language' => $user->language,
            'phone_number' => $phone_number,
            'avatar' => $avatar,
            'roles' => $user_roles,
            'permissions' => $user_permissions
        ], 200);

    }

    public function edit_profile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id, 'min:8', new Nospaces],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'password' => ['string', 'min:8', 'confirmed', 'nullable'],
        ]);

        $user->name =  $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->phone_number;
        $user->direction = $request->direction;
        $user->language = $request->language;

        if(isset($request->password))
            $user->password = Hash::make($request->password);
        $user->save();
        if (isset($request->delete_avatar)) {
            $user->clearMediaCollection('avatars');
        }
        if (isset($request->avatar)) {
            $user = User::find($user->id);
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
            $user->save();
        }
        $user_roles = [];

        $user_permissions = [];

        foreach($user->roles as $role){
            if($role->slug == 'student' or $role->slug == 'teacher'){
                break;
            }
            $user_roles[] = [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug
            ];
            foreach ( $role->permissions as $key => $permission )
            {
                $permission_data = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug
                ];
                if(in_array($permission_data, $user_permissions))
                    continue;
                $user_permissions[] = $permission_data;
            }
        }

        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'direction' => $user->direction,
            'language' => $user->language,
            'phone_number' => $user->number,
            'avatar' => $avatar,
            'roles' => $user_roles,
            'permissions' => $user_permissions
        ], 200);
    }
}