<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = $request->user();
//        dd($user->hasRole('developer')); //will return true, if user has role
//        dd($user->givePermissionsTo('create-tasks'));// will return permission, if not null
//        dd($user->can('create-user')); // will return true, if user has permission
        return view('home');
    }
}
