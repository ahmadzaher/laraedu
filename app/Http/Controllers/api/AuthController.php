<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Rules\Nospaces;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

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
            'number' => $request->number,
            'username' => $request->username,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;

        return response()->json(['token' => $token], 200);
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
            $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
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
        $user = User::firstOrCreate([
            'email' => $user->email
        ], [
            'username' => $user->email,
            'name' => $user->name != null ? $user->name : $user->nickname,
            'password' => Hash::make(Str::random(24))
        ]);
        // return $user->password;
        $token = $user->createToken('Laravel8PassportAuth')->accessToken;
        return response()->json(['token' => $token], 200);

    }

    public function userInfo()
    {

        $user = auth()->user();

        $user_info = User::find($user->id);
        $avatar = $user_info->getFirstMediaUrl('avatars', 'thumb') ? url($user_info->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        $user->phone_number = $user->number;
        unset($user->number);
        $user->avatar = $avatar;

        return response()->json(['user' => $user], 200);

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

        if(isset($request->password))
            $user->password = Hash::make($request->password);
        $user->save();
        if (isset($request->avatar)) {
            $user = User::find($user->id);
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }
        $user->save();
        $user->phone_number = $user->number;
        unset($user->number);
        return response()->json(['user' => $user], 200);
    }
}
