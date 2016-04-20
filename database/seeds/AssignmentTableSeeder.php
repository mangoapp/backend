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
            ['id' => 1, 'section_id' => 1, 'category_id' => 1, 'deadline' => new DateTime('tomorrow + 1day'), 'quiz' => false, 'data' => null, 'title' => 'Project Charter', 'description' => 'Please submit your project charter.', 'filesubmission' => 1, 'maxScore' => 25, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 2, 'section_id' => 1, 'category_id' => 1, 'deadline' => new DateTime('tomorrow + 1day'), 'quiz' => false, 'data' => null, 'title' => 'Lab 1', 'description' => 'Write your own kernel from scratch!', 'filesubmission' => 0, 'maxScore' => 100, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 3, 'section_id' => 1, 'category_id' => 1, 'deadline' => new DateTime('tomorrow + 1day'), 'quiz' => false, 'data' => null, 'title' => 'Sprint Doc 1', 'description' => 'Sprint 1 user stories.', 'filesubmission' => 1, 'maxScore' => 75, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 4, 'section_id' => 2, 'category_id' => 2, 'deadline' => new DateTime('tomorrow + 1day'), 'quiz' => false, 'data' => null, 'title' => 'Sprint Doc 2', 'description' => 'Sprint 2 user stories.', 'filesubmission' => 1, 'maxScore' => 75, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
            ['id' => 5, 'section_id' => 1, 'category_id' => 1, 'deadline' => new DateTime('tomorrow + 1day'), 'quiz' => true, 'data' => '[

      {
         "question": "Which class is the best",
         "answers":[
            "Calculus",
            "CS 307",
            "English",
            "History"
         ],
         "correctAnswer":1
      },
      {
      "question": "Which is the most hip?",
         "answers":[
            "PHP",
            "Node",
            "C#",
            "Java"
         ],
         "correctAnswer":1
      },
      {
      "question": "Which platform is the best?",
         "answers":[
            "Blackboard",
            "Mango!",
            "Canvas",
            "WebAssign"
         ],
         "correctAnswer":1
      }
]', 'title' => 'First Quiz', 'description' => 'Make sure you Pass!', 'filesubmission' => 0, 'maxScore' => 125, 'created_at' => new DateTime(), 'updated_at' => new DateTime()],
        );
        DB::table('assignments')->insert($data);

    }
}
