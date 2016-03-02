<?php namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Auth;

/**
 * Class GeneralController
 * Contains static helper methods for use in other controllers.
 * @package App\Http\Controllers\API
 */
class GeneralController extends Controller {

    /**
     * Checks the logged in user to make sure they are
     * part of the given section and that they have the
     * given auth level.
     */
    public static function hasPermissions($section, $level) {
        $user = Auth::user();
        if($section == null || $user->role($section) == null || $user->role($section)->level < $level) {
            return false;
        }
        return true;
    }

    /**
     * Checks the given user (not the logged in user) to
     * make sure they are part of the given section and
     * that they have the given auth level.
     */
    public static function userHasPermissions($user, $section, $level) {
        if($section == null || $user->role($section) == null || $user->role($section)->level < $level) {
            return false;
        }
        return true;
    }

    public static function successWrap($data) {
        return ['data'=>$data,
            'meta'=>['status'=>"200"]
        ];
    }
}
