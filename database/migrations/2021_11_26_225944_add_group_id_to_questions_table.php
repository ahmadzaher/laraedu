<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {

            $table->unsignedBigInteger('group_id');

            $table->foreign('group_id')->references('id')->on('question_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {

            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
}
