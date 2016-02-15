<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AssignmentCategory extends Model {
    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
    public function assignment() {
    	return $this->belongsTo('App\Models\Assignment');
    }
}