<?php

use Illuminate\Database\Seeder;

class PollsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'answer' => 1, 'status' => 0, 'description' => 'Favorite OS', 'section_id' => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('polls')->insert($data);
    }
}
