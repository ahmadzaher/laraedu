<?php

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
}
