<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('users')->insert([
            'email' => 'user@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 2,
            'first_name' => 'Nguyễn',
            'last_name' => 'Anh Kiệt',
            'gender_id' => 1,
            'birthday' => '1994-4-30',
            'postcode' => $faker->numberBetween(100000, 990000),
            'address' => 'HCM',
            'phone' => '0123123123',
            'last_access' => Carbon::now()->format('Y-m-d H:i:s'),
            'attempt' => 0,
            'activated' => 1,
        ]);
        DB::table('admins')->insert([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 1,
            'first_name' => 'Nguyễn',
            'last_name' => 'Anh Kiệt',
            'last_access' => Carbon::now()->format('Y-m-d H:i:s'),
            'attempt' => 0,
            'activated' => 1,
        ]);
        DB::table('user_types')->insert([
            'type_role_name' => 'Admin'
        ]);
        DB::table('user_types')->insert([
            'type_role_name' => 'User'
        ]);
        DB::table('genders')->insert([
            'gender_name' => 'Male'
        ]);
        DB::table('genders')->insert([
            'gender_name' => 'Female'
        ]);
    }
}
