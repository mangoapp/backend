<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Invite;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

    public function quickTest(){
        $user = Auth::user();
        Mail::queue('emails.welcome', ['user' => $user], function ($message) use ($user) {
            $message->from('noreply@mango.com');
            $message->subject("Welcome to Mango DEBUG!");
            $message->to($user->email);
        });
        return "donezo";
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

        Mail::queue('emails.classinvite', ['user' => $user, 'inviteTtoken' => $invite->token, 'course' => $section->course], function ($message) use ($user) {
            $message->from('noreply@mango.com');
            $message->subject("Welcome to Mango!");
            $message->to($user->email);
        });
        Log::debug("Class Invite Token: ".$invite->token);
        return "success";
    }

    public function editRole(Request $request) {

        $validator = Validator::make($request->all(), [
            'section_id' => 'required:exists:sections,id',
            'user_id' => 'required:exists:users,id',
            'role_id' => 'required:exists:roles,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $section = Section::where('id',$request['section_id'])->first();
        $userToChange = User::where('id',$request['user_id'])->first();
        $newRole = Role::where('id',$request['role_id'])->first();

        if(GeneralController::hasPermissions($section,4) == false) {
            return "invalid_permissions";
        }

        //Make sure user isnt updating their own role
        if(Auth::user() == $userToChange) {
            return "invalid_user_self";
        }

        $result = $this->modifyUserRole($newRole,$section,$userToChange);
        return $result;
    }
    /**
     * Assigns a new role to the specified user and notifies them via email.
     * The user must already be enrolled in the course.
     * NOTE: This method does NOT validate, check permissions elsewhere
     */
    private function modifyUserRole(Role $newRole, Section $section, User $user) {
        if(GeneralController::userHasPermissions($user,$section,1) == false)
            return "user_not_enrolled";

        $pivot = RoleUser::where('user_id',$user->id)->where('section_id',$section->id)->first();
        $currentRole = Role::where('id',$pivot->role_id)->first();
        //Make sure user is already in the section with a different role
        if($currentRole == null || $currentRole ->id == $newRole->id)
            return "identical_roles";

        //Update database
        //Eloquent does NOT like to update these 3-way pivot tables since they don't have an ID
        //Run a manual query instead
        $pivot->updateRole($user,$newRole,$section);
        Log::debug("Changed role for user ".$user->email." from ".$currentRole->name." to ".$newRole->name.". ".date("Y-m-d @ H:i"));

        //Email User
        Mail::queue('emails.rolechange', ['user' => $user, 'newRole' => $newRole, 'course' => $section->course, 'section' => $section], function ($message) use ($user) {
            $message->from('noreply@mango.com');
            $message->subject("Mango Role Changed");
            $message->to($user->email);
        });

        return "success";
    }

    /**
     * Deletes a section
     */
    public function deleteSection(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required:exists:sections,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $section = Section::where('id',"=",$request['section_id'])->first();

        //User must be a course admin to delete a section
        if(GeneralController::hasPermissions($section, 4) == false) {
            return "invalid_permissions";
        }

        //Delete all roles linked to this section
        RoleUser::where('section_id',$section->id)->delete();

        //Delete all announcements linked to this section
        Announcement::where('section_id',$section->id)->delete();

        //TODO: Might be a good idea to delete grades, quizzes, etc...at this stage

        //Delete the section
        $section->delete();
        return "success";
    }
}
