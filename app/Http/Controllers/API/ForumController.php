<?php namespace App\Http\Controllers\API;

use App\Models\Course;
use App\Models\Like;
use App\Models\Section;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
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
        // return Course::with("threads.user")->orderBy('created_at', 'asc')->find($section->course->id);
		return Course::with(
		 	['threads' => function($query) {
		 		$query->orderBy('created_at', 'asc');
		 	},'threads.user']
		)->orderBy('created_at', 'asc')->find($section->course->id);
    }
    public function getPosts(Request $request) {
        $section = Section::find($request->section_id);
        if($section == null) {
            return "no such section";
        }

        if(GeneralController::hasPermissions($section, 1) == false) {
            return "invalid permissions";
        }
        // $thread = Thread::with('posts.user.roles')->with('posts.likes')->find($request->thread_id);
		$arr = Thread::with(
		 	['posts' => function($query) {
		 		$query->orderBy('created_at', 'desc');
		 	},'posts.user.roles', 'posts.likes']
		)->with('user')->orderBy('created_at', 'asc')->find($request->thread_id)->toArray();
        foreach($arr['posts'] as $key => $post) {
            $p = Post::find($post['id']);
            $arr['posts'][$key]['totalLikes'] = $p->getLikes();
            // $p->getLikes();
        }
        return $arr;
        // return $thread;
    }
    public function createThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'title' => 'required',
            'body' => 'required',
            'anonymous' => 'required|integer',
            'sticky' => 'required|integer',
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

        if(GeneralController::hasPermissions($section, 2) == true) {
        	$thread->sticky = $request->sticky;
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
            'sticky' => 'boolean',
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
    public function stickyThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $thread = Thread::where('id', '=', $request->thread_id);

        if($thread->count()) {
            $thread = $thread->first();
            $thread->sticky = 1;
            $thread->save();
            return "success";
        }
        else {
            return "no such thread";
        }
    }

    public function unstickyThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $thread = Thread::where('id', '=', $request->thread_id);

        if($thread->count()) {
            $thread = $thread->first();
            $thread->sticky = 0;
            $thread->save();
            return "success";
        }
        else {
            return "no such thread";
        }
    }

    public function lockThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $thread = Thread::where('id', '=', $request->thread_id);

        if($thread->count()) {
            $thread = $thread->first();
            $thread->locked = 1;
            $thread->save();
            return "success";
        }
        else {
            return "no such thread";
        }
    }

    public function unlockThread(Request $request) {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'thread_id' => 'required|exists:threads,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        $user = Auth::user();
        $section = Section::find($request->section_id);
        if(GeneralController::hasPermissions($section, 2) == false) {
            return "invalid permissions";
        }

        $thread = Thread::where('id', '=', $request->thread_id);

        if($thread->count()) {
            $thread = $thread->first();
            $thread->locked = 0;
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

        Log::debug("Create Post: ".$post);
        //Notify thread owner
        $thread = Thread::where('id',$post->thread_id)->first();
        if($thread != null) {
            //Don't notify self-replies
            if($thread->user != $user)
                NotificationController::sendNotification($thread->user,$section,"Thread Response","Someone has replied to your thread '".$thread->title."'.");
        }
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

    public function getNumLikes(Request $request) {
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

        $post = Post::where('id', $request->post_id)->first();
        if($post == null) {
            return "unknown_post";
        }
        return $post->getLikes();
    }

    public function likePost(Request $request) {
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

        $post = Post::where('id', $request->post_id)->first();
        if($post == null) {
            return "unknown_post";
        }
        $like = Like::where('user_id', $user->id)->where('post_id',$post->id)->first();


        if($like == null) {
            $like = new Like;
        }

        $like->user_id = $user->id;
        $like->post_id = $post->id;
        $like->vote = 1;
        $like->save();
        return "success";
    }
    public function unlikePost(Request $request) {
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

        $post = Post::where('id', '=', $request->post_id)->first();
        if($post == null) {
            return "unknown_post";
        }

        $like = Like::where('user_id', $user->id)->where('post_id',$post->id)->first();
        if($like == null) {
            $like = new Like;
        }

        $like->user_id = $user->id;
        $like->post_id = $post->id;
        $like->vote = 0;
        $like->save();
        return "success";
    }
}