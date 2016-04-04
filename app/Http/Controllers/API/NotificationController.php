<?php

namespace App\Http\Controllers\API;


use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Auth;
use Illuminate\Http\Request;
use Log;
use Validator;

class NotificationController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }

    /**
     * Gets the pending notifications for a user
     */
    public function getNotifications(Request $request) {
        $user = Auth::user();
        $notifications = $user->notifications()->where('read',0)->get();
        return $notifications;
    }
    /**
     * Marks a notification as read
     */
    public function markNotificationRead(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $notification = Notification::where('id',$request->id)->first();
        if($notification == null || $notification->user != Auth::user()) {
            return "invalid_permissions";
        }
        $notification->read = 1;
        $notification->save();
        return "success";
    }

    /**
     * Add Notification to a users account.
     * The user can fetch all notifications using the NotificationsController.
     */
    public static function sendNotification($user, $section, $title, $message) {
        $notification = new Notification;
        $notification->section_id = $section->id;
        $notification->user_id = $user->id;
        $notification->title = $title;
        $notification->body = $message;
        $notification->save();
        Log::debug("Sent notification to user ".$user->email.": ".$message);
    }
}
