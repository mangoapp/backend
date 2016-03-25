<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
	public function course() {
    	return $this->belongsTo('App\Models\Course');
    }
}