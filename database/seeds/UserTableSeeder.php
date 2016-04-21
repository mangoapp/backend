<?php

use Illuminate\Database\Seeder;
use \App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'name' => 'TeacherGuy123', 'firstname' => 'Teacher', 'lastname' => 'McTeacherson', 'email' => 'teacher@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'name' => 'SteveJobs15', 'firstname' => 'Steve', 'lastname' => 'Jobs', 'email' => 'steve@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'name' => 'BillGates27', 'firstname' => 'Bill', 'lastname' => 'Gates', 'email' => 'bill@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 4, 'name' => 'ScrumMaster12', 'firstname' => 'Alan', 'lastname' => 'Turing', 'email' => 'alan@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 5, 'name' => 'ShaneMan123', 'firstname' => 'Shane', 'lastname' => 'DeWael', 'email' => 'swdewael@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('users')->insert($data);
    }
}
