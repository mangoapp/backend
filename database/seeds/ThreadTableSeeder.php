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
            ['id' => 1, 'title' => 'My Breadboard is broken!!1','body' => 'Please help me! My breadboard only works if I hold it perpendicular to the ground!',
                'course_id' => 1, 'user_id' => 1, 'anonymous' => false, 'sticky' => false, 'locked' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'title' => 'Do I have to attend lecture to pass?','body' => 'Its like 0 degrees outside. Am I really required to attend lecture in order to pass?',
                'course_id' => 1, 'user_id' => 1, 'anonymous' => false, 'sticky' => false, 'locked' => true, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'title' => 'Dear Professor. PLEASE use powerpoint.','body' => 'Stop printing your lectures out on paper. Im begging you, please use powerpoint!',
                'course_id' => 3, 'user_id' => 2, 'anonymous' => false, 'sticky' => false, 'locked' => true, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('threads')->insert($data);
    }
}
