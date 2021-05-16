<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessengerController extends Controller
{
    public function webhook(Request $request)
    {
        $verify_token = $request->hub_verify_token;
        $challenge = $request->hub_challenge;
        if($verify_token == config('messenger.messenger_verify_token')){
            echo $challenge;
        }
        return response()->json( config('messenger.messenger_verify_token'), 200);
    }
    public function webhook_get(Request $request)
    {
        $verify_token = $request->hub_verify_token;
        $challenge = $request->hub_challenge;
        if($verify_token == config('messenger.messenger_verify_token')){
            echo $challenge;
        }
        return response()->json( config('messenger.messenger_verify_token'), 200);
    }
}
