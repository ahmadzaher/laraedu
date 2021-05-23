<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('school_section_id');
            $table->unsignedBigInteger('school_class_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('school_section_id')->references('id')->on('school_sections')->onDelete('cascade');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['school_section_id','school_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections_classes');
    }
}
