<?php

namespace App\Http\Controllers\API;

use App\Models\Assignment;
use App\Models\AssignmentCategory;
use App\Models\Section;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class AssignmentController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Returns a list of existing assignments.
     * @param Request $request
     * @return string
     */
    public function getAssignments(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }
        return $section->assignments;
    }

    /**
     * Creates an assignment for this section
     * @param Request $request
     * @return array|string
     */
    public function createAssignment(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'filesubmission' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'max_score' => 'required|integer'
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
        $assignment = new Assignment;
        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->maxScore = $request->max_score;
        $assignment->filesubmission = $request->filesubmission;
        $assignment->section_id = $section->id;
        $assignment->category_id = $category->id;


        //Assign deadline if specified
        if($request->has('deadline')) {
            $assignment->deadline = Carbon::createFromFormat('Y-m-d H:i',$request->deadline);
        }
        else {
            $assignment->deadline = null;
        }

        $assignment->save();
        return "success";
    }


    /**
     * Updates the spcified assignment
     * @param Request $request
     * @return array|string
     */
    public function updateAssignment(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:assignments,id',
            'title' => 'required',
            'description' => 'required',
            'filesubmission' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'max_score' => 'required|integer'
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


        $assignment = Assignment::where('id',$request->id)->first();

        if($assignment == null)
            return "invalid assignment id";

        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->maxScore = $request->max_score;
        $assignment->filesubmission = $request->filesubmission;
        $assignment->section_id = $section->id;
        $assignment->category_id = $category->id;

        //Assign deadline if specified
        if($request->has('deadline')) {
            $assignment->deadline = Carbon::createFromFormat('Y-m-d H:i',$request->deadline);
        }
        else {
            $assignment->deadline = null;
        }

        $assignment->save();


        return "success";
    }


    /**
     * Deletes the given assignment
     * @param Request $request
     * @return array|string
     */
    public function deleteAssignment(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:assignments,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $assignment = Assignment::where('id',$request->id)->first();
        if($assignment == null)
            return "invalid assignment id";

        //Check user auth level
        $section = $assignment->section;
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $assignment->delete();
        return "success";
    }

}
