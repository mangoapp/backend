<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model {

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function course() {
    	return $this->belongsTo('App\Models\Course');
    }

    public function assignments() {
    	return $this->hasMany('App\Models\Assignment');
    }

    public function categories() {
    	return $this->hasMany('App\Models\AssignmentCategory');
    }

    public function announcements() {
        return $this->hasMany('App\Models\Announcement');
    }

    public function users() {
        return $this->belongsToMany('App\Models\User','role_user');
    }
}