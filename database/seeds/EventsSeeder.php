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
            ['id' => 1, 'title' => 'Midterm 1', 'description' => 'First midterm', "section_id" => 1, "begin" => Carbon::now()->addHours(5), "end" => Carbon::now()->addHours(7), "user_id" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
            ['id' => 2, 'title' => 'Midterm 2', 'description' => 'Another midterm', "section_id" => 1, "begin" => Carbon::now()->addDay(1), "end" => Carbon::now()->addDay(2), "user_id" => 1, "created_at" => new DateTime(), "updated_at" => new DateTime()],
        );
        DB::table('events')->insert($data);
    }
}
