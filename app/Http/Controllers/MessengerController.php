<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessengerController extends Controller
{
    public function webhook(Request $request)
    {
        $verify_token = $request->hub_verify_token;
        if($verify_token == config('messenger.messenger_verify_token')){
            return response()->json( 'correct', 200);
        }
        return response()->json( config('messenger.messenger_verify_token'), 200);
    }
}
