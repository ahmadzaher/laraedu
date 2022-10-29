<?php

namespace App\Http\Controllers;

use App\Subject;
use App\User;
use Illuminate\Http\Request;
use App\Exports\QuestionsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function index(Request $request)
    {

        $request->validate([
            'branch_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'],
            'seller_id' => ['required', 'integer'],
            'year' => ['required', 'integer']
        ]);
        $branch_id = $request->branch_id;
        $year = $request->year;
        $subject_id = $request->subject_id;
        $seller_id = $request->seller_id;
        return Excel::download(new QuestionsExport($branch_id, $year, $subject_id, $seller_id), 'questions.xlsx');

    }
}
