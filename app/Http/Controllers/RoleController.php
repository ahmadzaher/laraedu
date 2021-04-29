<?php

namespace App\Http\Controllers;

use App\Permission;
use App\User;
use Illuminate\Http\Request;
use App\Role;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-role')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view roles');
        }
        return view('roles.role');
    }


    public function getRoles(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-role')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view roles');
            }
            $data = Role::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-role') || $user->can('delete-role')){
                $datatable->addColumn('action', function($row){
                    $role_id = $row->id;
                    $actionBtn = view('roles.control_buttons', compact('role_id'));
                    if($row->slug == 'superadmin')
                        $actionBtn = '';
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-role')){
            return redirect('/role')->with('warning', 'You don\'t have permission to add role');
        }
        $user_info = User::with(['roles' => function($query){
            $query->with('permissions');
        }])->where('id', $user->id)->first();
        $roles = $user_info->roles;
        $permissions = [];
        foreach($roles as $role){
            foreach ($role->permissions as $permission){
                $perms = $permission->toArray();
                unset($perms['pivot']);
                $permissions[] = ($perms);

            }
        }
        $permissions = (array_unique($permissions, SORT_REGULAR));
        return view('roles.add', compact('permissions'));
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-role')){
            return redirect('/role')->with('warning', 'You don\'t have permission to add role');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles'],
        ]);
        $role = new Role([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);
        $role->save();
        $permissions = $request->permissions;
        $role->permissions()->sync($permissions);

        return redirect('/role')->with('success', 'Role saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-role')){
            return redirect('/role')->with('warning', 'You don\'t have permission to delete role');
        }
        $role = Role::find($id);

        if($role->slug == 'superadmin'){
            return redirect('/role')->with('warning', 'You can\'t edit or delete " Super Admin " role');
        }
        $role->delete();

        return redirect('/role')->with('success', 'Role Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-role')){
            return redirect('/role')->with('warning', 'You don\'t have permission to edit role');
        }
        $role = Role::with('permissions')->find($id);
        $role_permissions = [];
        foreach($role->permissions as $permission){
            $role_permissions[] = $permission->id;
        }
        $user_info = User::with(['roles' => function($query){
            $query->with('permissions');
        }])->where('id', $user->id)->first();
        $roles = $user_info->roles;
        $permissions = [];
        foreach($roles as $r){
            foreach ($r->permissions as $permission){
                $perms = $permission->toArray();
                unset($perms['pivot']);
                $permissions[] = ($perms);

            }
        }
        $permissions = (array_unique($permissions, SORT_REGULAR));

        return view('roles.edit', compact('role', 'permissions', 'role_permissions'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-role')){
            return redirect('/role')->with('warning', 'You don\'t have permission to update role');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$id],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug,'.$id],
        ]);

        $role = Role::find($id);
        if($role->slug == 'superadmin'){
            return redirect('/role')->with('warning', 'You can\'t edit or delete " Super Admin " role');
        }
        $role->name =  $request->name;
        $role->slug = $request->slug;
        $role->save();

        $permissions = $request->permissions;

        $role->permissions()->sync($permissions);

        return redirect('/role')->with('success', 'Role updated!');
    }

}
