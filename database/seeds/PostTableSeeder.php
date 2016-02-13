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
            ['id' => 1, 'body' => 'This is really bad. I talked to my TA and he says it means your breadboard is haunted. I suggest you get a new one ASAP.',
                'thread_id' => 1, 'user_id' => 2, 'reply_id' => 0, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'body' => 'I mean its college, you don\'t even have to take exams if you don\'t want to.',
                'thread_id' => 2, 'user_id' => 2, 'reply_id' => 0, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'body' => 'Thanks, that clears things up. I\'m going back to bed then.',
                'thread_id' => 2, 'user_id' => 1, 'reply_id' => 2, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 4, 'body' => 'I just want to learn about sociology!',
                'thread_id' => 3, 'user_id' => 3, 'reply_id' => 0, 'anonymous' => false, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('posts')->insert($data);
    }
}
