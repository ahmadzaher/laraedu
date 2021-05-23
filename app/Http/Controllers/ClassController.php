<?php

namespace App\Http\Controllers;

use App\SchoolClass;
use App\SchoolSection;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-class')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view classes');
        }
        return view('classes.class');
    }


    public function getClasses(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-class')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view classes');
            }
            $data = SchoolClass::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-class') || $user->can('delete-class')){
                $datatable->addColumn('action', function($row){
                    $class_id = $row->id;
                    $actionBtn = view('classes.control_buttons', compact('class_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-class')){
            return redirect('/class')->with('warning', 'You don\'t have permission to add class');
        }

        $sections = SchoolSection::all();
        return view('classes.add', compact('sections'));
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-class')){
            return redirect('/class')->with('warning', 'You don\'t have permission to add class');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_classes'],
            'name_numeric' => ['required', 'string', 'max:255'],
        ]);
        $class = new SchoolClass([
            'name' => $request->name,
            'name_numeric' => $request->name_numeric,
        ]);
        $class->save();

        $sections = $request->sections;
        $class->sections()->attach($sections);

        return redirect('/class')->with('success', 'Class saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-class')){
            return redirect('/class')->with('warning', 'You don\'t have permission to delete class');
        }
        $class = SchoolClass::find($id);

        $class->delete();

        return redirect('/class')->with('success', 'Class Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-class')){
            return redirect('/class')->with('warning', 'You don\'t have permission to edit class');
        }
        $class = SchoolClass::with('sections')->find($id);

        $class_sections = [];
        foreach($class->sections as $section){
            $class_sections[] = $section->id;
        }
        $sections = SchoolSection::all();
        return view('classes.edit', compact('class', 'class_sections', 'sections'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-class')){
            return redirect('/class')->with('warning', 'You don\'t have permission to update class');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_classes,name,'.$id],
            'name_numeric' => ['required', 'string', 'max:255'],
        ]);

        $class = SchoolClass::find($id);

        $class->name =  $request->name;
        $class->name_numeric = $request->name_numeric;
        $class->save();


        $sections = $request->sections;
        $class->sections()->sync($sections);

        return redirect('/class')->with('success', 'Class updated!');
    }
}
