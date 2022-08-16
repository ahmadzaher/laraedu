<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectIdToTables extends Migration
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
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
        // questions
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
        // groups
        Schema::table('question_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
        // quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
        // summaries
        Schema::table('summaries', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
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
            $table->dropForeign('users_subject_id_foreign');
            $table->dropColumn('subject_id');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_subject_id_foreign');
            $table->dropColumn('subject_id');
        });
        Schema::table('question_groups', function (Blueprint $table) {
            $table->dropForeign('question_groups_subject_id_foreign');
            $table->dropColumn('subject_id');
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign('quizzes_subject_id_foreign');
            $table->dropColumn('subject_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_subject_id_foreign');
            $table->dropColumn('subject_id');
        });
        Schema::table('summaries', function (Blueprint $table) {
            $table->dropForeign('summaries_subject_id_foreign');
            $table->dropColumn('subject_id');
        });
    }
}
