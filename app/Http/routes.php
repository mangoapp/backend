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

// LEAVE THIS ROUTE HERE!
Route::get('/', function () {
    return view('welcome');
});
// LEAVE THIS ROUTE HERE!

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


Route::group(['prefix' => 'v1','namespace'=>'API', 'middleware' => 'cors'], function()
{
    //Users & Auth
    Route::post('auth', 'AuthController@login');
    Route::post('users', 'AuthController@signUp');
    Route::post('passwordResetRequest', 'UserController@requestPasswordReset');
    Route::post('passwordResetResponse', 'UserController@confirmPasswordReset');

    //Courses & Sections
    Route::get('allcourses', 'CourseController@showAll');
    Route::post('courses', 'CourseController@createCourse');
    Route::post('courses/sections', 'CourseController@createSection');
    Route::delete('sections', 'CourseController@deleteSection');
    Route::get('users/sections', 'UserController@getUserSections');
    Route::post('users/sections', 'CourseController@addUserToCourse');
    Route::post('users/sections/accept', 'CourseController@acceptInvite');
    Route::post('users/roles/edit',"CourseController@editRole");
    Route::post('users/roles/kick',"CourseController@kickUser");
    Route::get('courses/sections/{course_id}', 'CourseController@showSections');
    Route::post('users/join', 'CourseController@joinCourse');

    //Announcements
    Route::post('announcements', 'AnnouncementController@createAnnouncement');
    Route::post('announcements/edit', 'AnnouncementController@editAnnouncement');
    Route::post('announcements/delete', 'AnnouncementController@deleteAnnouncement');
    Route::get('announcements/{section_id}', 'AnnouncementController@getAnnouncements');

    //Assignments
    Route::get('sections/{section_id}/assignments','AssignmentController@getAssignments');
    Route::post('sections/{section_id}/assignments','AssignmentController@createAssignment');
    Route::post('sections/{section_id}/updateAssignment','AssignmentController@updateAssignment');
    Route::post('sections/{section_id}/deleteAssignment','AssignmentController@deleteAssignment');

    //Grades
    Route::get('sections/{section_id}/grades','GradeController@getSectionGrades'); //Get your grades for a section (For Students)
    Route::get('assignments/{assignment_id}/grades','GradeController@getAssignmentGrades'); //Get all grades for assignment (For TAs)
    Route::post('assignments/{assignment_id}/grades','GradeController@createGrade');
    Route::post('assignments/{assignment_id}/updateGrade','GradeController@updateGrade');
    Route::post('assignments/{assignment_id}/deleteGrade','GradeController@deleteGrade');

    //Category
    Route::get('sections/{section_id}/categories','CategoryController@getSectionCategories'); //Get all categories in a section
    Route::get('sections/{section_id}/categories/{category_id}/assignments','CategoryController@getCategoryAssignments'); //Get all assignments in a category
    Route::post('sections/{section_id}/categories','CategoryController@createCategory');
    Route::post('sections/{section_id}/updateCategory','CategoryController@updateCategory');
    Route::post('sections/{section_id}/deleteCategory','CategoryController@deleteCategory');

    //Forum
    // lol urls
    Route::get('courses/{section_id}/threads', 'ForumController@allThreads');
    Route::post('courses/threads', 'ForumController@createThread');
    Route::post('courses/threads/update', 'ForumController@updateThread');
    Route::post('courses/threads/delete', 'ForumController@deleteThread');

    Route::post('courses/posts', 'ForumController@createPost');
    Route::post('courses/posts/update', 'ForumController@updatePost');
    Route::post('courses/posts/delete', 'ForumController@deletePost');

    Route::get('courses/{section_id}/threads/{thread_id}/posts', 'ForumController@getPosts');

});