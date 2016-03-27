<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
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