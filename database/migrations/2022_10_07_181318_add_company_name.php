<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        option(['company_name' => 'Ideal Tech']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        option()->remove('company_name');
    }
}
