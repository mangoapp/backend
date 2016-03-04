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
            ['id' => 1, 'user_id' => 1, 'section_id' => 1, 'title' => 'Urgent News', 'body' => 'I have urgent news! This is announcement!',  'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 2, 'user_id' => 1, 'section_id' => 1, 'title' => 'Whoops lol', 'body' => 'I forgot you guys all have an exam 2 days from now. Good luck!', 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 3, 'user_id' => 1, 'section_id' => 1, 'title' => 'You all failed', 'body' => 'I just thought I would let you all know!', 'created_at' => new DateTime(), 'updated_at' => new DateTime()]
        );
        DB::table('announcements')->insert($data);
    }
}
