<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSettingsToQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->tinyInteger('duration_mode')->default(0);
            $table->tinyInteger('marks_points_mode')->default(0);
            $table->tinyInteger('disabled_finished_button')->default(0);
            $table->tinyInteger('hide_solution')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->integer('marks_for_correct_answer')->default(0);
            $table->integer('pass_percentage')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('duration_mode');
            $table->dropColumn('marks_points_mode');
            $table->dropColumn('disabled_finished_button');
            $table->dropColumn('hide_solution');
            $table->dropColumn('duration_minutes');
            $table->dropColumn('marks_for_correct_answer');
            $table->dropColumn('pass_percentage');
        });
    }
}
