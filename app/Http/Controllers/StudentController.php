<?php

namespace App\Http\Controllers;

use App\Role;
use App\Rules\Nospaces;
use App\SchoolClass;
use App\SchoolSection;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        if(!$request->user()->can('view-student')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view users');
        }
        return view('students.user');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-student')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view users');
            }
            $data = User::latest()
                ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
                ->leftJoin('school_classes', 'school_classes.id', '=', 'users.class_id')
                ->leftJoin('school_sections', 'school_sections.id', '=', 'users.section_id')
                ->where('roles.slug', '=', 'student')
                ->select(
                    'users.*',
                    'roles.slug as role',
                    'school_classes.name as class_name',
                    'school_sections.name as section_name'
                )
                ->get();


            $datatable =  Datatables::of($data)
                ->addIndexColumn();

            if($user->can('edit-student') || $user->can('delete-student')){
                $datatable->addColumn('action', function($row){
                    $user_id = $row->id;
                    $actionBtn = view('students.control_buttons', compact('user_id'));
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
        if(!$user->can('create-student')){
            return redirect('/student')->with('warning', 'You don\'t have permission to add user');
        }
        $roles = Role::all();
        $sections = SchoolSection::all();
        $classes = SchoolClass::all();
        return view('students.add', compact('roles', 'sections', 'classes'));
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-student')){
            return redirect('/student')->with('warning', 'You don\'t have permission to add user');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8', new Nospaces],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'section' => ['required'],
            'class' => ['required'],
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

        // Assign permissions to user
//        if(is_array($roles))
//        foreach($roles as $role){
//            $role = Role::find($role);
//            $permission = $role->permissions()->get();
//            $user->permissions()->detach($permission);
//            $user->permissions()->attach($permission);
//        }

        return redirect('/student')->with('success', 'User saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-student')){
            return redirect('/student')->with('warning', 'You don\'t have permission to delete user');
        }
        $user = User::find($id);

        if($id == Auth::id()){
            return redirect('/student')->with('warning', 'You can\'t delete your profile');
        }
        if($user->hasRole('superadmin')){
            return redirect('/student')->with('warning', 'You can\'t delete superadmin user');
        }
        $user->delete();

        return redirect('/student')->with('success', 'User Deleted!');
    }
    public function edit(Request $request, $id)
    {
        $user = User::with('roles')->with('SchoolClass')->with('SchoolSection')->find($id);

        $classes = SchoolClass::all();
        $class_id = $user->class_id;
        $section_id = $user->section_id;
        $class = SchoolClass::with('sections')->find($class_id);

        $sections = [];
        if(isset($class->sections))
        foreach($class->sections as $section){
            $sections[] = [
                'id' => $section->id,
                'name' => $section->name
            ];
        }

        if(!$request->user()->can('edit-student')){
            return redirect('/student')->with('warning', 'You don\'t have permission to edit user');
        }
        $user_roles = [];
        foreach($user->roles as $role){
            if($role->slug == 'teacher'){
                return redirect('/student')->with('warning', 'Something went Wrong');
            }
            $user_roles[] = $role->id;
        }
        $roles = Role::all();

        return view('students.edit', compact('user', 'roles', 'user_roles', 'class_id', 'section_id', 'classes', 'sections'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'section' => ['required'],
            'class' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$id, 'min:8', new Nospaces],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'password' => ['string', 'min:8', 'confirmed', 'nullable'],
        ]);
        $user = User::find($id);

        if(!$request->user()->can('edit-student')){
            return redirect('/student')->with('warning', 'You don\'t have permission to update user');
        }
        $user->name =  $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->schoolClass()->associate($request->class);
        $user->schoolSection()->associate($request->section);
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


        return redirect('/student')->with('success', 'User updated!');
    }
}
