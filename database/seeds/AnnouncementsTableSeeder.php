<?php

use Illuminate\Database\Seeder;

class AnnouncementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'user_id' => 1, 'section_id' => 1, 'title' => 'Good news, Everyone!', 'body' => 'We will be having a surprise midterm tomorrow! I hope you guys all studied!',  'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 2, 'user_id' => 1, 'section_id' => 1, 'title' => 'Exam Graded', 'body' => 'The good news is that I finished grading the exam! The bad news is that the average was a 17%. The ugly news is that I still won\'t curve it.', 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 3, 'user_id' => 1, 'section_id' => 1, 'title' => 'Homework Problems', 'body' => 'Some people have been saying the homework is too hard. Therefore, I decided that everyone will get an automatic A!', 'created_at' => new DateTime(), 'updated_at' => new DateTime()]
        );
        DB::table('announcements')->insert($data);
    }
}
