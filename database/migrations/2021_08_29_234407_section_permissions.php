<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Permission;
use App\Role;
use Illuminate\Support\Facades\DB;

class SectionPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $superadmin_permission = [
            ['name' => 'Create Section', 'slug' => 'create-section'],
            ['name' => 'Edit Section', 'slug' => 'edit-section'],
            ['name' => 'Delete Section', 'slug' => 'delete-section'],
            ['name' => 'View Section', 'slug' => 'view-section'],
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
