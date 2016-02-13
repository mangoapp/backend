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
            ['id' => 1, 'name' => 'CS 307'],
            ['id' => 2, 'name' => 'CS 252'],
            ['id' => 3, 'name' => 'SOC 100']
        );
        DB::table('courses')->insert($data);
    }
}
