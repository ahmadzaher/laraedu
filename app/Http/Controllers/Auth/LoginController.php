<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->username = $this->findUsername();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function facebookCallback()
    {
        $user = Socialite::driver('facebook')->user();
        return redirect('/login')->with('token', $user->token);
        dd($user);
        $user_from_facebook = User::firstOrCreate([
            'email' => $user->email
        ], [
            'username' =>$user->email,
            'name' => $user->name != null ? $user->name : $user->nickname,
            'password' => Hash::make(Str::random(24))
        ]);
        $avatar = $user_from_facebook->getFirstMediaUrl('avatars', 'thumb') ? $user_from_facebook->getFirstMediaUrl('avatars', 'thumb') : ($user->avatar != null ? $user->avatar : url('/images/avatar.jpg'));
        $user_from_facebook->addMediaFromUrl($avatar)->toMediaCollection('avatars');

        Auth::login($user_from_facebook, true);
        return redirect('/home');
    }

    public function google()
    {
        return Socialite::driver('google')->redirect();
    }
    public function googleCallback()
    {
        $user = Socialite::driver('google')->user();
        return redirect('/login')->with('token', $user->token);
        $user_from_facebook = User::firstOrCreate([
            'email' => $user->email
        ], [
            'username' =>$user->email,
            'name' => $user->name != null ? $user->name : $user->nickname,
            'password' => Hash::make(Str::random(24))
        ]);
        $avatar = $user_from_facebook->getFirstMediaUrl('avatars', 'thumb') ? $user_from_facebook->getFirstMediaUrl('avatars', 'thumb') : ($user->avatar != null ? $user->avatar : url('/images/avatar.jpg'));
        $user_from_facebook->addMediaFromUrl($avatar)->toMediaCollection('avatars');

        Auth::login($user_from_facebook, true);
        return redirect('/home');
    }
}
