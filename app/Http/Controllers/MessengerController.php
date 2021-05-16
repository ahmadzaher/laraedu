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
            return $challenge;
        }
        return response()->json( config('messenger.messenger_verify_token'), 200);
    }
    public function webhook_get(Request $request)
    {
        $verify_token = $request->hub_verify_token;
        $challenge = $request->hub_challenge;
        if($verify_token != config('messenger.messenger_verify_token')){
            echo $challenge;
        }
        $json_response = http_build_query($_GET);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://5ac5cb97800aeb91bd52fecc4200f283.m.pipedream.net');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json_response);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $reply_response=curl_exec($ch);

        //return response()->json( config('messenger.messenger_verify_token'), 200);
    }
}
