<?php

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            //SETTING THE PRIMARY KEYS
            $table->primary(['role_id','permission_id']);
        });

        // add roles
        $superadmin_permission = [
            ['name' => 'Create User', 'slug' => 'create-user'],
            ['name' => 'Edit User', 'slug' => 'edit-user'],
            ['name' => 'Delete User', 'slug' => 'delete-user'],
            ['name' => 'View User', 'slug' => 'view-user'],

            ['name' => 'Create Role', 'slug' => 'create-role'],
            ['name' => 'Edit Role', 'slug' => 'edit-role'],
            ['name' => 'Delete Role', 'slug' => 'delete-role'],
            ['name' => 'View Role', 'slug' => 'view-role'],

            ['name' => 'Create Class', 'slug' => 'create-class'],
            ['name' => 'Edit Class', 'slug' => 'edit-class'],
            ['name' => 'Delete Class', 'slug' => 'delete-class'],
            ['name' => 'View Class', 'slug' => 'view-class'],

            ['name' => 'Create Section', 'slug' => 'create-section'],
            ['name' => 'Edit Section', 'slug' => 'edit-section'],
            ['name' => 'Delete Section', 'slug' => 'delete-section'],
            ['name' => 'View Section', 'slug' => 'view-section'],
        ];
        DB::table('permissions')->insert($superadmin_permission);

        $superadmin_permission = Permission::all();



        $student_role = new Role();
        $student_role->slug = 'student';
        $student_role->name = 'Student';
        $student_role->save();

        $teacher_role = new Role();
        $teacher_role->slug = 'teacher';
        $teacher_role->name = 'Teacher';
        $teacher_role->save();

        $receptionist_role = new Role();
        $receptionist_role->slug = 'receptionist';
        $receptionist_role->name = 'Receptionist';
        $student_role->save();

        $superadmin_role = new Role();
        $superadmin_role->slug = 'superadmin';
        $superadmin_role->name = 'Super Admin';
        $superadmin_role->save();
        $superadmin_role->permissions()->attach($superadmin_permission);



        $superadmin_role = Role::where('slug','superadmin')->first();

        $superadmin = new User();
        $superadmin->name = 'Ahmad Zaher Khrezaty';
        $superadmin->email = 'admin@admin.com';
        $superadmin->username = 'ahmadkhrezaty';
        $superadmin->number = '+963948842976';
        $superadmin->password = bcrypt('password');
        $superadmin->save();
        $superadmin->roles()->attach($superadmin_role);
        $superadmin->permissions()->attach($superadmin_permission);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_permissions');
    }
}
