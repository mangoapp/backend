<?php

use Illuminate\Database\Seeder;

class ThreadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'title' => 'Problem with Lab','body' => 'Please help me! For some reason when I run the test cases, I always get a 0!',
                'course_id' => 1, 'user_id' => 3, 'anonymous' => false, 'sticky' => false, 'locked' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'title' => 'Do I have to attend lecture to pass?','body' => 'Its like 0 degrees outside. Am I really required to attend lecture in order to pass?',
                'course_id' => 1, 'user_id' => 4, 'anonymous' => false, 'sticky' => false, 'locked' => true, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'title' => 'Macs, please!','body' => 'These lab machines are all on windows. I refuse to use them. Please get us some mac lab machines.',
                'course_id' => 1, 'user_id' => 2, 'anonymous' => false, 'sticky' => false, 'locked' => true, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 4, 'title' => 'Important Update','body' => 'This is an important update! Therefore I decided to sticky this thread so it can be seen easily!',
                'course_id' => 1, 'user_id' => 1, 'anonymous' => false, 'sticky' => true, 'locked' => true, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('threads')->insert($data);
    }
}
