<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branch_id = $request->branch_id;
        $year = $request->year;
        $search = $request->search;
        $subjects = Subject::latest()->where(function ($query) use ($search){
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%');
        })->where(function ($query) use ($branch_id, $year) {

            if($branch_id != ''){
                $query->where('subjects.branch_id', $branch_id);
                $query->where('subjects.year', $year);
            }

        })->paginate($request->per_page);
        return response($subjects, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $branch_id = $request->branch_id;
        $year = $request->year;
        $subjects = Subject::latest()
            ->withCount('quizzes')
            ->withCount('summaries')
            ->where(function ($query) use ($branch_id, $year) {

                if($branch_id != ''){
                    $query->where('subjects.branch_id', $branch_id);
                    $query->where('subjects.year', $year);
                }

            })
            ->get();
        return response($subjects, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!auth()->user()->hasRole('superadmin'))
            abort(403);
        $request->validate([
            'name' => ['required', 'string', 'max:255']
//            'branch_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        $subject = new Subject([
            'name' => $request->name,
            'description' => $request->description,
            'branch_id' => $request->branch_id,
            'year' => $request->year,

        ]);
        $subject->save();
        return response($subject, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Subject $subject)
    {
        return Response($subject, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        if(!auth()->user()->hasRole('superadmin'))
            abort(403);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
//            'branch_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        if($subject == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $subject->name = $request->name;
        $subject->description = $request->description;
        $subject->branch_id = $request->branch_id;
        $subject->year = $request->year;
        $subject->save();

        return response($subject, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        if(!auth()->user()->hasRole('superadmin'))
            abort(403);
        $subject->delete();
        return response(['msg' => 'Deleted Successfully!'], 200);
    }
}
