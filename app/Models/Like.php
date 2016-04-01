<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
	public function post() {
		return $this->belongsTo('App\Models\Post');
	}
}