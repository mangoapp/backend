<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'UserController@showWelcome');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['prefix' => 'v1','namespace'=>'API'], function()
{
	Route::post('auth', 'AuthController@login');
    Route::post('users', 'AuthController@signUp');
    Route::get('users/sections', 'UserController@getUserSections');
    Route::post('passwordResetRequest', 'UserController@requestPasswordReset');
    Route::post('passwordResetResponse', 'UserController@confirmPasswordReset');
    Route::post('courses', 'CourseController@createCourse');
    Route::post('users/sections', 'CourseController@addUserToCourse');
});