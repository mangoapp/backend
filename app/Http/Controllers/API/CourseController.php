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
use Mail;
use Mockery\CountValidator\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Auth;

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
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:courses',
            'section_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $course = new Course;
        $course->name= $request['name'];
        $course->save();

        //Create a new section
        $section = new Section;
        $section->name = $request['section_name'];
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

    public function createSection(Request $request) {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'section_name' => 'required|unique:sections,name',
            'course_id' => 'required|exists:courses,id'
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }


        //Get the 1st section in the course
        $course = Course::where('id',$request['course_id'])->first();
        if($course == null || $course->sections[0] == null){
            return "invalid_course";
        }

        //Course admins have permissions across every section, so just check the first
        if(!GeneralController::hasPermissions($course->sections[0],4)) {
            return "invalid_permissions";
        }

        //Create a new section for the same course
        $section = new Section;
        $section->name = $request['section_name'];
        $section->course_id = $request['course_id'];
        $section->save();


        $pivot = new RoleUser;
        $pivot->section_id = $section->id;
        $pivot->user_id = $user->id;
        $profRole = Role::where('name', '=', 'course_admin')->first();
        $pivot->role_id = $profRole->id;
        $pivot->save();

        return "success";
    }

    /**
     * Invites the user to join the course.
     * The user is not added to the course
     * until they accept the invitation.
     */
    public function addUserToCourse(Request $request) {
		//Get user who made the request
    	$requestingUser = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'sectionid' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $section = Section::where('id',$request['sectionid'])->get()->first();
        $userToAdd = User::where('email',$request['email'])->get()->first();

        //Check that the new user is not already in that section
        if($userToAdd->role($section) != null) {
            //User already in section
            return "user_already_added";
        }

        //Check permissions (TA or better)
        if(!GeneralController::hasPermissions($section,2)) {
            return "invalid_permissions";
        }

        //Send the new user an invitation
        $studentRole = Role::where('name', 'student')->get()->first();
        $this->inviteUserToSection($studentRole,$section,$userToAdd);
        return "success";
    }

    public function acceptInvite(Request $request) {
        //Get user who made the request
        $user = Auth::user();

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
            //Section doesn't exist anymore
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

        Mail::send('emails.classinvite', ['user' => $user, 'inviteTtoken' => $invite->token, 'course' => $section->course], function ($message) use ($user) {
            $message->from('noreply@mango.com');
            $message->subject("Welcome to Mango!");
            $message->to($user->email);
        });
        Log::debug("Class Invite Token: ".$invite->token);
        return "success";
    }
}
