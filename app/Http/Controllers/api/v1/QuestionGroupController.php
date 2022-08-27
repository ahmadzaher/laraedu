<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\QuestionGroup;
use Illuminate\Http\Request;

class QuestionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $year = $request->year;
        $search = $request->search;
            $groups = QuestionGroup::latest()->where(function ($query) use ($search){
                $query->where('title', 'like', '%'.$search.'%');
            })->where(function ($query) use ($branch_id, $subject_id, $year) {

                if($branch_id != ''){
                    $query->where('question_groups.branch_id', $branch_id);
                    $query->where('question_groups.subject_id', $subject_id);
                    $query->where('question_groups.year', $year);
                }

            })->paginate($request->per_page);
        return response($groups, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $year = $request->year;
        $sections = QuestionGroup::latest()->where(function ($query) use ($branch_id, $subject_id, $year) {

            if($branch_id != ''){
                $query->where('question_groups.branch_id', $branch_id);
                $query->where('question_groups.subject_id', $subject_id);
                $query->where('question_groups.year', $year);
            }

        })->get();
        return response($sections, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:question_groups']
//            'branch_id' => ['required', 'integer'],
//            'subject_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        $question_group = new QuestionGroup([
            'title' => $request->title,
            'branch_id' => $request->branch_id,
            'subject_id' => $request->subject_id,
            'year' => $request->year,

        ]);
        $question_group->save();
        return response($question_group, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuestionGroup  $questionGroup
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionGroup $questionGroup)
    {
        return Response($questionGroup, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuestionGroup  $questionGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuestionGroup $questionGroup)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:question_groups,title,'.$questionGroup->id],
//            'branch_id' => ['required', 'integer'],
//            'subject_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        if($questionGroup == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $questionGroup->title = $request->title;
        $questionGroup->branch_id = $request->branch_id;
        $questionGroup->subject_id = $request->subject_id;
        $questionGroup->year = $request->year;
        $questionGroup->save();

        return response($questionGroup, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuestionGroup  $questionGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionGroup $questionGroup)
    {
        $questionGroup->delete();
        return response(['msg' => 'Deleted Successfully!'], 200);
    }
}
