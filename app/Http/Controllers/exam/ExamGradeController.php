<?php

namespace App\Http\Controllers\exam;

use App\Http\Controllers\Controller;
use App\exam\ExamGrade;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExamGradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if(!$request->user()->can('view-exam-grade')) {
            return redirect('/home')->with('warning', 'You don\'t have permission to view Exam Grade');
        }
        return view('exam_grades.exam_grade');
    }


    public function getExamGrades(Request $request)
    {
        if ($request->ajax()) {
            $user = $request->user();
            if(!$user->can('view-exam-grade')) {
                return redirect('/home')->with('warning', 'You don\'t have permission to view Exam Grade');
            }
            $data = ExamGrade::latest()->get();
            $datatable =  Datatables::of($data)
                ->addIndexColumn();
            if($user->can('edit-exam-grade') || $user->can('delete-exam-grade')){
                $datatable->addColumn('action', function($row){
                    $exam_grade_id = $row->id;
                    $actionBtn = view('exam_grades.control_buttons', compact('exam_grade_id'));
                    return $actionBtn;
                })
                    ->rawColumns(['action']);
            }
            return $datatable->make(true);
        }
    }

    public function add(Request $request)
    {
        $user = $request->user();
        if(!$user->can('create-exam-grade')){
            return redirect('/exam_grade')->with('warning', 'You don\'t have permission to add Exam Grade');
        }
        return view('exam_grades.add');
    }

    public function store(Request $request){
        $user = $request->user();
        if(!$user->can('create-exam-grade')){
            return redirect('/exam_grade')->with('warning', 'You don\'t have permission to add Exam Grade');
        }


        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'point' => ['required', 'int'],
            'mark_from' => ['required', 'int'],
            'mark_upto' => ['required', 'int'],
        ]);
        $exam_grade = new ExamGrade([
            'name' => $request->name,
            'point' => $request->point,
            'mark_from' => $request->mark_from,
            'mark_upto' => $request->mark_upto,
            'note' => $request->note,
        ]);
        $exam_grade->save();

        return redirect('/exam_grade')->with('success', 'Exam Grade saved!');

    }
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('delete-exam-grade')){
            return redirect('/exam_grade')->with('warning', 'You don\'t have permission to delete Exam Grade');
        }
        $exam_grade = ExamGrade::find($id);

        $exam_grade->delete();

        return redirect('/exam_grade')->with('success', 'Exam Grade Deleted!');
    }
    public function edit(Request $request, $id)
    {

        $user = $request->user();
        if(!$user->can('edit-exam-grade')){
            return redirect('/exam_grade')->with('warning', 'You don\'t have permission to edit Exam Grade');
        }
        $exam_grade = ExamGrade::find($id);

        return view('exam_grades.edit', compact('exam_grade'));
    }
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if(!$user->can('edit-exam-grade')){
            return redirect('/exam_grade')->with('warning', 'You don\'t have permission to update Exam Grade');
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'point' => ['required', 'int'],
            'mark_from' => ['required', 'int'],
            'mark_upto' => ['required', 'int'],
        ]);

        $exam_grade = ExamGrade::find($id);

        $exam_grade->name =  $request->name;
        $exam_grade->point =  $request->point;
        $exam_grade->mark_from =  $request->mark_from;
        $exam_grade->mark_upto =  $request->mark_upto;
        $exam_grade->note =  $request->note;
        $exam_grade->save();

        return redirect('/exam_grade')->with('success', 'Exam Grade updated!');
    }
}
