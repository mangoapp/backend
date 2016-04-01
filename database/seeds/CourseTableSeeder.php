<?php

use Illuminate\Database\Seeder;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'name' => 'CS 307', 'category' => 'Science', 'active' => 1, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 2, 'name' => 'CS 252', 'category' => 'Math', 'active' => 1, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 3, 'name' => 'SOC 100', 'category' => 'History', 'active' => 1, 'created_at' => new DateTime(), 'updated_at' => new DateTime()]
        );
        DB::table('courses')->insert($data);
    }
}
