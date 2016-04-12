<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Assignment extends Model {
    protected $dates = ['deadline'];

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }

    public function category() {
    	return $this->hasOne('App\Models\AssignmentCategory','id','category_id');
    }

    public function grades() {
        return $this->hasMany('App\Models\Grade');
    }

    public function files() {
        return $this->hasMany('App\Models\AssignmentFileUpload','assignment_id','id');
    }

}