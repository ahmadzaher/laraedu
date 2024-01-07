<?php

namespace App\Http\Controllers\api\v1;

use App\Answer;
use App\Http\Controllers\Controller;
use App\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;
use Symfony\Component\Stopwatch\Stopwatch;

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
    public function generate(Request $request)
    {

        $request->validate([
            'branch_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'],
            'seller_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'year' => ['required', 'integer'],
            'content' => ['required'],
        ]);


        $openai_api_key = env('OPENAI_TOKEN');

        $api_url = 'https://api.openai.com/v1/chat/completions'; // استخدام نموذج GPT-3
        $content = $request['content'];
        $data = array(
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.1,
            "messages" => [
                [
                    "role" => "system",
                    "content" => ' أنت محترف في كتابة الامتحانات المؤتمتة بحيث عند ارسال المستخدم نص تقوم باستخراج عدة اسئلة منه ولكل سؤال 4 اجابات وتقوم بطباعة الأسئلة والأجوبة بنفس لغة النص الذي أعطاه لك المستخدم واخراج جميع الأسئلة المحتمل ورودها في الامتحان النائي "بصيغة json وبهذا الشكل حصرا"
                {
"questions": [
{
    "content": "كم عدد الطلاب",
    "answers": [
        {
            "correct": "0",
            "content": "طالبان"
        },
        {
            "correct": "1",
            "content": "ثلاثة طلاب"
        },
        {
            "correct": "0",
            "content": "أربقة طلاب"
        },
        {
            "correct": "0",
            "content": "خمسة طلاب"
        }
    ]
}
]}
بحيث تكون القيمة correct هي 1 في حال كانت الاجابة صحيحة ويجب وضع السؤال الصحيح
بمكان مختلف ضمن المصفوفة في كل مرة ويوجد اجابة صحيحة دائما ويجب عدم الخروج عن النص المكتوب أبدا ويجب عدم انهاء الجواب حتى اكمال المصفوفة "بصيغة json وبهذا الشكل حصرا"'
                ],
                [
                    "role" => "user",
                    "content" => $content
                ]
            ]
        );

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $openai_api_key,
        );

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
        $decoded_response = json_decode($response, true);
        $generated_text = json_decode($decoded_response['choices'][0]['message']['content']);
        if(is_object($generated_text))
        foreach ($generated_text->questions as $question)
        {
            if (!is_array($question->answers))
                return Response(['msg' => 'No Answers'], 422);

            $new_question = new Question([
                'type' => 'single',
                'active' => 1,
                'level' => 1,
                'score' => 1,
                'default_time' => 50,
                'content' => $question->content,
                'group_id' => $request->group_id,
                'solution' => '',
                'hint' => '',
                'branch_id' => $request->branch_id,
                'subject_id' => $request->subject_id,
                'seller_id' => $request->seller_id,
                'year' => $request->year,
            ]);

            $correct_exists = 0;
            foreach ($new_question->answers as $answer) {
                if ($answer['correct'] && $correct_exists)
                    return Response(['msg' => 'Choose just one correct answer'], 422);
                if ($answer['correct'] == 1)
                    $correct_exists = 1;
            }

            $new_question->save();


            foreach ($question->answers as $answer) {
                $answer = new Answer([
                    'question_id' => $new_question->id,
                    'active' => 1,
                    'correct' => $answer->correct,
                    'content' => $answer->content
                ]);
                $answer->save();
            }
        }



        return response(['question_generated' => is_object($generated_text) ? count($generated_text->questions) : 0, 'message' => 'success']);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'branch_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'],
            'seller_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'year' => ['required', 'integer'],
            'file' => ['required', 'mimes:xlsx'],
        ]);
        $group_id = $request->group_id;
        $branch_id = $request->branch_id;
        $year = $request->year;
        $subject_id = $request->subject_id;
        $seller_id = $request->seller_id;
        Excel::import(new QuestionsImport($branch_id, $year, $subject_id, $seller_id, $group_id), $request->file);

        return response(['message' => 'Imported Successfully!']);
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
                'group_id' => $request->group ? $request->group : null,
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
                    ->usingFileName(sha1(request('answer_img')))
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

        if ($question->type === 'essay') {
            $answers = $question->answers;
            $answers->each(function ($answer) {
                if($answer->getFirstMediaUrl('answer_images'))
                    $answer->answer_image = url($answer->getFirstMediaUrl('answer_images'));
            });
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
            $question->active = $request->active;
            $question->level = $request->level;
            $question->score = $request->score;
            $question->default_time = $request->default_time;
            $question->content = $request['content'];
            $question->group_id = $request->group ? $request->group : null;
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
            $question->active = $request->active;
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
            $question->active = $request->active;
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
            $question->active = $request->active;
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
            if (isset($request->delete_essay_image)) {
                $question->clearMediaCollection('answer_images');
            }

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
