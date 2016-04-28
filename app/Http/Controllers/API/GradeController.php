<?php

namespace App\Http\Controllers\API;

use App\Models\Assignment;
use App\Models\AssignmentCategory;
use App\Models\Grade;
use App\Models\Section;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class GradeController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Returns a list of ALL grades for an assignment. Requires TA permissions.
     * @param Request $request
     * @return string
     */
    public function getAssignmentGrades(Request $request) {
        $assignment = Assignment::where('id',$request->assignment_id)->first();
        $section = $assignment->section;
        if($section == null || GeneralController::hasPermissions($section,2) == false) {
            return "invalid_permissions";
        }
        return $assignment->grades;
    }


    /**
     * Returns a list of ALL grades for ALL students in a section. Requires TA permissions.
     * @param Request $request
     * @return string
     */
    public function getAllSectionGrades(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,2) == false) {
            return "invalid_permissions";
        }
        $grades = Grade::where('section_id',$section->id)->get();
        return $grades;
    }

    /**
     * Returns a list of current user's grades for given section
     * @param Request $request
     * @return string
     */
    public function getSectionGrades(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }
        $user = Auth::user();
        $grades = Grade::where('section_id',$section->id)->where('user_id',$user->id)->get();
        return $grades;
    }

    /**
     * Creates an grade for this assignment
     * @param Request $request
     * @return array|string
     */
    public function createGrade(Request $request) {
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer',
            'user_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $assignment = Assignment::where('id',$request->assignment_id)->first();
        if($assignment == null) {
            return "invalid_assignment_id";
        }

        $section = $assignment->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        //Check user recieving grade
        $userToGrade = User::where('id',$request->user_id)->first();
        if(GeneralController::userHasPermissions($userToGrade,$section, 1) == false) {
            return "user_not_in_section";
        }

        //Check that this grade doesn't already exist
        $gradeCheck = Grade::where('assignment_id',$request->assignment_id)->where('user_id',$userToGrade->id)->where('section_id',$assignment->section->id)->first();
        if($gradeCheck != null) {
            //Grade already exists
            return "grade_already_exists";
        }

        $grade = new Grade;
        $grade->score = $request->score;
        $grade->user_id = $userToGrade->id;
        $grade->assignment_id = $request->assignment_id;
        $grade->section_id = $assignment->section->id;
        $grade->created_at = Carbon::now();
        $grade->updated_at = Carbon::now();

        $grade->save();

        NotificationController::sendNotification($userToGrade,$section,"Assignment Graded","Your assignment '".$grade->assignment->title."' has been graded.");

        return "success";
    }

    /**
     * Updates an existing grade for this assignment
     * @param Request $request
     * @return array|string
     */
    public function updateGrade(Request $request) {
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer',
            'user_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $assignment = Assignment::where('id',$request->assignment_id)->first();
        if($assignment == null) {
            return "invalid_assignment_id";
        }

        $section = $assignment->section;
        //Check user recieving grade
        $userToGrade = User::where('id',$request->user_id)->first();
        if(GeneralController::userHasPermissions($userToGrade,$section, 1) == false) {
            return "user_not_in_section";
        }

        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }
        //Check that this grade doesn't already exist
        $grade = Grade::where('user_id',$request->user_id)->where('assignment_id',$request->assignment_id)->first();
        if($grade == null) {
            //Create new grade
            $grade = new Grade;
            $grade->user_id = $request->user_id; //Cant change this once created
        }
        $grade->score = $request->score;
        $grade->assignment_id = $request->assignment_id;
        $grade->section_id = $assignment->section->id;
        $grade->save();
        return "success";
    }

    /**
     * Deletes an existing grade for this assignment
     * @param Request $request
     * @return array|string
     */
    public function deleteGrade(Request $request) {
        $validator = Validator::make($request->all(), [
            'grade_id' => 'required|exists:grades,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $grade = Grade::where('id',$request->grade_id)->first();
        if($grade == null) {
            return "invalid_grade_id";
        }

        $section = $grade->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        //Check that this grade doesn't already exist
       $grade->delete();
        return "success";
    }

    /**
     * Gets grade for specified user
     * @param Request $request
     * @return string|void
     */
    public function getStudentAverage(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null) {
            return "invalid_section_id";
        }

        $student = User::where('id',$request->user_id)->first();
        if($student == null) {
            return "invalid_user_id";
        }

        if(GeneralController::hasPermissions($section,2) == false) {
            return "invalid_permissions";
        }
        if(GeneralController::userHasPermissions($student,$section,1) == false) {
            return "user_not_in_section";
        }

        return $this->calculateWeightedAverage($student,$section);
    }

    /**
     * Gets average for authed user
     * @param Request $request
     * @return string|void
     */
    public function getCurrentStudentAverage(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null) {
            return "invalid_section_id";
        }

        $student = Auth::user();
        if($student == null) {
            return "invalid_user_id";
        }

        if(GeneralController::userHasPermissions($student,$section,1) == false) {
            return "user_not_in_section";
        }

        return $this->calculateWeightedAverage($student,$section);
    }

    /**
     * Gets all averages for all students
     */
    public function getAllAverages(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null) {
            return "invalid_section_id";
        }

        $user = Auth::user();
        if(GeneralController::userHasPermissions($user,$section,2) == false) {
            return "user_not_in_section";
        }

        $sectionUsers = $section->users;
        $dataToReturn = array();
        foreach($sectionUsers as $user) {
            if($user->role($section) != null && $user->role($section)->level == 1) {
                $grade = $this->calculateWeightedAverage($user,$section);
                array_push($dataToReturn,array('user_id' => $user->id, 'average' => $grade));
            }
        }

        return $dataToReturn;


    }

    /**
     * Returns the current weighted average of a student in a section
     * @param User $user
     * @param Section $section
     */
    public function calculateWeightedAverage(User $user, Section $section) {

        $categoriesInSection = AssignmentCategory::where('section_id',$section->id)->get();
        $weightArray = array();
        $totalWeight = 0;
        foreach($categoriesInSection as $category) {
            $maxCategoryPoints = 0;
            $earnedCategoryPoints = 0;

            $assignmentsInCategory = Assignment::where('section_id',$section->id)->where('category_id',$category->id)->get();
            foreach($assignmentsInCategory as $assignment) {
                $userGrade = Grade::where('assignment_id',$assignment->id)->where('user_id',$user->id)->first();
                if($userGrade != null) {
                    $maxCategoryPoints += $assignment->maxScore;
                    $earnedCategoryPoints += $userGrade->score;
                } else {
                    //This assignment is not graded yet
                }
            };
            if($maxCategoryPoints > 0) {
                $categoryPercentage = ($earnedCategoryPoints/$maxCategoryPoints)*100;
                array_push($weightArray,array('percentage' => $categoryPercentage, 'weight' => $category->weight));
                $totalWeight += $category->weight;
            }
        }

        $finalGrade = 0;
        if($totalWeight == 0) {
            foreach($weightArray as $weightedCateogry) {
                $finalGrade += ($weightedCateogry['percentage']);
            }
        } else {
            foreach($weightArray as $weightedCateogry) {
                $relativeWeight = $weightedCateogry['weight']/$totalWeight;
                $finalGrade += ($weightedCateogry['percentage']*$relativeWeight);
            }
        }
        return $finalGrade;
    }
}
