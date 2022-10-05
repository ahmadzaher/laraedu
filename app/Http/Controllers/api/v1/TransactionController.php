<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Quiz;
use App\Summary;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        return response(['message' => 'Some thing went wrong!'], 404);
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
