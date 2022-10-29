<?php
namespace App\Exports;
use App\Question;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithProperties;

class QuestionsExport implements FromCollection, WithStrictNullComparison, WithColumnWidths, WithStyles, WithHeadings, WithProperties{

    private $branch_id;
    private $year;
    private $subject_id;
    private $seller_id;


    public function __construct(int $branch_id, int $year, int $subject_id, int $seller_id)
    {
        $this->branch_id = $branch_id;
        $this->year = $year;
        $this->subject_id = $subject_id;
        $this->seller_id = $seller_id;
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 33,
            'J' => 23,
            'K' => 23,
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Al Daheeh',
            'lastModifiedBy' => 'Al Daheeh',
            'title'          => 'Questions',
            'description'    => 'Questions related to Al Daheeh application',
            'subject'        => 'Al Daheeh questions',
            'keywords'       => 'questions,quiz,daheeh,aldaheeh',
            'category'       => 'Questions',
            'manager'        => 'Ahmad Zaher Khrezaty',
            'company'        => 'Ideal Tech',
        ];
    }


    public function headings(): array
    {
        return [
            'Type',
            'question',
            'Answer 1',
            'Answer 2',
            'Answer 3',
            'Answer 4',
            'Answer 5',
            'Answer 6',
            'Correct',
            'solution',
            'hint',
        ];
    }

    public function collection()
    {
//        $questions_collection = Question::where('questions.branch_id', $this->branch_id)
//            ->with('answers')
//            ->where('questions.subject_id', $this->subject_id)
//            ->where('questions.seller_id', $this->seller_id)
//            ->where('questions.type', 'single')
//            ->where('questions.year', $this->year)
//            ->select('type as question_type', 'content as question', 'solution', 'hint')
//            ->get();

        $questions_collection = [];

        $questions = Question::where('questions.branch_id', $this->branch_id)
            ->with('answers')
            ->where('questions.subject_id', $this->subject_id)
            ->where('questions.seller_id', $this->seller_id)
            ->where('questions.type', 'single')
            ->where('questions.year', $this->year)
            ->get();
        foreach ($questions as $question_key => $question) {
            $new_question = new \stdClass();
            // question properties
            $new_question->question_type = $question->type;
            $new_question->question = $question->content;
            // default answers
            $new_question->answer_1 = '';
            $new_question->answer_2 = '';
            $new_question->answer_3 = '';
            $new_question->answer_4 = '';
            $new_question->answer_5 = '';
            $new_question->answer_6 = '';
            $new_question->correct = '1';
            $answers = $question->answers;
            // set the answers
            $answers->each(function ($answer, $key) use ($new_question) {
                //                    dd($answer);
                $answer_heading = 'answer_' . ( $key + 1 );
                $new_question->$answer_heading = $answer->content;
            });
            $new_question->solution = $question->solution;
            $new_question->hint = $question->hint;
            $questions[$question_key] = $new_question;
        }


        return $questions;
    }

}
