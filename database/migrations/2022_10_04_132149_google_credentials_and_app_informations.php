<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GoogleCredentialsAndAppInformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['google_client_id' => '502969594400285']);
        option(['google_client_secret' => '1ffe4e91a80c043eed9addcd03373845']);
        option(['privacy_policy' => '']);
        option(['app_version' => '']);
        option(['app_disabled' => '']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('google_client_id');
        option()->remove('google_client_secret');
        option()->remove('privacy_policy');
        option()->remove('app_version');
        option()->remove('app_disabled');
    }
}
