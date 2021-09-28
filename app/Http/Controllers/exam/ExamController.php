<?php

namespace App\Http\Controllers\exam;

use App\Http\Controllers\Controller;
use App\exam\Exam;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-exam')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view exams');
        }
        return view('exams.exam');
    }


    public function getExams(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-exam')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view exams');
            }
            $data = Exam::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-exam') || $user->can('delete-exam')){
                $datatable->addColumn('action', function($row){
                    $exam_id = $row->id;
                    $actionBtn = view('exams.control_buttons', compact('exam_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request){
        $user = $request->user();
        if(!$user->can('create-exam')){
            return redirect('/exam')->with('warning', 'You don\'t have permission to add exam');
        }
        return view('exams.add');
    }
    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-exam')){
            return redirect('/exam')->with('warning', 'You don\'t have permission to add exam');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
        ]);
        $exam = new Exam([
            'name' => $request->name,
            'date' => $request->date,
            'note' => $request->note,
        ]);
        $exam->save();

        return redirect('/exam')->with('success', 'exam saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-exam')){
            return redirect('/exam')->with('warning', 'You don\'t have permission to delete exam');
        }
        $exam = Exam::find($id);

        $exam->delete();

        return redirect('/exam')->with('success', 'exam Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-exam')){
            return redirect('/exam')->with('warning', 'You don\'t have permission to edit exam');
        }
        $exam = Exam::find($id);

        return view('exams.edit', compact('exam'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-exam')){
            return redirect('/exam')->with('warning', 'You don\'t have permission to update exam');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
        ]);

        $exam = Exam::find($id);

        $exam->name =  $request->name;
        $exam->date =  $request->date;
        $exam->note =  $request->note;
        $exam->save();

        return redirect('/exam')->with('success', 'exam updated!');
    }
}
