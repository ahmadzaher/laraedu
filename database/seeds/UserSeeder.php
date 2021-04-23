<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $gender = $faker->randomElement(['male', 'female']);


        foreach (range(1,20) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name($gender),
                'username' => $faker->username,
                'email' => $faker->email,
                'number' => $faker->phoneNumber,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
