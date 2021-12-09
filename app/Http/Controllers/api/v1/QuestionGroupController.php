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
    public function index()
    {
        $sections = QuestionGroup::latest()->get();
        return response($sections, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {

        $search = $request->search;
        if($search != '')
        {
            $groups = QuestionGroup::latest()->where(function ($query) use ($search){
                $query->where('title', 'like', '%'.$search.'%');
            })->paginate($request->per_page);
//            $questions = Question::with('answers')
//                ->leftJoin('question_groups', 'question_groups.id', '=', 'questions.group_id')
//                ->latest()->where(function ($query) use ($search) {
//                    $query->where('content', 'like', '%'.$search.'%')
//                        ->orWhere('questions.id', 'like', '%'.$search.'%')
//                        ->orWhere('type', 'like', '%'.$search.'%');
//                })->select(['questions.*', 'question_groups.title as group_name'])->paginate($request->per_page);
        }else
            $groups = QuestionGroup::latest()->paginate($request->per_page);
        return response($groups, 200);
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
    ]);
        $question_group = new QuestionGroup([
            'title' => $request->title

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
        ]);
        if($questionGroup == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $questionGroup->title = $request->title;
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
