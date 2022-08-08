<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_id');
            $table->unsignedBigInteger('question_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['bank_id','question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banks_questions');
    }
}
