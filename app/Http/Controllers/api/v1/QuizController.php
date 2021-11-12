<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Quiz;
use App\QuizMeta;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quizzes = Quiz::latest()->paginate(10);;
        return response($quizzes, 200);
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
            'title' => ['required', 'string', 'max:255', 'unique:quizzes'],
            'meta_title' => ['required', 'string', 'max:255', 'unique:quizzes'],
            'slug' => ['required', 'string', 'max:255', 'unique:quizzes'],
            'type' => ['required', 'string', 'max:255'],
            'score' => ['required', 'string', 'max:255'],
            'published' => ['required', 'string', 'max:255'],
        ]);
        $quiz = new Quiz([
            'title' => $request->title,
            'meta_title' => $request->meta_title,
            'slug' => $request->slug,
            'summary' => $request->summary,
            'type' => $request->type,
            'score' => $request->score,
            'published' => $request->published,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'content' => $request->quiz_content,

        ]);
        $quiz->save();
        foreach ($request->meta as $key => $content)
        {
            $quiz_meta = new QuizMeta([
                'quiz_id' => $quiz->id,
                'key' => $key,
                'content' => $content
            ]);
            $quiz_meta->save();
        }
        $quiz->quiz_metas = QuizMeta::where('quiz_id', '=', $quiz->id)->get();

        return response($quiz, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        $quiz = Quiz::with('quiz_metas')->find($quiz->id);
        return Response($quiz, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:quizzes,title,'.$quiz->id],
            'meta_title' => ['required', 'string', 'max:255', 'unique:quizzes,meta_title,'.$quiz->id],
            'slug' => ['required', 'string', 'max:255', 'unique:quizzes,slug,'.$quiz->id],
            'type' => ['required', 'string', 'max:255'],
            'score' => ['required', 'string', 'max:255'],
            'published' => ['required', 'string', 'max:255'],
        ]);
        if($quiz == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $quiz->title = $request->title;
        $quiz->meta_title = $request->meta_title;
        $quiz->slug = $request->slug;
        $quiz->summary = $request->summary;
        $quiz->type = $request->type;
        $quiz->score = $request->score;
        $quiz->published = $request->published;
        $quiz->starts_at = $request->starts_at;
        $quiz->ends_at = $request->ends_at;
        $quiz->content = $request->quiz_content;
        $quiz->save();

        return response($quiz, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return response(['message' => 'Quiz Deleted Successfully!'], 200);
    }
}
