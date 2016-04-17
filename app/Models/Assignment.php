<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;


class Assignment extends Model {
    use SearchableTrait;

    protected $searchable = [
        'columns' => [
            'assignments.title' => 10,
            'assignments.description' => 5,
        ]
    ];

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