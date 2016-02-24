<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth",['except' => ['requestPasswordReset','confirmPasswordReset']]);
    }

    /**
     * Creates a new announcement with the specified details
     */
    public function createAnnouncement(Request $request) {
        //Get user who made the request
        $user = JWTAuth::parseToken()->toUser();
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
            'sectionID' => 'required|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',"=",$request['sectionID'])->first();
        if($section == null || $user->role($section) == null || $user->role($section)->level < 2) {
            //No Auth
            return("invalid_permissions");
        };

        $announcement = new Announcement;
        $announcement->title = $request['title'];
        $announcement->body = $request['body'];
        $announcement->section_id = $section->id;
        $announcement->user_id = $user->id;
        $announcement->save();

        return("success");

    }

    /**
     * Returns a list of all announcements for a particular section
     */
    public function getAnnouncements(Request $request) {
        //Get user who made the request
        $user = JWTAuth::parseToken()->toUser();
        $validator = Validator::make($request->all(), [
            'sectionID' => 'required|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check that user is enrolled
        $section = Section::where('id',"=",$request['sectionID'])->first();
        if($section == null || $user->role($section) == null || $user->role($section)->level < 1) {
            //No Auth
            return("invalid_permissions");
        };

        $announcements = Announcement::where('section_id',$section->id)->get();
        return($announcements);

    }

}
