<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller {

	public function showWelcome() {

		$user = User::where('firstname', '=', 'Buster')->first();
        $sections = $user->sections;
        $roleInSection1 = $user->role($sections[1]);
		return ($roleInSection1);
	}
}