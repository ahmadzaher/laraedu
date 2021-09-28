<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExamGradePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $superadmin_permission = [
            ['name' => 'Create Exam Grade', 'slug' => 'create-exam-grade'],
            ['name' => 'Edit Exam Grade', 'slug' => 'edit-exam-grade'],
            ['name' => 'Delete Exam Grade', 'slug' => 'delete-exam-grade'],
            ['name' => 'View Exam Grade', 'slug' => 'view-exam-grade'],
        ];
        DB::table('permissions')->insert($superadmin_permission);
        $superadmin_permission = Permission::all();

        $superadmin_role = Role::where('slug', 'superadmin')->first();
        $superadmin_role->permissions()->sync($superadmin_permission);
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
