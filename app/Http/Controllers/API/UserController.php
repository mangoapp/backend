<?php namespace App\Http\Controllers\API;

use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	public function showWelcome() {
		return view('welcome');
	}
}