<?php

namespace App\Http\Controllers\API;

use App\Models\Course;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Section;
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
     * @param Request $request
     */
    public function createCourse(Request $request) {
        //Get user who made the request
        $user = $user = JWTAuth::parseToken()->toUser();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
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

            return $course;
        }
    }
}
