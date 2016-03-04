<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(CourseTableSeeder::class);
        $this->call(SectionTableSeeder::class);
        $this->call(ThreadTableSeeder::class);
        $this->call(PostTableSeeder::class);
        $this->call(LikeTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(AnnouncementsTableSeeder::class);

    }
}
