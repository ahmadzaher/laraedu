<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Permission;
use App\Role;
use Illuminate\Support\Facades\DB;

class TeacherPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $superadmin_permission = [
            ['name' => 'Create Teacher', 'slug' => 'create-teacher'],
            ['name' => 'Edit Teacher', 'slug' => 'edit-teacher'],
            ['name' => 'Delete Teacher', 'slug' => 'delete-teacher'],
            ['name' => 'View Teacher', 'slug' => 'view-teacher'],
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
