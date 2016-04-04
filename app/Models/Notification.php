<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
}
