<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersActivationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_activation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            $table->string('token');
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {

            $table->boolean('is_activated');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_activation');
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('is_activated');

        });
    }
}
