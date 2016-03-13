<?php

namespace App\Http\Controllers\API;

use App\Models\Quiz;
use App\Models\AssignmentCategory;
use App\Models\Section;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class QuizController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Returns a list of existing quizzes.
     * @param Request $request
     * @return string
     */
    public function getQuizzes(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }
        return $section->quizzes;
    }

    /**
     * Creates a quiz for this section
     * @param Request $request
     * @return array|string
     */
    public function createQuiz(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'data' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        //Check that category is present for this section
        $category = AssignmentCategory::where('id',$request->category_id)->first();
        if($category->section_id != $section->id) {
            return "invalid category";
        }
        $quiz = new Quiz;
        $quiz->title = $request->title;
        $quiz->data = $request->data;
        $quiz->section_id = $section->id;
        $quiz->category_id = $category->id;

        //Assign deadline if specified
        if($request->has('deadline')) {
            $quiz->deadline = Carbon::createFromFormat('Y-m-d H:i',$request->deadline);
        }
        else {
            $quiz->deadline = null;
        }

        $quiz->save();
        return "success";
    }
}
