<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleUser extends Model {

    use SoftDeletes;
	protected $table = 'role_user';
	public $timestamps = false;
    protected $dates = ['deleted_at'];
}
