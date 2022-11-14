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

        if(!$request->user()->hasRole('superadmin')){
            $seller = Seller::find($request->user()->seller_id);
        }
        $request->validate([
            'invoice_month' => ['required'],
            'invoice_year' => ['required']
        ]);
        $transactions = Seller::where('sellers.id', $seller->id)
            ->whereYear('transactions.created_at', $request->invoice_year)->whereMonth('transactions.created_at', $request->invoice_month)
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
                DB::raw("(SELECT IF(quizzes.id, quizzes.price, summaries.price)) as material_cost"),
                DB::raw("(SELECT IF(quizzes.id, quizzes.percentage, summaries.percentage)) as material_percentage"),
                DB::raw("(select COUNT(id) from transactions where seller_id = sellers.id AND transactions.subject_id = subjects.id AND (transactions.quiz_id = quizzes.id OR transactions.summary_id = summaries.id) AND year(`transactions`.`created_at`) = '".$request->invoice_year."' AND month(`transactions`.`created_at`) = '".$request->invoice_month."') as count"),
                DB::raw("(select SUM(cost) from transactions where seller_id = sellers.id AND transactions.subject_id = subjects.id AND (transactions.quiz_id = quizzes.id OR transactions.summary_id = summaries.id) AND year(`transactions`.`created_at`) = '".$request->invoice_year."' AND month(`transactions`.`created_at`) = '".$request->invoice_month."') as total"),
                DB::raw("(select (SUM(cost) * (material_percentage / 100)) from transactions where seller_id = sellers.id AND transactions.subject_id = subjects.id AND (transactions.quiz_id = quizzes.id OR transactions.summary_id = summaries.id) AND year(`transactions`.`created_at`) = '".$request->invoice_year."' AND month(`transactions`.`created_at`) = '".$request->invoice_month."') as net"),
            ])
            ->groupBy(['transactions.quiz_id', 'transactions.summary_id'])
            ->orderBy('count', 'desc')
            ->get();
        $total_payment = Transaction::where('transactions.seller_id', $seller->id)
            ->whereYear('transactions.created_at', $request->invoice_year)->whereMonth('transactions.created_at', $request->invoice_month)
            ->select(DB::raw('(SELECT SUM(cost)) as total'))->first()->total;

        $total_revenue = Transaction::where('transactions.seller_id', $seller->id)
            ->whereYear('transactions.created_at', $request->invoice_year)->whereMonth('transactions.created_at', $request->invoice_month)
            ->leftJoin('subjects', 'transactions.subject_id', '=', 'subjects.id')
            ->leftJoin('quizzes', 'quizzes.id', '=', 'transactions.quiz_id')
            ->leftJoin('summaries', 'summaries.id', '=', 'transactions.summary_id')
            ->select([
                'summaries.id as s_id',
                DB::raw("(SELECT IF(quizzes.id, quizzes.percentage, summaries.percentage)) as material_percentage"),
                DB::raw('(SELECT (cost * material_percentage / 100)) as total'),
            ])->get();
        $total_revenue = collect($total_revenue)->sum('total');
        return response([
            'total_payment' => $total_payment,
            'total_revenue' => $total_revenue,
            'invoice_items' => $transactions,
            'seller_information' => $seller,
            'company_information' => [
                'app_name' => option('app_name'),
                'company_name' => option('company_name'),
                'phone_number' => option('phone_number'),
                'email' => option('email'),
            ],
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
            $request->type . '_id' => $material->id,
            'seller_id' => $material->seller_id,
            'branch_id' => $material->branch_id,
            'year' => $material->year,
            'subject_id' => $material->subject_id,
        ];
        $already_transaction = Transaction::where(array_merge($material_data))->latest()->first();
        $material_data['cost'] = $material->price;


        if($already_transaction){
            if(!$already_transaction->deleted)
                return response(['message' => 'Already Purchased'], 403);
        }
        if ($material->branch_id && $material->year && $material->subject_id && $material->seller_id){
            $transaction = new Transaction($material_data);
            $transaction->save();
            $user->coins = $user->coins - $material->price;
            $user->save();
            $transaction->material_id = $transaction[$request->type . '_id'];
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

    public function delete_branch_transactions(Request $request)
    {
        $request->validate([
            'branch_id' => ['required'],
        ]);
        Transaction::where('branch_id', $request->branch_id)->update(['deleted' => 1]);
        return response(['message' => 'Deleted Successfully!'], 200);
    }

    public function rollback_delete_branch_transactions(Request $request)
    {
        $request->validate([
            'branch_id' => ['required'],
        ]);
        Transaction::where('branch_id', $request->branch_id)->update(['deleted' => 0]);
        return response(['message' => 'Rolled back Successfully!'], 200);
    }
}
