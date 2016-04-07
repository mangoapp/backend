<?php

use Illuminate\Database\Seeder;

class AssignmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id' => 1, 'section_id' => 1, 'category_id' => 1, 'quiz' => false, 'data' => null, 'title' => 'Project Charter', 'description' => 'We will take off 1 point no matter what.', 'filesubmission' => 1, 'maxScore' => 25, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 2, 'section_id' => 1, 'category_id' => 1, 'quiz' => false, 'data' => null, 'title' => 'Project Backlog', 'description' => 'Make sure to include way more tasks than you can actually do.', 'filesubmission' => 1, 'maxScore' => 50, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 3, 'section_id' => 1, 'category_id' => 1, 'quiz' => false, 'data' => null, 'title' => 'Sprint Doc 1', 'description' => 'Make sure you actually finish these.', 'filesubmission' => 0, 'maxScore' => 75, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 4, 'section_id' => 2, 'category_id' => 2, 'quiz' => false, 'data' => null, 'title' => 'Sprint Doc 2', 'description' => 'Make sure you actually finish these.', 'filesubmission' => 0, 'maxScore' => 75, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 5, 'section_id' => 1, 'category_id' => 1, 'quiz' => true, 'data' => '[

      {
         "question": "string",
         "answers":[
            "a",
            "b",
            "c",
            "d"
         ],
         "correctAnswer":1
      },
      {
      "question": "string2",
         "answers":[
            "a",
            "b",
            "c",
            "d"
         ],
         "correctAnswer":1
      },
      {
      "question": "string3",
         "answers":[
            "a",
            "b",
            "c",
            "d"
         ],
         "correctAnswer":2
      }
]', 'title' => 'First Quiz', 'description' => 'Make sure you Pass!', 'filesubmission' => 0, 'maxScore' => 125, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
        );
        DB::table('assignments')->insert($data);

    }
}
