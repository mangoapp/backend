<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Announcement extends Model
{
    use SoftDeletes;
    use SearchableTrait;

    protected $searchable = [
        'columns' => [
            'announcements.title' => 10,
            'announcements.body' => 5,
        ]
    ];
    protected $dates = ['deleted_at'];
    public function section() {
        return $this->belongsTo('App\Models\Section');
    }
}
