<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HeroAreaOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['hero_title' => '']);
        option(['hero_photo_url' => '']);
        option(['button_text' => 'Get Started!']);
        option(['button_url' => '#']);
        option(['hero_description' => '']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('title');
        option()->remove('hero_photo');
        option()->remove('hero_photo_url');
        option()->remove('button_text');
        option()->remove('button_url');
        option()->remove('description');
    }
}
