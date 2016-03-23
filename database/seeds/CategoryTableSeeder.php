<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'name' => 'Default', 'default' => true, 'weight' => 0, 'section_id' => 1],
            ['id' => 2, 'name' => 'Default', 'default' => true, 'weight' => 0, 'section_id' => 2],
            ['id' => 3, 'name' => 'Default', 'default' => true, 'weight' => 0, 'section_id' => 3],
            ['id' => 4, 'name' => 'Default', 'default' => true, 'weight' => 0, 'section_id' => 4],
        );
        DB::table('categories')->insert($data);
    }
}
