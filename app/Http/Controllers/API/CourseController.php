<?php

namespace App\Http\Controllers\API;

use App\Models\Course;
use App\Models\Invite;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
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
            $profRole = Role::where('name', '=', 'course_admin')->first();
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
        $authLevel = $requestingUser->role($section)->level;
        if($authLevel  >= 2) {
            $studentRole = Role::where('name', 'student')->get()->first();
            $this->inviteUserToSection($studentRole,$section,$userToAdd);
            return "success";
        } else {
            return "invalid_permissions";
        }
    }

    public function acceptInvite(Request $request) {
        //Get user who made the request
        $user = JWTAuth::parseToken()->toUser();

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Make sure password reset token is valid
        $invite = Invite::where('user_id',$user->id)->where('token',$request['token'])->first();
        if($invite == null) {
            return "invalid_token";
        }

        //Check that user is not already in that section
        $section = Section::where('id',$invite->section_id)->get()->first();
        if($section == null) {
            //Section doesnt exist anymore
            return "invalid_section";
        }
        if($user->role($section) != null) {
            //User already in section
            return "user_already_added";
        }

        //Add user to section
        $pivot = new RoleUser;
        $pivot->section_id = $invite->section_id;
        $pivot->user_id = $invite->user_id;
        $pivot->role_id = $invite->role_id;
        $pivot->save();
        return "success";
    }

    private function inviteUserToSection(Role $role, Section $section, User $user) {
        //Make sure user was not already invited
        $invite = Invite::where('user_id',$user->id)->first();
        if($invite != null) {
            return "success";
        }
        //Generate new invite
        $invite = new Invite;
        $invite->user_id = $user->id;
        $invite->role_id = $role->id;
        $invite->section_id = $section->id;
        $invite->token = str_random(100);
        $invite->save();

        //FIXME: Email token back
        //          Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
        //  			$message->from('hello@mango.org', 'Welcome');
        //  			$message->to($user->email);
        // });
        Log::debug("Class Invite Token: ".$invite->token);
        return "success";
    }
}
