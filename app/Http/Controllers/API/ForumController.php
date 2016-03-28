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
    public function createThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'title' => 'required',
            'body' => 'required',
            'anonymous' => 'required|integer',
            'sticky' => 'integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
    	$user = Auth::user();
		$section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }

        $thread = new Thread;
        $thread->title = $request->title;
        $thread->body = $request->body;
        $thread->course_id = $section->course->id;
        $thread->user_id = $user->id;
        $thread->anonymous = $request->anonymous;

        if(GeneralController::hasPermissions($section, 2) == false && $request->has('sticky')) {
        	$thread->sticky = 1;
        }
        $thread->save();
        return "success";
    }
    public function updateThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
            'title' => 'required',
            'body' => 'required',
            'anonymous' => 'required|integer',
            'sticky' => 'integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
    	$user = Auth::user();
		$section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }

        $thread = Thread::where('id', '=', $request->thread_id);

        // lol
        if($thread->count()) {
        	$thread = $thread->first();
        	if($thread->user_id != $user->id && GeneralController::hasPermissions($section, 2) == false) {
        		return "invalid permissions (2)";
        	}
	        $thread->title = $request->title;
	        $thread->body = $request->body;
	        $thread->anonymous = $request->anonymous;

	        if(GeneralController::hasPermissions($section, 2) == false && $request->has('sticky')) {
	        	$thread->sticky = 1;
	        }
	        $thread->save();
	        return "success";
	    }
	    else {
	    	return "no such thread";
	    }
    }
    public function deleteThread(Request $request) {
		$validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
    	$user = Auth::user();
		$section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }

        $thread = Thread::where('id', '=', $request->thread_id);

        // lol
        if($thread->count()) {
        	$thread = $thread->first();
        	if($thread->user_id != $user->id && GeneralController::hasPermissions($section, 2) == false) {
        		return "invalid permissions (2)";
        	}
	        $thread->delete();
	        return "success";
	    }
	    else {
	    	return "no such thread";
	    }    	
    }

    public function createPost(Request $request) {
       $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
            'body' => 'required',
            'anonymous' => 'required|integer',
            'reply_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }

        $post = new Post;
        $post->body = $request->body;
        $post->user_id = $user->id;
        $post->thread_id = $request->thread_id;
        $post->anonymous = $request->anonymous;
        $post->save();

        return "success";  
    }

    public function updatePost(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'post_id' => 'required|exists:posts,id',
            'body' => 'required',
            'anonymous' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }

        $post = Post::where('id', '=', $request->post_id);

        // lol
        if($post->count()) {
            $post = $post->first();
            if($post->user_id != $user->id && GeneralController::hasPermissions($section, 2) == false) {
                return "invalid permissions (2)";
            }
            $post->body = $request->body;
            $post->anonymous = $request->anonymous;
            $post->save();
            return "success";
        }
        else {
            return "no such post";
        }    
    }

    public function deletePost(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }

        $post = Post::where('id', '=', $request->post_id);

        if($post->count()) {
            $post = $post->first();
            if($post->user_id != $user->id && GeneralController::hasPermissions($section, 2) == false) {
                return "invalid permissions (2)";
            }
            $post->delete();
            return "success";
        }
        else {
            return "no such post";
        }       
    }

}