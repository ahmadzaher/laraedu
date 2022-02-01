<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GlobalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['default_language' => 'en']);
        option(['default_direction' => 'ltr']);
        option(['app_name' => 'SMS App']);
        option(['app_logo' => '']);
        option(['app_favicon' => '']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('default_language');
        option()->remove('default_direction');
        option()->remove('app_name');
        option()->remove('app_logo');
        option()->remove('app_favicon');
    }
}
