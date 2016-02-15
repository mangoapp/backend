<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Assignment extends Model {
    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
    public function assignmentCategory() {
    	return $this->hasOne('App\Models\AssignmentCategory');
    }
}