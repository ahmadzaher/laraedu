<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FrontendDefaultSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['cms_title' => env('APP_NAME')]);
        option(['active' => 1]);
        option(['receive_email_to' => env('mail_from_address')]);
        option(['number_of_working_hours' => '5/9']);
        option(['logo' => '']);
        option(['fav_icon' => '']);
        option(['phone_number' => '0948842975']);
        option(['email' => 'ahmad.khrezaty@gmail.com']);
        option(['fax' => '']);
        option(['footer_about_text' => '']);
        option(['copyright_text' => '']);
        option(['facebook_url' => '']);
        option(['twitter_url' => '']);
        option(['youtube_url' => '']);
        option(['google_url' => '']);
        option(['linkedin_url' => '']);
        option(['pinterest_url' => '']);
        option(['instagram_url' => '']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('cms_title');
        option()->remove('active');
        option()->remove('receive_email_to');
        option()->remove('number_of_working_hours');
        option()->remove('logo');
        option()->remove('fav_icon');
        option()->remove('phone_number');
        option()->remove('email');
        option()->remove('fax');
        option()->remove('footer_about_text');
        option()->remove('copyright_text');
        option()->remove('facebook_url');
        option()->remove('twitter_url');
        option()->remove('youtube_url');
        option()->remove('google_url');
        option()->remove('linkedin_url');
        option()->remove('pinterest_url');
        option()->remove('instagram_url');
    }
}
