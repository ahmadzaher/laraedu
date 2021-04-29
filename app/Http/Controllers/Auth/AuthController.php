<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
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
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8'],
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
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
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
        $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($contents)
            $response =  $contents;
        $decoded_response = json_decode($response);

        if(isset($decoded_response->error)){
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        $user = Socialite::driver('facebook')->stateless()->userFromToken($token);
        $user = User::firstOrCreate([
            'email' => $user->email
        ], [
            'username' =>$user->email,
            'name' => $user->name != null ? $user->name : $user->nickname,
            'password' => Hash::make(Str::random(24))
        ]);
        $data = [
            'email' => $user->email,
            'password' => $user->password
        ];
        // return $user->password;
            $token = $user->createToken('Laravel8PassportAuth')->accessToken;
            return response()->json(['token' => $token], 200);

    }

    public function userInfo()
    {

        $user = auth()->user();

        return response()->json(['user' => $user], 200);

    }
}
