<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Section extends Model {
    public function course() {
    	return $this->belongsTo('App\Models\Course');
    }
    public function assignments() {
    	return $this->hasMany('App\Models\Assignment');
    }
    public function assignmentCategory() {
    	return $this->hasMany('App\Models\AssignmentCategory');
    }
}