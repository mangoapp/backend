<?php

use Illuminate\Database\Seeder;

class SectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'name' => 'LE 307-1', 'course_id' => 1],
            ['id' => 2, 'name' => 'LE 307-2', 'course_id' => 1],
            ['id' => 3, 'name' => 'LE 252-1', 'course_id' => 2],
            ['id' => 4, 'name' => 'LE 100-1', 'course_id' => 3],
        );
        DB::table('sections')->insert($data);
    }
}
