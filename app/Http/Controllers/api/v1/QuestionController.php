<?php

namespace App\Http\Controllers\api\v1;

use App\Answer;
use App\Http\Controllers\Controller;
use App\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::with('answers')->get();

        return Response($questions, 200);
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
            'type' => ['required', 'max:255'],
            //'active' => ['required', 'integer'],
            'level' => ['required', 'integer'],
            'score' => ['required'],
            'content' => ['required'],
            'answers.*.active' => ['integer'],
            'answers.*.content' => ['required'],
            'answers.*.correct' => ['required', 'integer'],
            'question_image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($request->type == 'single' || $request->type == 'multiple') {
            if (!is_array($request->answers))
                return Response(['msg' => 'No Answers'], 422);

            $question = new Question([
                'type' => $request->type,
                'active' => 1,
                'level' => $request->level,
                'score' => $request->score,
                'content' => $request['content']
            ]);

            if ($request->type == 'single') {
                $correct_exists = 0;
                foreach ($request->answers as $answer) {
                    if ($answer['correct'] && $correct_exists)
                        return Response(['msg' => 'Choose just one correct answer'], 422);
                    if ($answer['correct'] == 1)
                        $correct_exists = 1;
                }
            }

            $question->save();


            foreach ($request->answers as $answer) {
                $answer = new Answer([
                    'question_id' => $question->id,
                    'active' => 1,
                    'correct' => $answer['correct'],
                    'content' => $answer['content']
                ]);
                $answer->save();
            }
        }
        if ($request->type == 'short_answer') {
            if (!is_array($request->answer))
                return Response(['msg' => 'Answer is required'], 422);
            $question = new Question([
                'type' => $request->type,
                'active' => 1,
                'level' => $request->level,
                'score' => $request->score,
                'content' => $request['content'],
            ]);
            $question->save();

            foreach ($request->answer as $answer) {
                $answer = new Answer([
                    'question_id' => $question->id,
                    'active' => 1,
                    'correct' => 1,
                    'content' => $answer
                ]);
                $answer->save();
            }

            $question->answer = $request->answer;
        }
        if ($request->type == 'true/false') {
            if (!isset($request->correct))
                return Response(['msg' => 'Correct is required'], 422);

            $question = new Question([
                'type' => $request->type,
                'active' => 1,
                'level' => $request->level,
                'score' => $request->score,
                'content' => $request['content'],
            ]);
            $question->save();

            $answer = new Answer([
                'question_id' => $question->id,
                'active' => 1,
                'correct' => $request->correct
            ]);
            $answer->save();
        }


        $question = Question::with('answers')->find($question->id);

        if (isset($request->attachment_type, $request->attachment_url))
        {
            $question->attachment_type = $request->attachment_type;
            $question->attachment_url = $request->attachment_url;
            $question->save();
        }
        if (isset($request->question_image)) {
            $question->clearMediaCollection('question_images');
            $question->addMediaFromRequest('question_image')->toMediaCollection('question_images');
        }
        if ( $question->getFirstMediaUrl('question_images', 'question_image') )
        {
            $question->question_image = url($question->getFirstMediaUrl('question_images', 'question_image'));
        }
        unset($question->media);


        return Response($question, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        if ( $question->getFirstMediaUrl('question_images', 'question_image') )
        {
            $question->question_image = url($question->getFirstMediaUrl('question_images', 'question_image'));
        }
        unset($question->media);
        $question->answers = Answer::where('question_id', '=', $question->id)->get();
        return Response($question, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'type' => ['required', 'max:255'],
            //'active' => ['required', 'integer'],
            'level' => ['required', 'integer'],
            'score' => ['required'],
            'content' => ['required'],
            'answers.*.id' => ['required', 'integer'],
            'answers.*.content' => ['required'],
            'answers.*.correct' => ['required', 'integer'],
            'question_image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $question = Question::with('answers')->find($id);

        if ($request->type == 'single' || $request->type == 'multiple') {
            if (!is_array($request->answers))
                return Response(['msg' => 'No Answers'], 422);

            $question->type = $request->type;
            $question->active = 1;
            $question->level = $request->level;
            $question->score = $request->score;
            $question->content = $request['content'];

            if ($request->type == 'single') {
                $correct_exists = 0;
                foreach ($request->answers as $answer) {
                    if ($answer['correct'] && $correct_exists)
                        return Response(['msg' => 'Choose just one correct answer'], 422);
                    if ($answer['correct'] == 1)
                        $correct_exists = 1;
                }
            }

            $question->save();


            foreach ($request->answers as $answer) {
                $old_answer = Answer::find($answer['id']);
                $old_answer->question_id = $id;
                $old_answer->active = 1;
                $old_answer->correct = $answer['correct'];
                $old_answer->content = $answer['content'];
                $old_answer->save();
            }
            $question = Question::with('answers')->find($question->id);
        }

        if ($request->type == 'short_answer') {
            if (!is_array($request->answer))
                return Response(['msg' => 'Answer is required'], 422);
            $question->type = $request->type;
            $question->active = 1;
            $question->level = $request->level;
            $question->score = $request->score;
            $question->content = $request['content'];

            $question->save();

            foreach ($request->answer as $answer_id => $answer) {
                $old_answer = Answer::find($answer_id);
                $old_answer->question_id = $id;
                $old_answer->active = 1;
                $old_answer->correct = 1;
                $old_answer->content = $answer;
                $old_answer->save();
            }

        }

        if ($request->type == 'true/false') {
            if (!isset($request->correct))
                return Response(['msg' => 'Correct is required'], 422);


            $question->type = $request->type;
            $question->active = 1;
            $question->level = $request->level;
            $question->score = $request->score;
            $question->content = $request['content'];

            $question->save();

            $answer = Answer::where('question_id', '=', $id)->get()->last();

            $answer->question_id = $id;
            $answer->active = 1;
            $answer->correct = $request->correct;
            $answer->save();
        }

        $question = Question::with('answers')->find($id);
        return Response($question, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        $question->delete();
        return response(['message' => 'Question Deleted Successfully!'], 200);
    }
}
