<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Role;
use App\Rules\Nospaces;
use App\SchoolClass;
use App\SchoolSection;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function getUsers()
    {
        $data = User::latest()
            ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->leftJoin('school_classes', 'school_classes.id', '=', 'users.class_id')
            ->leftJoin('school_sections', 'school_sections.id', '=', 'users.section_id')
            ->where('roles.slug', '=', 'student')
            ->select(
                'users.id',
                'users.name',
                'email',
                'users.created_at',
                'username',
                'number',
                'school_classes.name as class_name',
                'school_sections.name as section_name'
            )->paginate(10);
        foreach($data as $key => $student)
        {
            $avatar = $student->getFirstMediaUrl('avatars', 'thumb') != null ? url($student->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');

            $data[$key]['avatar'] = $avatar;
            unset($data[$key]['media']);
        }


        return response()->json($data, 200);
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
            'password' => Hash::make($request->password),
            'number' => $request->number,

        ]);
        $user->schoolClass()->associate($request->class);
        $user->schoolSection()->associate($request->section);
        $user->save();
        if (isset($request->avatar)) {
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        $role = Role::Where(['slug' => 'student'])->get();
        $user->roles()->attach($role);
        $user->phone_number = $user->number;
        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;


        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'phone_number' => $user->phone_number,
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

            $role = Role::Where(['slug' => 'student'])->get();
            $user->roles()->sync($role);

        }
        $user->phone_number = $user->number;
        $avatar = $user->getFirstMediaUrl('avatars', 'thumb') ? url($user->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg') ;


        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'avatar' => $avatar
        ], 200);
    }
    public function destroy($id)
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
        if($user->hasRole('student')){
            $user->delete();

            return response()->json(['message' => 'User Deleted!'], 200);
        }
        return response()->json(['message' => 'Something went wrong!'], 404);
    }
}
