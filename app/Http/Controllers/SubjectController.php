<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-subject')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view subjects');
        }
        return view('subjects.subject');
    }


    public function getSubjects(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-subject')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view subjects');
            }
            $data = Subject::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-subject') || $user->can('delete-subject')){
                $datatable->addColumn('action', function($row){
                    $subject_id = $row->id;
                    $actionBtn = view('subjects.control_buttons', compact('subject_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-subject')){
            return redirect('/subject')->with('warning', 'You don\'t have permission to add subject');
        }
        return view('subjects.add');
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-subject')){
            return redirect('/subject')->with('warning', 'You don\'t have permission to add subject');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects'],
            'code' => ['required', 'string', 'max:255', 'unique:subjects'],
            'author' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
        ]);
        $subject = new Subject([
            'name' => $request->name,
            'code' => $request->code,
            'author' => $request->author,
            'type' => $request->type,
        ]);
        $subject->save();

        return redirect('/subject')->with('success', 'subject saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-subject')){
            return redirect('/subject')->with('warning', 'You don\'t have permission to delete subject');
        }
        $subject = Subject::find($id);

        $subject->delete();

        return redirect('/subject')->with('success', 'subject Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-subject')){
            return redirect('/subject')->with('warning', 'You don\'t have permission to edit subject');
        }
        $subject = Subject::find($id);

        return view('subjects.edit', compact('subject'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-subject')){
            return redirect('/subject')->with('warning', 'You don\'t have permission to update subject');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name,'.$id],
            'code' => ['required', 'string', 'max:255', 'unique:subjects,name,'.$id],
            'author' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
        ]);

        $subject = Subject::find($id);

        $subject->name =  $request->name;
        $subject->code =  $request->code;
        $subject->author =  $request->author;
        $subject->type = $request->type;
        $subject->save();

        return redirect('/subject')->with('success', 'subject updated!');
    }
}
