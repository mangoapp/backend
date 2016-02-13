<?php

use Illuminate\Database\Seeder;

class LikeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //FIXME: Need to find a better way of handing post_id/thread_id. One of them should be null, or we need a 3rd field to say which we want to use.
        $data = array(
            ['id' => 1, 'thread_id' => 1, 'post_id' => 1, 'user_id' => 2, "vote" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'thread_id' => 2, 'post_id' => 2, 'user_id' => 3, "vote" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'thread_id' => 1, 'post_id' => 2, 'user_id' => 3, "vote" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('likes')->insert($data);
    }
}
