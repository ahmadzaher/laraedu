<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $subjects = [
            ['name' => 'Arabic', 'code' => 'ar', 'author' => 'ahmad', 'type' => 'Theory'],
            ['name' => 'English', 'code' => 'en', 'author' => 'zaher', 'type' => 'Theory'],
            ['name' => 'Science', 'code' => 'sc', 'author' => 'khrezaty', 'type' => 'Practical'],
            ['name' => 'Math', 'code' => 'ma', 'author' => 'yasser', 'type' => 'Optional'],
        ];
        DB::table('subjects')->insert($subjects);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
