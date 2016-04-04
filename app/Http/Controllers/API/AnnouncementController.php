<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use App\Models\Section;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Auth;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Creates a new announcement with the specified details
     */
    public function createAnnouncement(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
            'section_id' => 'required|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        //Check user auth level
        $section = Section::where('id',"=",$request['section_id'])->first();
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }
        $user = Auth::user();
        $announcement = new Announcement;
        $announcement->title = $request['title'];
        $announcement->body = $request['body'];
        $announcement->section_id = $section->id;
        $announcement->user_id = $user->id;
        $announcement->save();

        //Email all users in that section
        $usersList = $section->users;
        foreach($usersList as $userToSend) {
            NotificationController::sendNotification($userToSend,$section,$announcement->title,"An instructor has made an announcement in ".$section->course->name);
            Mail::queue('emails.announcements', ['user' => $userToSend,'announcement' => $announcement], function ($message) use ($userToSend,$section,$announcement) {
                $message->from('noreply@mango.com');
                $message->subject($section->course->name.": ".$announcement->title);
                $message->to($userToSend->email);
            });
        }
        return("success");
    }

    /**
     * Edits an announcement
     */
    public function editAnnouncement(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
            'announcement_id' => 'required:exists:announcements,id',
            'section_id' => 'required|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $section = Section::where('id',"=",$request['section_id'])->first();
        if(GeneralController::hasPermissions($section, 2)) {
            $announcement = Announcement::where('id', '=', $request['announcement_id'])->first();
            $announcement->title = $request['title'];
            $announcement->body = $request['body'];
            $announcement->save();
            return "success";
        }
        return "no permissions";
    }

    /**
     * Deletes an announcement
     */
    public function deleteAnnouncement(Request $request) {
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required:exists:announcements,id',
            'section_id' => 'required|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $section = Section::where('id',"=",$request['section_id'])->first();
        if(GeneralController::hasPermissions($section, 2)) {
            $announcement = Announcement::where('id', '=', $request['announcement_id'])->first();
            if($announcement) {
                $announcement->delete();
                return("success");
            }
            else {
                return("announcement doesn't exist");
            }
        }
        return "invalid";
    }

    /**
     * Returns a list of all announcements for a particular section
     */
    public function getAnnouncements(Request $request) {
        if(Section::where('id','=', $request->section_id)->count() != 1) {
            return "section doesn't exist";
        }
        
        $section = Section::where('id',"=", $request->section_id)->first();
        if(GeneralController::hasPermissions($section, 1)) {
            $announcements = Announcement::where('section_id',$section->id)->get();
            return $announcements;
        }
        return "invalid permissions";
    }
}
