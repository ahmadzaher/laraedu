<?php

namespace App\Http\Controllers;

use App\SchoolSection;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-section')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view sections');
        }
        return view('sections.section');
    }


    public function getSections(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-section')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view sections');
            }
            $data = SchoolSection::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-section') || $user->can('delete-section')){
                $datatable->addColumn('action', function($row){
                    $section_id = $row->id;
                    $actionBtn = view('sections.control_buttons', compact('section_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-section')){
            return redirect('/section')->with('warning', 'You don\'t have permission to add section');
        }
        return view('sections.add');
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-section')){
            return redirect('/section')->with('warning', 'You don\'t have permission to add section');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_sections'],
            'capacity' => ['required', 'string', 'max:255'],
        ]);
        $section = new SchoolSection([
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);
        $section->save();

        return redirect('/section')->with('success', 'Section saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-section')){
            return redirect('/section')->with('warning', 'You don\'t have permission to delete section');
        }
        $section = SchoolSection::find($id);

        $section->delete();

        return redirect('/section')->with('success', 'Section Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-section')){
            return redirect('/section')->with('warning', 'You don\'t have permission to edit section');
        }
        $section = SchoolSection::find($id);

        return view('sections.edit', compact('section'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-section')){
            return redirect('/section')->with('warning', 'You don\'t have permission to update section');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:school_sections,name,'.$id],
            'capacity' => ['required', 'string', 'max:255'],
        ]);

        $section = SchoolSection::find($id);

        $section->name =  $request->name;
        $section->capacity = $request->capacity;
        $section->save();

        return redirect('/section')->with('success', 'Section updated!');
    }
}
