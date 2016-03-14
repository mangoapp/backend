<?php

use Illuminate\Database\Seeder;

class GradeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'section_id' => 1, 'assignment_id' => 1, 'user_id' => 1, 'score' => 20, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 2, 'section_id' => 1, 'assignment_id' => 1, 'user_id' => 2, 'score' => 10, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 3, 'section_id' => 1, 'assignment_id' => 2, 'user_id' => 1, 'score' => 7, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
        );
        DB::table('grades')->insert($data);
    }
}
