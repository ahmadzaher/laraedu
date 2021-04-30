<?php

namespace App\Http\Controllers;

use App\Rules\Nospaces;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        $main_menu = 'profile';

        return view('profile.edit', compact('user', 'main_menu'));
    }
    public function update(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id, 'min:8', new Nospaces],
        ]);

        $user->name =  $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->save();
        return redirect('/')->with('success', 'Your Profile has been edited successfully!');
    }
}
