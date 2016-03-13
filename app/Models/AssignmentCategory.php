<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AssignmentCategory extends Model {

    protected $table = "categories";

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }

    public function assignment() {
    	return $this->belongsTo('App\Models\Assignment');
    }
}