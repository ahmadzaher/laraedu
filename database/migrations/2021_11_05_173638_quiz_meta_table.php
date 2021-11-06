<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuizMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_meta', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('quiz_id');

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');

            $table->string('key');

            $table->text('content')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_meta');
    }
}
