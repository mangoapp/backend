<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
	public function thread() {
    	return $this->belongsTo('App\Models\Thread');
    }
    public function user() {
    	return $this->belongsTo('App\Models\User');
    }
}