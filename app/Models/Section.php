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

    public function grades() {
        return $this->hasMany('App\Models\Grade');
    }

    public function assignments() {
    	return $this->hasMany('App\Models\Assignment');
    }

    public function quizzes() {
        return $this->hasMany('App\Models\Quiz');
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

    public function events() {
        return $this->hasMany('App\Models\Event');
    }

    public function files() {
        return $this->hasMany('App\Models\CourseContent','section_id','id');
    }
}