<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	public function showWelcome() {
		$user = User::where('firstname', '=', 'Buster')->first();
		return ($user->roles()->get()->toArray());
		
	}
}