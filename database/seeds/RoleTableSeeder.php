<?php

use Illuminate\Database\Seeder;


class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'name' => 'student', 'display_name' => 'Student', 'description' => 'Student enrolled in course', "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'name' => 'ta', 'display_name' => 'Teaching Assistant', 'description' => 'Teaching Assistant', "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'name' => 'prof', 'display_name' => 'Professor', 'description' => 'Professor', "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 4, 'name' => 'course_admin', 'display_name' => 'Course Administrator', 'description' => 'Adminstrative User', "created_at" => new DateTime(), "updated_at" => new DateTime()]
        );
        DB::table('roles')->insert($data);
    }
}
