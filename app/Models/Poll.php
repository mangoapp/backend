<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    protected $table = 'polls';

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
}
