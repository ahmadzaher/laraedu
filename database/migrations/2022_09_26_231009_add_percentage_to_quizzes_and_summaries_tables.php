<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercentageToQuizzesAndSummariesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('percentage')->nullable()->default('10.00');
        });
        Schema::table('summaries', function (Blueprint $table) {
            $table->string('percentage')->nullable()->default('10.00');
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
            $table->dropColumn('percentage');
        });
        Schema::table('summaries', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
}
