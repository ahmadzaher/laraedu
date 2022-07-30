<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToTables extends Migration
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
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
        // questions
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
        // groups
        Schema::table('question_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
        // quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
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
            $table->dropForeign('users_branch_id_foreign');
            $table->dropColumn('branch_id');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_branch_id_foreign');
            $table->dropColumn('branch_id');
        });
        Schema::table('question_groups', function (Blueprint $table) {
            $table->dropForeign('question_groups_branch_id_foreign');
            $table->dropColumn('branch_id');
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign('quizzes_branch_id_foreign');
            $table->dropColumn('branch_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_branch_id_foreign');
            $table->dropColumn('branch_id');
        });
    }
}
