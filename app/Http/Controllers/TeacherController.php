<?php

namespace App\Http\Controllers;

use App\Department;
use App\Role;
use App\Rules\Nospaces;
use App\SchoolClass;
use App\TeacherAllocation;
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
                    'departments.name as department_name'
                )->get();


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
                $avatar = url($row->getFirstMediaUrl('avatars', 'thumb')) != null ? url($row->getFirstMediaUrl('avatars', 'thumb')) : url('/images/avatar.jpg');
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

    public function teacher_allocation(Request $request)
    {
        if (!$request->user()->can('view-teacher')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view users');
        }
        return view('teachers.allocations');
    }

    public function getAllocations(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if (!$user->can('view-teacher')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view users');
            }
            $data = TeacherAllocation::latest('teacher_allocations.created_at')
                ->leftJoin('users', 'teacher_allocations.teacher_id', '=', 'users.id')
                ->leftJoin('school_classes', 'teacher_allocations.class_id', '=', 'school_classes.id')
                ->leftJoin('school_sections', 'teacher_allocations.section_id', '=', 'school_sections.id')
                ->leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
                ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
                ->where('roles.slug', '=', 'teacher')
                ->select(
                    //'teacher_allocations.*',
                    'users.name',
                    'school_classes.name as class_name',
                    'school_sections.name as section_name',
                    'roles.slug as role',
                    'departments.name as department_name',
                    'teacher_allocations.id as id'
                )
                ->get();


            $datatable = Datatables::of($data)
                ->addIndexColumn();

            if ($user->can('edit-teacher') || $user->can('delete-teacher')) {
                $datatable->addColumn('action', function ($row) {
                    $id = $row->id;
                    $actionBtn = view('teachers.allocation_control_buttons', compact('id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }
    public function add_allocation(Request $request)
    {
        $user = $request->user();
        if (!$user->can('create-teacher')) {
            return redirect('/teacher_allocation')->with('warning', 'You don\'t have permission to add allocation');
        }
        $teachers = User::leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->where('roles.slug', '=', 'teacher')
            ->select('users.id as id', 'users.name')
            ->get();
        $classes = SchoolClass::all();

        return view('teachers.add_allocation', compact('teachers', 'classes'));
    }

    public function store_allocation(Request $request)
    {
        $user = $request->user();
        if (!$user->can('create-teacher')) {
            return redirect('/teacher_allocation')->with('warning', 'You don\'t have permission to add allocation');
        }
        if (TeacherAllocation::where('class_id', '=', $request->class)
            ->where('section_id', '=', $request->section)
            ->where('teacher_id', '=', $request->class_teacher)
            ->exists()) {
            return redirect('/allocation/add')->with('warning', 'Class Teachers Are Already Allocated For This Class');
        }
        if (TeacherAllocation::where('class_id', '=', $request->class)
            ->where('section_id', '=', $request->section)
            ->exists()) {
            return redirect('/allocation/add')->with('warning', 'This Class Teacher Already Assigned');
        }

        $request->validate([
            'class_teacher' => ['required'],
            'class' => ['required'],
            'section' => ['required'],
        ]);
        $allocation = new TeacherAllocation([
            'teacher_id' => $request->class_teacher,
            'class_id' => $request->class,
            'section_id' => $request->section,

        ]);

        $allocation->save();


        return redirect('/teacher_allocation')->with('success', 'Allocated Successfully');

    }

    public function destroy_allocation(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->can('delete-teacher')) {
            return redirect('/teacher_allocation')->with('warning', 'You don\'t have permission to delete allocation');
        }
        $allocation = TeacherAllocation::find($id);

        $allocation->delete();

        return redirect('/teacher_allocation')->with('success', 'Deleted Successfully!');
    }

    public function edit_allocation(Request $request, $id)
    {
        $allocation = TeacherAllocation::with('Teacher')->with('SchoolClass')->with('SchoolSection')->find($id);


        $teachers = User::leftJoin('users_roles', 'users.id', '=', 'users_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'users_roles.role_id')
            ->where('roles.slug', '=', 'teacher')
            ->select('users.id as id', 'users.name')
            ->get();
        $classes = SchoolClass::all();
        $class_id = $allocation->class_id;

        $class = SchoolClass::with('sections')->find($class_id);

        $sections = [];
        if(isset($class->sections))
        foreach($class->sections as $section){
            $sections[] = [
                'id' => $section->id,
                'name' => $section->name
            ];
        }
        $section_id = $allocation->section_id;
        $teacher_id = $allocation->teacher_id;

        if (!$request->user()->can('edit-teacher')) {
            return redirect('/teacher_allocation')->with('warning', 'You don\'t have permission to edit user');
        }

        return view('teachers.edit_allocation', compact('allocation', 'teachers', 'classes', 'class_id', 'sections', 'section_id', 'teacher_id'));
    }

    public function update_allocation(Request $request, $id)
    {
        $request->validate([
            'class_teacher' => ['required'],
            'class' => ['required'],
            'section' => ['required'],
        ]);
        $allocation = new TeacherAllocation([
            'teacher_id' => $request->class_teacher,
            'class_id' => $request->class,
            'section_id' => $request->section,

        ]);
        $allocation = TeacherAllocation::find($id);

        if (!$request->user()->can('edit-teacher')) {
            return redirect('/teacher_allocation')->with('warning', 'You don\'t have permission to update user');
        }
        $allocation->teacher_id = $request->class_teacher;
        $allocation->class_id = $request->class;
        $allocation->section_id = $request->section;
        $allocation->save();
        return redirect('/teacher_allocation')->with('success', 'Allocation updated!');
    }
}
