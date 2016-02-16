<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller {

    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

	public function showWelcome() {
		$user = User::where('firstname', '=', 'Buster')->first();
        $sections = $user->sections;
        $roleInSection1 = $user->role($sections[1]);
		return ($roleInSection1);
	}
}