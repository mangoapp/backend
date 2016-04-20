<?php

use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'body' => 'This is really bad. I talked to my TA and he says it means you have a virus.',
                'thread_id' => 1, 'user_id' => 3, 'reply_id' => 0, 'anonymous' => true, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'body' => 'Have you tried turning it off and then back on again?',
                'thread_id' => 1, 'user_id' => 4, 'reply_id' => 0, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'body' => 'Wow. I for one think windows machines are great!',
                'thread_id' => 3, 'user_id' => 3, 'reply_id' => 0, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 4, 'body' => 'Thanks, I\'m really glad you made this thread a sticky!',
                'thread_id' => 4, 'user_id' => 2, 'reply_id' => 2, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('posts')->insert($data);
    }
}
