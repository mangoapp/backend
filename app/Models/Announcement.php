<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
}
