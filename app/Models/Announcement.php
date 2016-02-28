<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
}
