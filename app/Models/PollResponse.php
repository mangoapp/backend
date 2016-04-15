<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollResponse extends Model
{
    protected $table = 'responses';

    public function poll() {
        return $this->belongsTo('App\Models\Poll');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
