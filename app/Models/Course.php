<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Course extends Model {
    public function sections() {
        return $this->hasMany('App\Models\Section');
    }
}