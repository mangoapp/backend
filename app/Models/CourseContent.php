<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CourseContent
 * Represents a file uploaded to a section of a course.
 * @package App\Models
 */
class CourseContent extends Model
{
    protected $table = "course_content";

    public function section() {
        return $this->belongsTo('App\Models\Section');
    }

    public function document() {
        return $this->hasOne('App\Models\FileUpload','id','file_id');
    }
}
