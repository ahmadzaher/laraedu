<?php

namespace App\Http\Controllers\api\v1;

use App\Answer;
use App\Http\Controllers\Controller;
use App\Quiz;
use App\QuizMeta;
use App\Question;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        if($search != '')
        {
            $quizzes = Quiz::with('questions')
                ->leftJoin('categories', 'categories.id', '=', 'quizzes.category_id')
                ->latest()->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('quizzes.id', 'like', '%'.$search.'%');
            })->select(['quizzes.*', 'categories.name as category_name'])->paginate($request->per_page);
        }else
            $quizzes = Quiz::with('questions')->leftJoin('categories', 'categories.id', '=', 'quizzes.category_id')
                ->latest()
                ->select(['quizzes.*', 'categories.name as category_name'])
                ->paginate($request->per_page);
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
            'type' => ['required', 'max:255'],
            'score' => ['required', 'max:255'],
            'published' => ['required', 'max:255'],
            'category' => ['required', 'integer'],
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
            'category_id' => $request->category,

        ]);
        $quiz->save();
        if(!empty($request->meta))
        foreach ($request->meta as $key => $content)
        {
            $quiz_meta = new QuizMeta([
                'quiz_id' => $quiz->id,
                'key' => $key,
                'content' => $content
            ]);
            $quiz_meta->save();
        }
        $questions = (array_unique($request->questions, SORT_REGULAR));

        $quiz->questions()->sync($questions);
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
        $questions = Question::with('answers')->whereHas('quizzes', function ($query) use ($quiz) {
            return $query->where('id', '=', $quiz->id);
        })->get();
        $quiz->questions = $questions;
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
            'type' => ['required', 'max:255'],
            'score' => ['required', 'max:255'],
            'published' => ['required', 'max:255'],
            'category' => ['required', 'integer'],
            'questions' => ['required']
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
        $quiz->category_id = $request->category;
        $quiz->save();

        $questions = (array_unique($request->questions, SORT_REGULAR));

        $quiz->questions()->sync($questions);

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $quiz
     * @return \Illuminate\Http\Response
     */
    public function questions_store(Request $request, $quiz)
    {
        foreach ($request->questions as $question)
        {
            if( !isset($question['type'], $question['active'], $question['level'], $question['score'], $question['content']) )
                return Response(['msg' => 'The given data was invalid!'], 422);
            if($question['type'] == 'multiple'){
                if ( !isset($question['answers']) )
                    return Response(['msg' => 'The given data was invalid!'], 422);
                foreach ($question['answers'] as $answer)
                {
                    if( !isset($answer['active'], $answer['correct'], $answer['content']) )
                        return Response(['msg' => 'The given data was invalid!'], 422);
                }
            }
        }
        Question::where('quiz_id', '=', $quiz)->delete();

        foreach ($request->questions as $question)
        {
            if($question['type'] == 'true/false')
            {
                $correct = $question['correct'];
                $question = new Question([
                    'quiz_id' => $quiz,
                    'type' => $question['type'],
                    'active' => $question['active'],
                    'level' => $question['level'],
                    'score' => $question['score'],
                    'content' => $question['content']
                ]);
                $question->save();
                $answer = new Answer([
                    'quiz_id' => $quiz,
                    'question_id' => $question['id'],
                    'active' => $answer['active'],
                    'correct' => $correct,
                    'content' => $answer['content']
                ]);
                $answer->save();
            }
            if($question['type'] == 'multiple')
            {
                $answers = $question['answers'];
                $question = new Question([
                    'quiz_id' => $quiz,
                    'type' => $question['type'],
                    'active' => $question['active'],
                    'level' => $question['level'],
                    'score' => $question['score'],
                    'content' => $question['content']
                ]);
                $question->save();

                foreach ($answers as $answer)
                {
                    $answer = new Answer([
                        'quiz_id' => $quiz,
                        'question_id' => $question['id'],
                        'active' => $answer['active'],
                        'correct' => $answer['correct'],
                        'content' => $answer['content']
                    ]);
                    $answer->save();
                }

            }
        }
        $quiz = Question::with('answers')
            ->where('quiz_id', '=', $quiz)
            ->get();
        return Response($quiz, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $quiz
     * @return \Illuminate\Http\Response
     */
    public function questions_show($quiz)
    {
        $quiz = Question::with('answers')
            ->where('quiz_id', '=', $quiz)
            ->get();

        return Response($quiz, 200);
    }
}
