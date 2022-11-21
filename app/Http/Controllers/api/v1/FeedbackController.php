<?php

namespace App\Http\Controllers\api\v1;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $feedback = Feedback::latest()
            ->leftJoin('users', 'users.id', '=', 'feedback.user_id')
            ->leftJoin('branches', 'branches.id', '=', 'users.branch_id')
            ->where(function ($query) use ($search){
            $query->where('text', 'like', '%'.$search.'%');
        })
            ->select('feedback.*', 'users.name as name', 'branches.name as branch')
            ->paginate($request->per_page);
        return response($feedback, 200);
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
            'text' => ['required']
        ]);
        $feedback = new Feedback([
            'text' => $request->text,
            'user_id' => $request->user()->id
        ]);
        $feedback->save();
        $users = User::whereHas("roles", function($q){ $q->where("slug", "superadmin"); })->get();

        foreach ($users as $user)
        {
            $user->sendFeedbackNotification($request->text);
        }
        return response($feedback, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        return Response($feedback, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'text' => ['required']
        ]);
        if($feedback == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $feedback->text = $request->text;

        $feedback->save();

        return response($feedback, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        if(!auth()->user()->hasRole('superadmin'))
            abort(403);
        $feedback->delete();
        return response(['msg' => 'Deleted Successfully!'], 200);
    }
}
