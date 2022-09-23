<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsFromQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('quiz_metas');
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('duration_mode');
            $table->dropColumn('marks_points_mode');
            $table->dropColumn('disabled_finished_button');
            $table->dropColumn('hide_solution');
            $table->dropColumn('duration_minutes');
            $table->dropColumn('marks_for_correct_answer');
            $table->dropColumn('pass_percentage');
            $table->dropColumn('meta_title');
            $table->dropColumn('slug');
            $table->dropColumn('starts_at');
            $table->dropColumn('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('quiz_metas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('quiz_id');

            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');

            $table->string('key');

            $table->text('content')->nullable();
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('meta_title');
            $table->string('slug');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->tinyInteger('duration_mode')->default(0);
            $table->tinyInteger('marks_points_mode')->default(0);
            $table->tinyInteger('disabled_finished_button')->default(0);
            $table->tinyInteger('hide_solution')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->integer('marks_for_correct_answer')->default(0);
            $table->integer('pass_percentage')->default(0);
        });
    }
}
