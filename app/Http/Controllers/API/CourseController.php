<?php

namespace App\Http\Controllers\API;

use App\Models\Course;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class CourseController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Creates a new course and section
     * @param Request $request
     * @return string
     */
    public function createCourse(Request $request) {
        //Get user who made the request
        $user = JWTAuth::parseToken()->toUser();
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:courses',
            'sectionName' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            $course = new Course;
            $course->name= $request['name'];
            $course->save();

            //Create a new section
            $section = new Section;
            $section->name = $request['sectionName'];
            $section->course_id = $course->id;
            $section->save();

            //Assign admin rights to creator
            $pivot = new RoleUser;
            $pivot->section_id = $section->id;
            $pivot->user_id = $user->id;
            $profRole = Role::where('name', '=', 'prof')->first();
            $pivot->role_id = $profRole->id;
            $pivot->save();

            return "success";
        }
    }

    public function addUserToCourse(Request $request) {
        //Get user who made the request
        $requestingUser = JWTAuth::parseToken()->toUser();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'sectionid' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        //Check that section is valid
        $section = Section::where('id',$request['sectionid'])->get()->first();
        if($section == null)
            return "invalid_section";
        //Check that email is valid
        $userToAdd = User::where('email',$request['email'])->get()->first();
        iF($userToAdd == null)
            return "invalid_email";

        //Check that user is not already in that section
        if($userToAdd->role($section) != null) {
            //User already in section
            return "user_already_added";
        }
        //TODO: Need a better way to handle permissions by role
        $requesterRole = $requestingUser->role($section);
        if($requesterRole != null && ($requesterRole->name == "ta" || $requesterRole->name == "prof" || $requesterRole->name == "course_admin")) {
            //Assign admin rights to creator
            $pivot = new RoleUser;
            $pivot->section_id = $section->id;
            $pivot->user_id = $userToAdd->id;
            $profRole = Role::where('name', '=', 'student')->first();
            $pivot->role_id = $profRole->id;
            $pivot->save();
            return "success";
        } else {
            return "invalid_permissions";
        }
    }
}
