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
    Route::post('sections', 'CourseController@deleteSection');
    Route::get('users/sections', 'UserController@getUserSections');
    Route::post('users/sections', 'CourseController@addUserToCourse');
    Route::post('users/sections/accept', 'CourseController@acceptInvite');
    Route::post('users/roles/edit',"CourseController@editRole");
    Route::post('users/roles/kick',"CourseController@kickUser");
    Route::get('courses/sections/{course_id}', 'CourseController@showSections');
    Route::post('users/join', 'CourseController@joinCourse');
    Route::get('sections/{section_id}/students','CourseController@getSectionUsers');

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
    Route::post('sections/{section_id}/submitQuiz','AssignmentController@submitQuiz');
    Route::get('sections/assignments/{assignment_id}','AssignmentController@getQuiz');

    //Grades
    Route::get('sections/{section_id}/grades','GradeController@getSectionGrades'); //Get your grades for a section (For Students)
    Route::get('sections/{section_id}/allGrades','GradeController@getAllSectionGrades'); //Get all student's grades for all assignments section (TAs)
    Route::get('assignments/{assignment_id}/grades','GradeController@getAssignmentGrades'); //Get all grades for assignment (For TAs)
    Route::get('sections/{section_id}/users/{user_id}/currentAverage','GradeController@getStudentAverage');
    Route::get('sections/{section_id}/myAverage','GradeController@getCurrentStudentAverage');
    Route::get('sections/{section_id}/allAverages','GradeController@getAllAverages');

    // Verify that assignment_id is needed in the url
    Route::post('assignments/{assignment_id}/grades','GradeController@createGrade');
    Route::post('assignments/{assignment_id}/updateGrade','GradeController@updateGrade');
    Route::post('assignments/{assignment_id}/deleteGrade','GradeController@deleteGrade');

    //File Submission
    Route::post('assignments/{assignment_id}/upload','FileController@submitFile');
    Route::get('assignments/{assignment_id}/uploads','FileController@getFiles');
    Route::get('files/{file_id}','FileController@downloadFile');

    //Category
    Route::get('sections/{section_id}/categories','CategoryController@getSectionCategories'); //Get all categories in a section
    Route::get('sections/{section_id}/categories/{category_id}/assignments','CategoryController@getCategoryAssignments'); //Get all assignments in a category

    // verify that the get url param is actually needed. If it's a post it should be put in the body not URL
    Route::post('sections/{section_id}/categories','CategoryController@createCategory');
    Route::post('sections/{section_id}/updateCategory','CategoryController@updateCategory');
    Route::post('sections/{section_id}/deleteCategory','CategoryController@deleteCategory');

    //Notifications
    Route::get('notifications','NotificationController@getNotifications');
    Route::post('notifications','NotificationController@markNotificationRead');

    //Forum
    Route::get('forum/{section_id}/threads', 'ForumController@allThreads');
    Route::get('forum/{section_id}/threads/{thread_id}/posts', 'ForumController@getPosts');
    Route::post('forum/threads', 'ForumController@createThread');
    Route::post('forum/threads/update', 'ForumController@updateThread');
    Route::post('forum/threads/delete', 'ForumController@deleteThread');
    Route::post('forum/threads/lock', 'ForumController@lockThread');
    Route::post('forum/threads/unlock', 'ForumController@unlockThread');
    Route::post('forum/threads/sticky', 'ForumController@stickyThread');
    Route::post('forum/threads/unsticky', 'ForumController@unstickyThread');
    Route::post('forum/posts', 'ForumController@createPost');
    Route::post('forum/posts/update', 'ForumController@updatePost');
    Route::post('forum/posts/delete', 'ForumController@deletePost');
    Route::post('forum/like', 'ForumController@likePost');
    Route::post('forum/unlike', 'ForumController@unlikePost');
    Route::post('forum/numLike', 'ForumController@getNumLikes');

    // Events
    Route::get('sections/{section_id}/events', 'EventsController@getEventsBySection');
    Route::get('users/events', 'EventsController@getEventsByUser');
    Route::post('sections/events/create', 'EventsController@createEvent');
    Route::post('sections/events/update', 'EventsController@editEvent');
    Route::post('sections/events/delete', 'EventsController@deleteEvent');
    Route::get('calendar/{uuid}', 'EventsController@generateCalendar');

});