<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $superadmin_permission = [
            ['name' => 'Create Student', 'slug' => 'create-student'],
            ['name' => 'Edit Student', 'slug' => 'edit-student'],
            ['name' => 'Delete Student', 'slug' => 'delete-student'],
            ['name' => 'View Student', 'slug' => 'view-student'],
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
