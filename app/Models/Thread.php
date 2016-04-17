<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Thread extends Model
{
    use SearchableTrait;
    protected $searchable = [
        'columns' => [
            'threads.title' => 10,
            'threads.body' => 5,
        ]
    ];

	public function course() {
    	return $this->belongsTo('App\Models\Course');
    }
    public function user() {
    	return $this->belongsTo('App\Models\User');
    }
    public function posts() {
    	return $this->hasMany('App\Models\Post');
    }
}