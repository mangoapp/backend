<?php namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Auth;

class GeneralController extends Controller {
    public static function hasPermissions($section, $level) {
        $user = Auth::user();
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
