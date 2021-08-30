<?php

namespace App\Http\Controllers;

use App\Department;
use App\Role;
use App\Rules\Nospaces;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class TeacherController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        if (!$request->user()->can('view-teacher')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view users');
        }
        return view('teachers.user');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if (!$user->can('view-teacher')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view users');
            }
            $data = User::latest()
                ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
                ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
                ->where('roles.slug', '=', 'teacher')
                ->select(
                    'users.*',
                    'roles.slug as role',
                    'departments.name as department_name',
                )
                ->get();


            $datatable = Datatables::of($data)
                ->addIndexColumn();

            if ($user->can('edit-teacher') || $user->can('delete-teacher')) {
                $datatable->addColumn('action', function ($row) {
                    $user_id = $row->id;
                    $actionBtn = view('teachers.control_buttons', compact('user_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            $datatable->addColumn('avatar', function ($row) {
                $avatar = $row->getFirstMediaUrl('avatars', 'thumb') != null ? $row->getFirstMediaUrl('avatars', 'thumb') : url('/images/avatar.jpg');
                return '<img src="' . $avatar . '" alt="avatar" class="avatar rounded img-responsive mr-1" />';
            })->rawColumns(['avatar']);
            return $datatable->make(true);
        }
    }

    public function add(Request $request)
    {
        $user = $request->user();
        if (!$user->can('create-teacher')) {
            return redirect('/teacher')->with('warning', 'You don\'t have permission to add user');
        }
        $roles = Role::all();
        $departments = Department::all();
        return view('teachers.add', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user->can('create-teacher')) {
            return redirect('/teacher')->with('warning', 'You don\'t have permission to add user');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'min:8', new Nospaces],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'department' => ['required'],
        ]);
        $user = new User([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'number' => $request->number,

        ]);
        $user->departments()->associate($request->department);
        $user->save();
        if (isset($request->avatar)) {
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        $role = Role::Where(['slug' => 'teacher'])->get();
        $user->roles()->attach($role);

        // Assign permissions to user
//        if(is_array($roles))
//        foreach($roles as $role){
//            $role = Role::find($role);
//            $permission = $role->permissions()->get();
//            $user->permissions()->detach($permission);
//            $user->permissions()->attach($permission);
//        }

        return redirect('/teacher')->with('success', 'User saved!');

    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->can('delete-teacher')) {
            return redirect('/teacher')->with('warning', 'You don\'t have permission to delete user');
        }
        $user = User::find($id);

        if ($id == Auth::id()) {
            return redirect('/teacher')->with('warning', 'You can\'t delete your profile');
        }
        if ($user->hasRole('superadmin')) {
            return redirect('/teacher')->with('warning', 'You can\'t delete superadmin user');
        }
        $user->delete();

        return redirect('/teacher')->with('success', 'User Deleted!');
    }

    public function edit(Request $request, $id)
    {
        $user = User::with('roles')->with('SchoolClass')->with('departments')->find($id);

        $departments = Department::all();
        $department_id = $user->department_id;

        if (!$request->user()->can('edit-teacher')) {
            return redirect('/teacher')->with('warning', 'You don\'t have permission to edit user');
        }
        $user_roles = [];
        foreach ($user->roles as $role) {
            if ($role->slug == 'student') {
                return redirect('/teacher')->with('warning', 'Something went Wrong');
            }
            $user_roles[] = $role->id;
        }
        $roles = Role::all();

        return view('teachers.edit', compact('user', 'departments', 'user_roles', 'department_id', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $id, 'min:8', new Nospaces],
            'avatar' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'password' => ['string', 'min:8', 'confirmed', 'nullable'],
        ]);
        $user = User::find($id);

        if (!$request->user()->can('edit-teacher')) {
            return redirect('/teacher')->with('warning', 'You don\'t have permission to update user');
        }
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->departments()->associate($request->department);
        if (isset($request->password))
            $user->password = Hash::make($request->password);
        $user->save();
        if (isset($request->avatar)) {
            $user->clearMediaCollection('avatars');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        }

        if ($id != $request->user()->id) {

            $role = Role::Where(['slug' => 'teacher'])->get();
            $user->roles()->sync($role);

            return redirect('/teacher')->with('success', 'User updated!');
        }
    }
}
