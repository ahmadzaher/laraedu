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
    public function getUsers(Request $request)
    {
        $search = $request->search;
        $branch_id = $request->branch_id;
        $year = $request->year;
        $data = User::latest()
            ->where(function ($query) use ($search, $branch_id, $year) {

                if ($branch_id != '') {
                    $query->where('users.branch_id', $branch_id);
                    $query->where('users.year', $year);
                }
            })
            ->where(function ($query) {
                $query->where('roles.slug', '!=', 'student')
                    ->where('roles.slug', '!=', 'teacher')
                    ->orWhere('roles.slug', null);
            })
            ->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%'.$search.'%')
                    ->orWhere('users.email', 'like', '%'.$search.'%')
                    ->orWhere('users.username', 'like', '%'.$search.'%')
                    ->orWhere('users.number', 'like', '%'.$search.'%');
            })
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->select(
                'users.id',
                'users.name',
                'email',
                'users.created_at',
                'username',
                'number'
            )
            ->groupBy('users.id')
            ->paginate($request->per_page);
        foreach($data as $key => $staff)
        {
            $avatar = $staff->getFirstMediaUrl('avatars', 'thumb') != null ? url($staff->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');

            $data[$key]['avatar'] = $avatar;
            unset($data[$key]['media']);
        }


        return response()->json($data, 200);
    }

    public function get(Request $request, $id)
    {
        $user = User::with('roles')->find($id);

        if($user == null){
            return response()->json(['message' => 'Something went wrong!'], 404);
        }

        if(!$request->user()->can('edit-user')){
            return response()->json(['message' => 'You don\'t have permission to edit user'], 403);
        }
        $user_roles = [];
        foreach($user->roles as $role){
            if($role->slug == 'student' or $role->slug == 'teacher'){
                return response()->json(['message' => 'Something went wrong!'], 404);
            }
            $user_roles[] = $role->id;
        }
        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'direction' => $user->direction,
            'language' => $user->language,
            'name' => $user->name,
            'phone_number' => $user->number,
            'avatar' => $avatar,
        ], 200);

    }

    public function store(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8', new Nospaces],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
//            'branch_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        $user = new User([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'direction' => $request->direction,
            'language' => $request->language,
            'password' => Hash::make($request->password),
            'number' => $request->phone_number,
            'branch_id' => $request->branch_id,
            'year' => $request->year,
            'is_activated' => 0
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$id, 'min:8', new Nospaces],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'password' => ['string', 'min:8', 'confirmed', 'nullable'],
//            'branch_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        $user = User::find($id);


        if($user == null){
            return response()->json(['message' => 'Something went wrong!'], 404);
        }

        $user->name =  $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->phone_number;
        $user->direction =  $request->direction;
        $user->language =  $request->language;
        $user->email =  $request->email;
        $user->branch_id = $request->branch_id;
        $user->year = $request->year;
        if(isset($request->password))
            $user->password = Hash::make($request->password);

        $user->save();
        if (isset($request->delete_avatar)) {
            $user->clearMediaCollection('avatars');
        }
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

        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'phone_number' => $user->number,
            'avatar' => $avatar,
            'direction' => $user->direction,
            'language' => $user->language,
            'roles' => $user_roles,
            'permissions' => $user_permissions
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        if($user == null){
            return response()->json(['message' => 'Something went wrong!'], 404);
        }
        if($id == Auth::id()){
            return response()->json(['message' => 'You cann\'t delete your account'], 403);
        }
        if($user->hasRole('superadmin')){
            return response()->json(['message' => 'You can\'t delete superadmin user'], 403);
        }
        if($user->hasRole('student') or $user->hasRole('teacher')){
            return response()->json(['message' => 'You can\'t delete student or teacher using this request'], 403);
        }
        $user->delete();

        return response()->json(['message' => 'User Deleted!'], 200);
    }
}
