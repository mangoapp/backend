<?php

use Illuminate\Database\Seeder;


class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'name' => 'Math'],
            ['id' => 2, 'name' => 'Computer Science'],
            ['id' => 3, 'name' => 'Philosophy'],
            ['id' => 4, 'name' => 'English'],
            ['id' => 5, 'name' => 'Biology'],
            ['id' => 6, 'name' => 'History'],
        );
        DB::table('types')->insert($data);
    }
}
