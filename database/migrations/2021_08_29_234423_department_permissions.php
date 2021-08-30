<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Permission;
use App\Role;
use Illuminate\Support\Facades\DB;

class DepartmentPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $superadmin_permission = [
            ['name' => 'Create Department', 'slug' => 'create-department'],
            ['name' => 'Edit Department', 'slug' => 'edit-department'],
            ['name' => 'Delete Department', 'slug' => 'delete-department'],
            ['name' => 'View Department', 'slug' => 'view-department'],
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
