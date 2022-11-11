<?php

namespace App\Http\Controllers\api\v1;

use App\Code;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class CodeController extends Controller
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
     * Store a newly created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $request->validate([
            'data.*.code' => ['required', 'unique:codes'],
            'data.*.price' => ['required'],
        ]);
        foreach ($request->data as $data){
            $code = new Code([
                'code' => $data['code'],
                'price' => $data['price'] ? $data['price'] : 5000
            ]);
            $code->save();
        }
        return response($request->data, 201);
    }

    /**
     * Charge student account
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function charge(Request $request)
    {
        $request->validate([
            'code' => ['required'],
        ]);
        $user = auth()->user();

        $user = User::find($user->id);
        $code = Code::where('code', $request->code)->first();
        if(!$code)
            return response(['message' => 'Incorrect code!'], 404);
        if($code->activated)
            return response(['message' => 'Code is already activated'], 403);

        $code->activated = 1;
        $code->user_id = $user->id;
        $code->save();
        $user->coins = $user->coins + $code->price;
        $user->save();
        return response($user, 201);
    }

    /**
     * Check the specified resource.
     *
     * @param  \App\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        $request->validate([
            'code' => ['required'],
        ]);

        $code = Code::where('code', $request->code)->first();

        if(!$code)
            return response(['msg' => 'Incorrect code!'], 404);
        if($code->activated)
            return response(['msg' => 'Code is already activated'], 403);

        return response(['msg' => 'Correct code!'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function show(Code $code)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Code $code)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Code  $code
     * @return \Illuminate\Http\Response
     */
    public function destroy(Code $code)
    {
        //
    }
}
