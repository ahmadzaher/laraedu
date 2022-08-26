<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerIdToTables extends Migration
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
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
        // questions
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
        // groups
        Schema::table('question_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
        // quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
        // summaries
        Schema::table('summaries', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
        // subjects
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
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
            $table->dropForeign('users_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
        Schema::table('question_groups', function (Blueprint $table) {
            $table->dropForeign('question_groups_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign('quizzes_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
        Schema::table('summaries', function (Blueprint $table) {
            $table->dropForeign('summaries_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign('sellers_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
    }
}
