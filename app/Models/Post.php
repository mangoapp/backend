<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Post extends Model
{
    use SearchableTrait;
    protected $searchable = [
        'columns' => [
            'posts.body' => 10,
        ]
    ];

	public function thread() {
    	return $this->belongsTo('App\Models\Thread');
    }

    public function user() {
    	return $this->belongsTo('App\Models\User');
    }

    public function likes() {
    	return $this->hasMany('App\Models\Like');
    }

    public function getLikes() {
        $allLikes = Like::where('post_id',$this->id)->get();
        $score = 0;
        foreach($allLikes as $like) {
            $score += $like->vote;
        }
        return $score;
    }
}