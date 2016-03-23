<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AssignmentCategory extends Model {

    protected $table = "categories";

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }

    public function assignments() {
    	return $this->hasMany('App\Models\Assignment','category_id','id');
    }

}