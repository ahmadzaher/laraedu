<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Quiz;
use App\Transaction;
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
        $user_id = auth()->user();
        if($request->type == 'quiz'){
            $quiz = Quiz::find($request->material_id);
            if (!$quiz)
                return response(['message' => 'Not found'], 404);
            return response($quiz, 201);
        }elseif($request->type == 'summary'){
            $summary = Quiz::find($request->material_id);
            if (!$summary)
                return response('Not found', 404);
            return response($summary, 201);
        }else{
            return response(['message' => 'Not found'], 404);
        }
        $transaction = new Transaction([

        ]);

        $t = [
            'user_id',
            'quiz_id',
            'summary_id',
            'seller_id',
            'branch_id',
            'year',
            'subject_id',
            'cost',
        ];
        $transaction->save();
        return response($request->data, 201);
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
