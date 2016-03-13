<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Quiz extends Model {
    protected $dates = ['deadline'];
    protected $table = 'quiz';

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }

    public function category() {
    	return $this->hasOne('App\Models\AssignmentCategory','id','category_id');
    }
}