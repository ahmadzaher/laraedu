<?php
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Config;



function get_service_option($option_name)
{
    $db = new DB;
    $db->addConnection(array(
        'driver'    => env('DB_CONNECTION'),
        'host'      => env('DB_HOST'),
        'database'  => env('DB_DATABASE'),
        'username'  => env('DB_USERNAME'),
        'password'  => env('DB_PASSWORD'),
    ));
    $db->setAsGlobal();
    $db->bootEloquent();
    return $db::table('options')->where('key', $option_name)->first()->value;
}

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id' => str_replace('"', '', get_service_option('facebook_client_id')),
        'client_secret' => str_replace('"', '', get_service_option('facebook_client_secret')),
        'redirect' => env('FACEBOOK_REDIRECT_URL'),
    ],

    'google' => [
        'client_id' => get_service_option('google_client_id'),
        'client_secret' => get_service_option('google_client_secret'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

];
