<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Rules\Nospaces;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers()
    {
        $data = User::latest()
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->where('roles.slug', '!=', 'student')
            ->where('roles.slug', '!=', 'teacher')
            ->orWhere('roles.slug', null)
            ->select(
                'users.id',
                'users.name',
                'email',
                'users.created_at',
                'username',
                'number'
            )
            ->groupBy('users.id')
            ->paginate(10);
        foreach($data as $key => $staff)
        {
            $avatar = $staff->getFirstMediaUrl('avatars', 'thumb') != null ? url($staff->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');

            $data[$key]['avatar'] = $avatar;
            unset($data[$key]['media']);
        }


        return response()->json($data, 200);
    }

    public function store(Request $request){
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

        $user_roles = [];

        $user_permissions = [];

        foreach($user->roles as $role){
            if($role->slug == 'student' or $role->slug == 'teacher'){
                return response()->json(['message' => 'Something went wrong!']);
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
        $user->phone_number = $user->number;
        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'avatar' => $avatar,
            'roles' => $user_roles,
            'permissions' => $user_permissions
        ], 200);


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

        }


        $user_roles = [];

        $user_permissions = [];

        foreach($user->roles as $role){
            if($role->slug == 'student' or $role->slug == 'teacher'){
                return response()->json(['message' => 'Something went wrong!']);
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
        $user->phone_number = $user->number;
        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'avatar' => $avatar,
            'roles' => $user_roles,
            'permissions' => $user_permissions
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if($id == Auth::id()){
            return response()->json(['message' => 'You cann\'t delete your account'], 403);
        }
        if($user->hasRole('superadmin')){
            return response()->json(['message' => 'You can\'t delete superadmin user'], 403);
        }
        $user->delete();

        return response()->json(['message' => 'User Deleted!'], 200);
    }
}