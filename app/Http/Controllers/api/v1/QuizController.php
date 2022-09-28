<?php

namespace App\Http\Controllers\api\v1;

use App\Answer;
use App\Http\Controllers\Controller;
use App\Quiz;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
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
        $seller_id = $request->seller_id;
        $year = $request->year;
        $search = $request->search;
            $quizzes = Quiz::leftJoin('categories', 'categories.id', '=', 'quizzes.category_id')
                ->latest()
                ->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('quizzes.id', 'like', '%'.$search.'%');
            })->where(function ($query) use ($branch_id, $subject_id, $year, $seller_id) {

                    if($branch_id != ''){
                        $query->where('quizzes.branch_id', $branch_id);
                        $query->where('quizzes.subject_id', $subject_id);
                        $query->where('quizzes.seller_id', $seller_id);
                        $query->where('quizzes.year', $year);
                    }

                })->select(['quizzes.*', 'categories.name as category_name'])->paginate($request->per_page);
        return response($quizzes, 200);
    }


    public function all(Request $request)
    {
        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $year = $request->year;
        $quizzes = Quiz::where('published', 1)
            ->select(['quizzes.*', DB::raw("(SELECT COUNT('id') FROM `transactions` WHERE transactions.quiz_id = quizzes.id AND transactions.user_id = ".$request->user()->id.") as is_purchased ")])
            ->where(function ($query) use ($branch_id, $subject_id, $year) {

                if($branch_id != ''){
                    $query->where('quizzes.branch_id', $branch_id);
                    $query->where('quizzes.subject_id', $subject_id);
                    $query->where('quizzes.year', $year);
                }

            })
            ->latest('quizzes.created_at')
            ->get();
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
            'type' => ['required', 'integer', 'min:1', 'max:3'],
            'score' => ['max:255'],
            'published' => ['required', 'max:255'],
            'category' => ['integer'],
            'price' => ['required|integer'],
            'percentage' => ['required', 'integer']
        ]);
        $quiz = new Quiz([
            'title' => $request->title,
            'summary' => $request->summary,
            'type' => $request->type,
            'score' => $request->score,
            'published' => $request->published,
            'content' => $request['content'],
            'category_id' => $request->category ? $request->category : null,
            'branch_id' => $request->branch_id,
            'subject_id' => $request->subject_id,
            'seller_id' => $request->seller_id,
            'year' => $request->year,
            'price' => $request->price,
            'percentage' => $request->percentage

        ]);
        $quiz->save();
        $questions = (array_unique($request->questions, SORT_REGULAR));

        $quiz->questions()->sync($questions);

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
        $questions = Question::with('answers')->where('active', 1)->with('group')->whereHas('quizzes', function ($query) use ($quiz) {
            return $query->where('id', '=', $quiz->id);
        })->get();
        foreach ($questions as $key => $question) {
            $questions[$key]->group_name = is_object($question->group) ? $question->group->title : '';
        }
        $quiz->questions = $questions;
        return Response($quiz, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function student_show(Quiz $quiz, Request $request)
    {
        if(!$quiz->purchasedBy($request->user()))
            return response(['message' => 'Not Purchased'], 403);
        $questions = Question::where('active', 1)
        ->with('answers')->with('group')->whereHas('quizzes', function ($query) use ($quiz) {
            return $query->where('id', '=', $quiz->id);
        })->get();
        foreach ($questions as $key => $question) {
            $questions[$key]->group_name = $question->group->title;

            if ( $question->getFirstMediaUrl('question_images', 'question_image') )
            {
                $questions[$key]->question_image = url($question->getFirstMediaUrl('question_images', 'question_image'));
            }
        }
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
            'type' => ['required', 'integer', 'min:1', 'max:3'],
            'score' => ['max:255'],
            'published' => ['required', 'max:255'],
            'category' => ['integer'],
            'questions' => ['required'],
            'price' => ['required', 'integer']
        ]);
        if($quiz == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $quiz->title = $request->title;
        $quiz->summary = $request->summary;
        $quiz->type = $request->type;
        $quiz->score = $request->score;
        $quiz->published = $request->published;
        $quiz->content = $request['content'];
        $quiz->category_id = $request->category ? $request->category : null;
        $quiz->branch_id = $request->branch_id;
        $quiz->subject_id = $request->subject_id;
        $quiz->seller_id = $request->seller_id;
        $quiz->year = $request->year;
        $quiz->price = $request->price;
        $quiz->percentage = $request->percentage;
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
