<?php

namespace App\Http\Controllers\api\v1;

use App\Answer;
use App\Http\Controllers\Controller;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
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
        $subject_id = $request->subject_id;
        $seller_id = $request->seller_id;
        $search = $request->search;
        $types = $request->types;
        $groups = $request->groups;
        $difficulties = $request->difficulties;

            $questions = Question::with('answers')
                ->leftJoin('question_groups', 'question_groups.id', '=', 'questions.group_id')
                ->latest()->where(function ($query) use ($search) {
                    $query->where('content', 'like', '%'.$search.'%')
                        ->orWhere('questions.id', 'like', '%'.$search.'%')
                        ->orWhere('type', 'like', '%'.$search.'%');

                })->where(function ($query) use ($branch_id, $year, $subject_id, $seller_id) {

                    if($branch_id != ''){
                        $query->where('questions.branch_id', $branch_id);
                        $query->where('questions.subject_id', $subject_id);
                        $query->where('questions.seller_id', $seller_id);
                        $query->where('questions.year', $year);
                    }
                })->where(function ($query) use ($types) {

                    if(is_array($types) && !empty($types)){
                        $query->whereIn('type', $types);
                    }
                })->where(function ($query) use ($difficulties) {

                    if(is_array($difficulties) && !empty($difficulties)){
                        $query->whereIn('level', $difficulties);
                    }
                })->where(function ($query) use ($groups) {

                    if(is_array($groups) && !empty($groups)){
                        $query->whereIn('group_id', $groups);
                    }
                })->select(['questions.*', 'question_groups.title as group_name'])->paginate($request->per_page);

            $questions->each(function ($question) {
                if ($question->type === 'essay') {
                    $answers = $question->answers;
                    $answers->each(function ($answer) {
                        $answer->answer_image = url($answer->getFirstMediaUrl('answer_images'));
                    });
                }
            });

        return response($questions, 200);
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
            'group' => ['required', 'integer'],
            'level' => ['required', 'integer'],
            'score' => ['required'],
            'default_time' => ['required'],
            'content' => ['required'],
            'answers.*.active' => ['integer'],
            'answers.*.content' => ['required'],
            'answer.*' => ['required'],
            'answers.*.correct' => ['required', 'integer'],
            'question_image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
//            'branch_id' => ['required', 'integer'],
//            'subject_id' => ['required', 'integer'],
//            'seller_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        if(is_string($request->answers))
            $request->answers = json_decode($request->answers);
        if ($request->type == 'single' || $request->type == 'multiple') {
            if (!is_array($request->answers))
                return Response(['msg' => 'No Answers'], 422);

            $question = new Question([
                'type' => $request->type,
                'active' => 1,
                'level' => $request->level,
                'score' => $request->score,
                'default_time' => $request->default_time,
                'content' => $request['content'],
                'group_id' => $request->group,
                'solution' => $request->solution,
                'hint' => $request->hint,
                'branch_id' => $request->branch_id,
                'subject_id' => $request->subject_id,
                'seller_id' => $request->seller_id,
                'year' => $request->year,
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
                'active' => $request->active,
                'level' => $request->level,
                'score' => $request->score,
                'default_time' => $request->default_time,
                'content' => $request['content'],
                'group_id' => $request->group,
                'solution' => $request->solution,
                'hint' => $request->hint,
                'branch_id' => $request->branch_id,
                'subject_id' => $request->subject_id,
                'seller_id' => $request->seller_id,
                'year' => $request->year,
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

        }
        if ($request->type == 'true/false') {
            if (!isset($request->correct))
                return Response(['msg' => 'Correct is required'], 422);

            $question = new Question([
                'type' => $request->type,
                'active' => $request->active,
                'level' => $request->level,
                'score' => $request->score,
                'default_time' => $request->default_time,
                'content' => $request['content'],
                'group_id' => $request->group,
                'solution' => $request->solution,
                'hint' => $request->hint,
                'branch_id' => $request->branch_id,
                'subject_id' => $request->subject_id,
                'seller_id' => $request->seller_id,
                'year' => $request->year,
            ]);
            $question->save();

            $answer = new Answer([
                'question_id' => $question->id,
                'active' => 1,
                'correct' => $request->correct
            ]);
            $answer->save();
        }
        if ($request->type === 'essay') {

            $question = new Question([
                'type' => request('type'),
                'active' => request('active'),
                'level' => request('level'),
                'score' => request('score'),
                'default_time' => request('default_time'),
                'content' => request('content'),
                'group_id' => request('group'),
                'solution' => request('solution'),
                'hint' => request('hint'),
                'branch_id' => request('branch_id'),
                'subject_id' => request('subject_id'),
                'seller_id' => request('seller_id'),
                'year' => request('year')
            ]);
            $question->save();

            $answer = new Answer([
                'question_id' => $question->id,
                'active' => 1,
                'correct' => 1,
                'content' => $request->answer
            ]);
            $answer->save();

            if (request()->hasFile('answer_img') && request()->file('answer_img')->isValid()) {
                $answer->addMediaFromRequest('answer_img')
                    ->toMediaCollection('answer_images');
            }
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
        unset($question->media);
        $question = Question::with('answers')
            ->leftJoin('question_groups', 'question_groups.id', '=', 'questions.group_id')
            ->select(['questions.*', 'question_groups.title as group_name'])->find($question->id);
        //$question->answers = Answer::where('question_id', '=', $question->id)->get();
        if ( $question->getFirstMediaUrl('question_images', 'question_image') )
        {
            $question->question_image = url($question->getFirstMediaUrl('question_images', 'question_image'));
        }
        unset($question->media);
        return Response($question, 200);
    }


    public function get($id)
    {
        $question = Question::find($id);
        unset($question->media);
        $question = Question::with('answers')
            ->leftJoin('question_groups', 'question_groups.id', '=', 'questions.group_id')
            ->select(['questions.*', 'question_groups.title as group_name'])->find($question->id);
        //$question->answers = Answer::where('question_id', '=', $question->id)->get();
        if ( $question->getFirstMediaUrl('question_images', 'question_image') )
        {
            $question->question_image = url($question->getFirstMediaUrl('question_images', 'question_image'));
        }
        unset($question->media);
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
            'group' => ['required', 'integer'],
            'level' => ['required', 'integer'],
            'score' => ['required'],
            'default_time' => ['required'],
            'content' => ['required'],
            'answers.*.active' => ['integer'],
            'answers.*.content' => ['required'],
            'answer.*' => ['required'],
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
            $question->default_time = $request->default_time;
            $question->content = $request['content'];
            $question->group_id = $request->group;
            $question->solution = $request->solution;
            $question->hint = $request->hint;
            $question->branch_id = $request->branch_id;
            $question->subject_id = $request->subject_id;
            $question->seller_id = $request->seller_id;
            $question->year = $request->year;

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
            DB::table('answers')->where('question_id', $question->id)->delete();

            foreach ($request->answers as $answer) {
                $answer = new Answer([
                    'question_id' => $question->id,
                    'active' => 1,
                    'correct' => $answer['correct'],
                    'content' => $answer['content']
                ]);
                $answer->save();
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
            $question->default_time = $request->default_time;
            $question->content = $request['content'];
            $question->group_id = $request->group;
            $question->solution = $request->solution;
            $question->hint = $request->hint;
            $question->branch_id = $request->branch_id;
            $question->subject_id = $request->subject_id;
            $question->seller_id = $request->seller_id;
            $question->year = $request->year;

            $question->save();
            DB::table('answers')->where('question_id', $question->id)->delete();

            foreach ($request->answer as $answer) {
                $answer = new Answer([
                    'question_id' => $question->id,
                    'active' => 1,
                    'correct' => 1,
                    'content' => $answer
                ]);
                $answer->save();
            }


        }

        if ($request->type == 'true/false') {
            if (!isset($request->correct))
                return Response(['msg' => 'Correct is required'], 422);


            $question->type = $request->type;
            $question->active = 1;
            $question->level = $request->level;
            $question->score = $request->score;
            $question->default_time = $request->default_time;
            $question->content = $request['content'];
            $question->group_id = $request->group;
            $question->solution = $request->solution;
            $question->hint = $request->hint;
            $question->branch_id = $request->branch_id;
            $question->subject_id = $request->subject_id;
            $question->seller_id = $request->seller_id;
            $question->year = $request->year;

            $question->save();

            $answer = Answer::where('question_id', '=', $id)->get()->last();


            DB::table('answers')->where('question_id', $id)->where('id', '!=', $answer->id)->delete();

            $answer->question_id = $id;
            $answer->active = 1;
            $answer->correct = $request->correct;
            $answer->save();
        }

        if (request('type') === 'essay') {
            $question->type = request('type');
            $question->active = 1;
            $question->level = request('level');
            $question->score = request('score');
            $question->default_time = request('default_time');
            $question->content = request('content');
            $question->group_id = request('group');
            $question->solution = request('solution');
            $question->hint = request('hint');
            $question->branch_id = request('branch_id');
            $question->subject_id = request('subject_id');
            $question->seller_id = request('seller_id');
            $question->year = request('year');

            $question->save();

            Answer::where('question_id', '=', $id)->get()->last()->delete();

            $answer = new Answer([
                'question_id' => $id,
                'active' => 1,
                'correct' => 1,
                'content' => request('answer')
            ]);
            $answer->save();

            if (request()->hasFile('answer_img') && request()->file('answer_img')->isValid()) {
                $answer->addMediaFromRequest('answer_img')
                    ->toMediaCollection('answer_images');
            }

        }



        $question = Question::with('answers')->find($id);
        if (isset($request->delete_question_image)) {
            $question->clearMediaCollection('question_images');
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
