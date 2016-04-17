<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Post;
use App\Models\Section;
use App\Models\Thread;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class SearchController extends Controller
{
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth",['except' => ['']]);
    }

    /**
     * Searches for relevant results
     * @param Request $request
     * @return array|string
     */
    public function search(Request $request){
        $section = Section::where('id',$request->section_id)->first();
        if(!GeneralController::hasPermissions($section,1)) {
            return "invalid_permissions";
        }
        $course = $section->course;

        $query = $request->search_query;
        //Search Threads
        $threadResults = Thread::search($query)->where('course_id',$course->id)->get();
        //Search Announcements
        $announcementResults = Announcement::search($query)->where('section_id',$section->id)->get();
        //Search Assignments
        $assignmentResults  = Assignment::search($query)->where('section_id',$section->id)->get();

        $responseData = array(
            'threads'           => $threadResults,
            'announcements'     => $announcementResults,
            'assignments'       => $assignmentResults,
        );
        return $responseData;
    }
}
