<?php

namespace App\Http\Controllers;

use App\Department;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-department')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view departments');
        }
        return view('departments.department');
    }


    public function getDepartments(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-department')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view departments');
            }
            $data = Department::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-department') || $user->can('delete-department')){
                $datatable->addColumn('action', function($row){
                    $department_id = $row->id;
                    $actionBtn = view('departments.control_buttons', compact('department_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-department')){
            return redirect('/department')->with('warning', 'You don\'t have permission to add department');
        }
        return view('departments.add');
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-department')){
            return redirect('/department')->with('warning', 'You don\'t have permission to add department');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments'],
        ]);
        $department = new Department([
            'name' => $request->name,
        ]);
        $department->save();

        return redirect('/department')->with('success', 'Department saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-department')){
            return redirect('/department')->with('warning', 'You don\'t have permission to delete department');
        }
        $department = Department::find($id);

        $department->delete();

        return redirect('/department')->with('success', 'department Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-department')){
            return redirect('/department')->with('warning', 'You don\'t have permission to edit department');
        }
        $department = Department::find($id);

        return view('departments.edit', compact('department'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-department')){
            return redirect('/department')->with('warning', 'You don\'t have permission to update department');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,'.$id],
        ]);

        $department = Department::find($id);

        $department->name =  $request->name;
        $department->save();

        return redirect('/department')->with('success', 'Department updated!');
    }
}
