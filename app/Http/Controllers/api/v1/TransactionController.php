<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Quiz;
use App\Seller;
use App\Summary;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function seller(Seller $seller, Request $request)
    {
        $request->validate([
            'month' => ['required'],
            'year' => ['required']
        ]);
        $transactions = Seller::where('sellers.id', $seller->id)
            ->whereYear('transactions.created_at', $request->year)->whereMonth('transactions.created_at', $request->month)
            ->leftJoin('transactions', 'sellers.id', '=', 'transactions.seller_id')
            ->where(function ($query) use ($request){
                $query->where('transactions.quiz_id', '!=', null)
                    ->orWhere('transactions.summary_id', '!=', null);
            })
            ->leftJoin('subjects', 'transactions.subject_id', '=', 'subjects.id')
            ->leftJoin('quizzes', 'quizzes.id', '=', 'transactions.quiz_id')
            ->leftJoin('summaries', 'summaries.id', '=', 'transactions.summary_id')
            ->leftJoin('branches', 'branches.id', '=', 'transactions.branch_id')
            ->select([
                'branches.name as branch_name',
                'transactions.year as year',
                'subjects.name as subject_name',
                DB::raw("(SELECT IF(quizzes.id, quizzes.title, summaries.name)) as material_name"),
                DB::raw("(SELECT IF(quizzes.id, 'quiz', 'summary')) as material_type"),
                DB::raw("(SELECT IF(quizzes.id, quizzes.id, summaries.id)) as material_id"),
                DB::raw("(SELECT IF(quizzes.id, quizzes.percentage, summaries.percentage)) as material_percentage"),
                DB::raw("(SELECT IF(quizzes.id, quizzes.price, summaries.price)) as material_cost"),
                DB::raw("(select COUNT(id) from transactions where seller_id = sellers.id AND transactions.subject_id = subjects.id AND (transactions.quiz_id = quizzes.id OR transactions.summary_id = summaries.id) AND year(`transactions`.`created_at`) = '".$request->year."' AND month(`transactions`.`created_at`) = '".$request->month."') as count"),
                DB::raw("(select SUM(cost) from transactions where seller_id = sellers.id AND transactions.subject_id = subjects.id AND (transactions.quiz_id = quizzes.id OR transactions.summary_id = summaries.id) AND year(`transactions`.`created_at`) = '".$request->year."' AND month(`transactions`.`created_at`) = '".$request->month."') as total"),
                DB::raw("(select (SUM(cost) * (material_percentage / 100)) from transactions where seller_id = sellers.id AND transactions.subject_id = subjects.id AND (transactions.quiz_id = quizzes.id OR transactions.summary_id = summaries.id) AND year(`transactions`.`created_at`) = '".$request->year."' AND month(`transactions`.`created_at`) = '".$request->month."') as net"),
            ])
            ->groupBy(['transactions.quiz_id', 'transactions.summary_id'])
            ->orderBy('count', 'desc')
            ->get();
        $total_payment = Transaction::where('transactions.seller_id', $seller->id)
            ->whereYear('transactions.created_at', $request->year)->whereMonth('transactions.created_at', $request->month)
            ->select(DB::raw('(SELECT SUM(cost)) as total'))->first()->total;
        $total_revenue = Transaction::where('transactions.seller_id', $seller->id)
            ->leftJoin('quizzes', 'quizzes.id', '=', 'transactions.quiz_id')
            ->leftJoin('summaries', 'summaries.id', '=', 'transactions.summary_id')
            ->whereYear('transactions.created_at', $request->year)->whereMonth('transactions.created_at', $request->month)
            ->select(DB::raw('(SELECT (
                SUM(cost) * (SELECT IF(quizzes.id, quizzes.percentage, summaries.percentage) / 100)
            )) as total'))->first()->total;
        return response([
            'total_payment' => $total_payment,
            'total_revenue' => $total_revenue,
            'invoice_items' => $transactions
        ]);
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
            'type' => ['required'],
            'material_id' => ['required']
        ]);
        $user = auth()->user();
        if($request->type == 'quiz'){
            $material = Quiz::find($request->material_id);
            if (!$material)
                return response(['message' => 'Material not found'], 404);
        }elseif($request->type == 'summary'){
            $material = Summary::find($request->material_id);
            if (!$material)
                return response(['message' => 'Material not found'], 404);
        }else{
            return response(['message' => 'Type should be quiz or summary'], 422);
        }
        if($user->coins < $material->price){
            return response(['message' => 'There is no enough money in your account'], 403);
        }
        if($material->price == null)
            $material->price = 0;
        $material_data = [
            'user_id' => $user->id,
            'material_id' => $material->id,
            'seller_id' => $material->seller_id,
            'branch_id' => $material->branch_id,
            'year' => $material->year,
            'subject_id' => $material->subject_id,
            'cost' => $material->price
        ];
        $already_transaction = Transaction::where(array_merge($material_data))->latest()->first();
        $date_before_6_months = strtotime(Carbon::now());


        if($already_transaction){
            $year1 = date('Y', strtotime($already_transaction->created_at));
            $year2 = date('Y', $date_before_6_months);

            $month1 = date('m', strtotime($already_transaction->created_at));
            $month2 = date('m', $date_before_6_months);

            $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
            if($diff < 6)
                return response(['message' => 'Already Purchased'], 403);
        }
        if ($material->branch_id && $material->year && $material->subject_id && $material->seller_id){
            $transaction = new Transaction($material_data);
            $transaction->save();
            $user->coins = $user->coins - $material->price;
            $user->save();
            return response($transaction, 201);
        }
        return response(['message' => 'Something went wrong!'], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
