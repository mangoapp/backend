<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentFileUpload extends Model
{
    protected $table = "assignment_files";

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function assignment() {
        return $this->belongsTo('App\Models\Assignment');
    }

    public function document() {
        return $this->hasOne('App\Models\FileUpload','id','file_id');
    }
}
