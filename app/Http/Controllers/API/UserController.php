<?php namespace App\Http\Controllers\API;

use App\Models\Course;
use App\Models\PasswordReset;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Auth;

class UserController extends Controller {

    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth",['except' => ['requestPasswordReset','confirmPasswordReset']]);
    }

	public function showWelcome() {
		return view('welcome');
	}

    public function requestPasswordReset(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            $email = $request['email'];
            $user = User::where('email',$email)->first();
            if($user == null) {
                return "invalid_email";
            }

            //Make sure password reset has not been requested already
            $reset = PasswordReset::where('user_id',$user->id)->first();
            if($reset != null) {
                return "success";
            }
            //Generate new password reset
            $reset = new PasswordReset;
            $reset->user_id = $user->id;
            $reset->token = str_random(100);
            $reset->save();

            Mail::queue('emails.passwordreset', ['resetToken' => $reset->token], function ($message) use ($user) {
                $message->from('noreply@mango.com');
                $message->subject("Welcome to Mango!");
                $message->to($user->email);
            });
            Log::debug("Password Reset Token: ".$reset->token);

            return "success";

        }
    }

    public function confirmPasswordReset(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        } else {
            $email = $request['email'];
            $user = User::where('email',$email)->first();
            if($user == null) {
                return "invalid_email";
            }

            //Make sure password reset token is valid
            $reset = PasswordReset::where('user_id',$user->id)->where('token',$request['token'])->first();
            if($reset == null) {
                return "invalid_token";
            }

            //Is this token too old?
            $currentDate = Carbon::now();
            Log::debug("createdat: ".$reset->created_at);
            $tokenDate = $reset->created_at;
            $diff = $currentDate->diffInHours($tokenDate);

            Log::debug("Diff is ".$diff.", original: ".$tokenDate);
            if(abs($diff) > 1) {
                //This token is too old. Delete it.
                $reset->delete();
                return "expired_token";
            }
            //Token is valid, update password
            $user->password = Hash::make($request['password']);
            $user->save();

            //Remove reset token from database
            $reset->delete();
            return "success";//fixme
        }
    }

    public function getUserSections() {
        $user = Auth::user();
        
        $data = array();
        foreach($user->sections as $section) {
            $sectionData = array(
                "id" => $section->id,
                "name" => $section->name,
                "role" => $user->role($section)->name,
                "course" => array(
                    "id" => $section->course->id,
                    "name" => $section->course->name,
                )
            );
            array_push($data,$sectionData);
        }
        return $data;
    }
}