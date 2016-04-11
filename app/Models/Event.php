<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
}
