<?php namespace App\Http\Controllers\API;
use App\Models\User;
use App\Models\Role;
use JWTAuth;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Hash;
use Mail;
class AuthController extends Controller {
    /**
    * Authenticate a user
    *
    * @param  Request  $request
    * @return Response
    */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password'   => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        else {
	        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
	            $roles = Auth::user()->roles()->get()->lists('name');
	            $token = JWTAuth::fromUser(Auth::user(),['exp' => strtotime('+1 year'),'roles'=>$roles, 'slug'=>Auth::user()->slug()]);
	            return compact('token');
	        }
    	}
		return response()->json(['error' => 'invalid_credentials'], 401);
    }

    /**
    * Register a user
    *
    * @param  Request  $request
    * @return Response
    */
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email'   => 'required|email|unique:users',
            'password'    => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        else {
            $user = new User;
            $user->firstname = $request['firstname'];
            $user->lastname = $request['lastname'];
            $user->password = Hash::make($request['password']);
            $user->email = $request['email'];
            $user->save();

            // $user->postSignupActions(); // Attach roles

            $roles = $user->roles();
            $token = JWTAuth::fromUser($user,['exp' => strtotime('+1 year'),'roles'=>$roles, 'slug'=>$user->slug()]);
            
   //          Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
   //  			$message->from('hello@mango.org', 'Welcome');
   //  			$message->to($user->email);
			// });
			
            return compact('token');
        }
    }

	public function debug()
	{
		$user = JWTAuth::parseToken()->authenticate();
		return $user;
	}
}