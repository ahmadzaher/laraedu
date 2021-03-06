<?php

namespace App\Http\Controllers;

use App\Role;
use App\Rules\Nospaces;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        if(!$request->user()->can('view-user')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view users');
        }
        return view('users.user');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-user')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view users');
            }
            $data = User::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();

            if($user->can('edit-user') || $user->can('delete-user')){
                $datatable->addColumn('action', function($row){
                    $user_id = $row->id;
                    $actionBtn = view('users.control_buttons', compact('user_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            $datatable->addColumn('avatar', function ($row){
                $avatar = $row->getFirstMediaUrl('avatars', 'thumb') != null ? $row->getFirstMediaUrl('avatars', 'thumb') : url('/images/avatar.jpg');
                return '<img src="'.$avatar.'" alt="avatar" class="avatar rounded img-responsive mr-1" />';
            })->rawColumns(['avatar']);
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-user')){
            return redirect('/user')->with('warning', 'You don\'t have permission to add user');
        }
        $roles = Role::all();
        return view('users.add', compact('roles'));
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-user')){
            return redirect('/user')->with('warning', 'You don\'t have permission to add user');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8', new Nospaces],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $user = new User([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'number' => $request->number,
        ]);
        $user->save();
        if (isset($request->avatar)) {
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        $roles = $request->roles;
        $user->roles()->attach($roles);

        // Assign permissions to user
//        if(is_array($roles))
//        foreach($roles as $role){
//            $role = Role::find($role);
//            $permission = $role->permissions()->get();
//            $user->permissions()->detach($permission);
//            $user->permissions()->attach($permission);
//        }

        return redirect('/user')->with('success', 'User saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-user')){
            return redirect('/user')->with('warning', 'You don\'t have permission to delete user');
        }
        $user = User::find($id);

        if($id == Auth::id()){
            return redirect('/user')->with('warning', 'You can\'t delete your profile');
        }
        if($user->hasRole('superadmin')){
            return redirect('/user')->with('warning', 'You can\'t delete superadmin user');
        }
        $user->delete();

        return redirect('/user')->with('success', 'User Deleted!');
    }
    public function edit(Request $request, $id)
    {
        $user = User::with('roles')->find($id);

        if(!$request->user()->can('edit-user')){
            return redirect('/user')->with('warning', 'You don\'t have permission to edit user');
        }
        $user_roles = [];
        foreach($user->roles as $role){
            $user_roles[] = $role->id;
        }
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles', 'user_roles'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$id, 'min:8', new Nospaces],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'password' => ['string', 'min:8', 'confirmed', 'nullable'],
        ]);
        $user = User::find($id);

        if(!$request->user()->can('edit-user')){
            return redirect('/user')->with('warning', 'You don\'t have permission to update user');
        }
        $user->name =  $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->number;
        if(isset($request->password))
            $user->password = Hash::make($request->password);
        $user->save();
        if (isset($request->avatar)) {
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        if($id != $request->user()->id){
            $roles = $request->roles;
            $user->roles()->sync($roles);

            // Assign permission to staff

//        $user->permissions()->detach();
//        if(is_array($roles))
//        foreach($roles as $role){
//            $role = Role::find($role);
//            $permission = $role->permissions()->get();
//            $user->permissions()->detach($permission);
//            $user->permissions()->attach($permission);
//        }
        }


        return redirect('/user')->with('success', 'User updated!');
    }
}
