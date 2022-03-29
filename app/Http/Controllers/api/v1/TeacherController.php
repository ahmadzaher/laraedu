<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Role;
use App\Rules\Nospaces;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function getUsers(Request $request)
    {
        $search = $request->search;
        $data = User::latest()
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
            ->where('roles.slug', '=', 'teacher')
            ->where(function ($query) use ($search) {
                $query->where('users.name', 'like', '%'.$search.'%')
                    ->orWhere('users.email', 'like', '%'.$search.'%')
                    ->orWhere('users.username', 'like', '%'.$search.'%')
                    ->orWhere('users.number', 'like', '%'.$search.'%');
            })
            ->select(
                'users.id',
                'users.name',
                'email',
                'users.created_at',
                'username',
                'number',
                'department_id',
                'roles.slug as role',
                'departments.name as department_name'
            )->paginate($request->per_page);
        foreach($data as $key => $teacher)
        {
            $avatar = $teacher->getFirstMediaUrl('avatars', 'thumb') != null ? url($teacher->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');

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

//        if(!$request->user()->can('edit-student')){
//            return response()->json(['message' => 'You don\'t have permission to edit user'], 403);
//        }
        $user_roles = [];
        $student_role = new \stdClass();
        $student_role->slug = 'teacher';
        foreach($user->roles as $role){
            $user_roles[] = $role->slug;
        }
        if(!in_array('teacher', $user_roles)){
            return response()->json(['message' => 'Something went wrong!'], 404);
        }
        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'direction' => $user->direction,
            'language' => $user->language,
            'username' => $user->username,
            'name' => $user->name,
            'phone_number' => $user->number,
            'avatar' => $avatar
        ], 200);

    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8', new Nospaces],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
//            'section' => ['required'],
//            'class' => ['required'],
        ]);
        $user = new User([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'direction' => $request->direction,
            'language' => $request->language,
            'password' => Hash::make($request->password),
            'number' => $request->phone_number,

        ]);
        $user->save();
        if (isset($request->avatar)) {
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        $role = Role::Where(['slug' => 'teacher'])->get();
        $user->roles()->attach($role);

        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;


        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'direction' => $user->direction,
            'language' => $user->language,
            'phone_number' => $user->number,
            'avatar' => $avatar
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
        if($user == null){
            return response()->json(['message' => 'Something went wrong!'], 404);
        }

        $user->name =  $request->name;
        $user->direction =  $request->direction;
        $user->language =  $request->language;
        $user->email =  $request->email;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->phone_number;
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

            $role = Role::Where(['slug' => 'teacher'])->get();
            $user->roles()->sync($role);

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
            'avatar' => $avatar
        ], 200);
    }


    public function destroy(Request $request, $id)
    {
        $user = User::find($id);


        if($user == null){
            return response()->json(['message' => 'Something went wrong!'], 404);
        }

        if($id == Auth::id()){
            return response()->json(['message' => 'You can\'t delete your account'], 403);
        }
        if($user->hasRole('superadmin')){
            return response()->json(['message' => 'You can\'t delete superadmin user'], 403);
        }
        if($user->hasRole('teacher')){
            $user->delete();

            return response()->json(['message' => 'User Deleted!'], 200);
        }
        return response()->json(['message' => 'Something went wrong!'], 404);
    }
}
