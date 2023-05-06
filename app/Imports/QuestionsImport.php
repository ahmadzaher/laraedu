<?php

namespace App\Imports;

use App\Answer;
use App\Question;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class QuestionsImport implements ToCollection, WithStartRow
{
    private $rows = 0;
    private $branch_id;
    private $year;
    private $subject_id;
    private $seller_id;
    private $group_id;

    public function __construct(int $branch_id, int $year, int $subject_id, int $seller_id, int $group_id)
    {
        $this->branch_id = $branch_id;
        $this->year = $year;
        $this->subject_id = $subject_id;
        $this->seller_id = $seller_id;
        $this->group_id = $group_id;
    }


    public function collection(Collection $rows)
    {


        Validator::make($rows->toArray(), [
            '*.0' => 'required',
            '*.1' => 'required',
            '*.8' => 'required',
        ])->validate();

        foreach ($rows as $key => $row) {
            $answer_found = $correct_found = false;
            for ($i = 2; $i <= 7; $i++) {
                if ($row[$i] != null) {
                    $answer_found = true;
                    if ($row[8] == $i - 1)
                        $correct_found = true;
                }
            }
            if(!$answer_found || !$correct_found ){
                if (!$answer_found)
                    $error = 'No answer found in raw [ ' . $key . ' ]';
                else
                    $error = 'No correct found in raw [ ' . $key . ' ]';
                header('Content-Type: application/json');
                header('HTTP/1.1 422 Unprocessable Entity', TRUE, 422);
                echo json_encode(['message' => "The given data was invalid.", 'errors' => ['answers' => [$error]] ]); exit;
            }
        }





        for ( $x = 0; $x <= 10; $x++ )
        {
            $x = $x + $x;
        }
        echo $x;





        foreach ($rows as $row)
        {
            $question = new Question([
                'type' => $row[0],
                'active' => 1,
                'level' => 1,
                'score' => 1,
                'default_time' => 1,
                'content' => $row[1],
                'group_id' => $this->group_id,
                'solution' => $row[9],
                'hint' => $row[10],
                'branch_id' => $this->branch_id,
                'subject_id' => $this->subject_id,
                'seller_id' => $this->seller_id,
                'year' => $this->year,
            ]);
            $question->save();

            if ($row[0] == 'single'){
                for ($i = 2; $i <= 7; $i++)
                {
                    if($row[$i] != null)
                    {
                        $answer = new Answer([
                            'question_id' => $question->id,
                            'active' => 1,
                            'correct' => $row[8] == $i - 1,
                            'content' => $row[$i]
                        ]);
                        $answer->save();
                    }
                }
            }

        }
    }

    /**
     * @param array $row
     *
     * @return Question|null
     */
    public function model(array $row)
    {

    }
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
//
//    public function getRowCount(): int
//    {
//        return $this->rows;
//    }
}
