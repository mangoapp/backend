<?php namespace App\Http\Controllers\API;

use App\Models\Course;
use App\Models\Section;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Auth;

class ForumController extends Controller {

    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth",['except' => ['']]);
    }

    public function allThreads(Request $request) {
        $section = Section::find($request->section_id);
        if($section == null) {
            return "no such section";
        }

        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }
        return Course::with("threads.user")->find($section->course->id);
    }
    public function getPosts(Request $request) {
        $section = Section::find($request->section_id);
        if($section == null) {
            return "no such section";
        }

        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }
        $thread = Thread::with('posts.user')->find($request->thread_id);
        return $thread;
    }
}