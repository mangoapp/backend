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
            ['id' => 1, 'name' => 'CoolGuy17', 'firstname' => 'Buster', 'lastname' => 'Dunsmore', 'email' => 'buster@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'name' => 'LannisterGuy23', 'firstname' => 'Tyrion', 'lastname' => 'Lannister', 'email' => 'tyrion@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'name' => 'ScrumMaster99', 'firstname' => 'Bill', 'lastname' => 'Gates', 'email' => 'bill@gmail.com',
                'password' => Hash::make("password123"), "remember_token" => "", "uuid" => User::v4(), "created_at" => new DateTime(), "updated_at" => new DateTime()]
        );
        DB::table('users')->insert($data);
    }
}
