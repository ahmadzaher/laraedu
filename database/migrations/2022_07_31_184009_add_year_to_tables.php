<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('year')->nullable();
        });
        // questions
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('year')->nullable();
        });
        // groups
        Schema::table('question_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('year')->nullable();
        });
        // quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('year')->nullable();
        });
        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('year');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('year');
        });
        Schema::table('question_groups', function (Blueprint $table) {
            $table->dropColumn('year');
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('year');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
}
