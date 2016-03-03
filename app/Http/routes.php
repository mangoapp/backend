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


Route::group(['prefix' => 'v1','namespace'=>'API'], function()
{


    //Users & Auth
    Route::post('auth', 'AuthController@login');
    Route::post('users', 'AuthController@signUp');
    Route::post('passwordResetRequest', 'UserController@requestPasswordReset');
    Route::post('passwordResetResponse', 'UserController@confirmPasswordReset');

    //Courses & Sections
    Route::post('courses', 'CourseController@createCourse');
    Route::post('courses/sections', 'CourseController@createSection');
    Route::delete('sections', 'CourseController@deleteSection');
    Route::get('users/sections', 'UserController@getUserSections');
    Route::post('users/sections', 'CourseController@addUserToCourse');
    Route::post('users/sections/accept', 'CourseController@acceptInvite');
    Route::post('users/roles/edit',"CourseController@editRole");
    Route::post('users/roles/kick',"CourseController@kickUser");

    //Announcements
    Route::post('announcements', 'AnnouncementController@createAnnouncement');
    Route::post('announcements/edit', 'AnnouncementController@editAnnouncement');
    Route::post('announcements/delete', 'AnnouncementController@deleteAnnouncement');
    Route::get('announcements/{course_id}', 'AnnouncementController@getAnnouncements');
});