<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FacebookCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['facebook_client_id' => '502969594400285']);
        option(['facebook_client_secret' => '1ffe4e91a80c043eed9addcd03373845']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('facebook_client_id');
        option()->remove('facebook_client_secret');
    }
}
