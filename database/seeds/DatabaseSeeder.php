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
            'name' => 'Nguyễn Linh Chân',
            'email' => 'linhchannguyen94@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 1,
            'last_access' => Carbon::now()->format('Y-m-d H:i:s'),
            'attempt' => 0,
            'activated' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'Nguyễn Anh Kiệt',
            'email' => 'nguyenanhkiet@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 2,
            'last_access' => Carbon::now()->format('Y-m-d H:i:s'),
            'attempt' => 0,
            'activated' => 1,
        ]);
        DB::table('user_types')->insert([
            'role' => 'Admin'
        ]);
        DB::table('user_types')->insert([
            'role' => 'User'
        ]);
    }
}
