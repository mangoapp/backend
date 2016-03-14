<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use SoftDeletes;

    public function assignment() {
        return $this->belongsTo('App\Models\Assignment');
    }

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }

}
