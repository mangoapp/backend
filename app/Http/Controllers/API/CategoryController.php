<?php

namespace App\Http\Controllers\API;

use App\Models\AssignmentCategory;
use App\Models\Section;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Validator;

class CategoryController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Returns a list of categories for given section
     * @param Request $request
     * @return string
     */
    public function getSectionCategories(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }
        $categories = AssignmentCategory::where('section_id',$section->id)->get();
        return $categories;
    }

    /**
     * Returns the assignments for the given category
     * @param Request $request
     * @return string
     */
    public  function getCategoryAssignments(Request $request) {
        $section = Section::where('id',$request->section_id)->first();
        if($section == null || GeneralController::hasPermissions($section,1) == false) {
            return "invalid_permissions";
        }
        $category = AssignmentCategory::where('section_id',$request->section_id)->where('id',$request->category_id)->first();

        if($category == null)
            return "invalid_category";

        return $category->assignments;
    }

    /**
     * Creates a new Assignment Category
     * @param Request $request
     * @return array|string
     */
    public function createCategory(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'weight' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $category = new AssignmentCategory;
        $category->name = $request->name;
        $category->weight = $request->weight;
        $category->section_id = $request->section_id;
        $category->default = false;
        $category->save();
        return "success";
    }


    /**
     * Updates an Assignment Category
     * @param Request $request
     * @return array|string
     */
    public function updateCategory(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories,id',
            'name' => 'required',
            'weight' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $category = AssignmentCategory::where('id',$request->id)->first();

        if($category->default == true) {
            return "cannot_modify_default_category";
        }

        $category->name = $request->name;
        $category->weight = $request->weight;
        $category->save();

        return "success";
    }
    /**
     * Deletes an Assignment Category. All assignments in that category will be moved to the default.
     * @param Request $request
     * @return array|string
     */
    public function deleteCategory(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',$request->section_id)->first();
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $category = AssignmentCategory::where('id',$request->id)->first();
        if($category->default == true) {
            return "cannot_modify_default_category";
        }

        $assignmentsToMove = $category->assignments;
        if(count($assignmentsToMove) > 0) {
            Log::debug("Removed category id".$category->id." '".$category->name."' has ".count($assignmentsToMove)." assignments, moving...");
            $defaultCategory = AssignmentCategory::where('section_id',$section->id)->where('default',true)->first();
            if($defaultCategory == null) {
                Log::error("Failed to find default category after deleting category `".$category->name."`in section id".$section->id);
                return "error_missing_default";
            }
            foreach($assignmentsToMove as $assignment) {
                Log::debug("Moving assignment id ".$assignment->id." '".$assignment->name."' back to default category.");
            }
        }
        $category->delete();
        return "success";
    }
}
