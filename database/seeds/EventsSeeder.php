<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'title' => 'Midterm 1', 'description' => 'First midterm', "section_id" => 1, "begin" => Carbon::today()->addHours(20), "end" => Carbon::today()->addHours(22), "user_id" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'title' => 'Midterm 2', 'description' => 'Another midterm', "section_id" => 1, "begin" => Carbon::tomorrow()->addHours(14), "end" => Carbon::tomorrow()->addHours(16), "user_id" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 3, 'title' => 'Review Session', 'description' => 'Review Session', "section_id" => 2, "begin" => Carbon::yesterday()->addHours(10), "end" => Carbon::yesterday()->addHours(15), "user_id" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 4, 'title' => 'Movie Night', 'description' => 'Movie Session', "section_id" => 3, "begin" => Carbon::tomorrow()->addHours(21), "end" => Carbon::tomorrow()->addHours(23), "user_id" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('events')->insert($data);
    }
}
