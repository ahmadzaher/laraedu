<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['app' => '']);
        option(['app_current_version' => '1']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('app');
        option()->remove('app_current_version');
    }
}
