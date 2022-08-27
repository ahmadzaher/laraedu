<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSellerIdFromQuestionGroupsAndFromCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_groups', function (Blueprint $table) {
            $table->dropForeign('question_groups_seller_id_foreign');
            $table->dropColumn('seller_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_seller_id_foreign');
            $table->dropColumn('seller_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // groups
        Schema::table('question_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });

        // categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
    }
}
