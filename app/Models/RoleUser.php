<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class RoleUser extends Model {
	protected $table = 'role_user';
	public $timestamps = false;
    protected $dates = ['deleted_at'];

    /**
     * Updates role. This is needed because eloquent doesnt like 3 way relations.
     * @param User $user
     * @param Role $newRole
     * @param Section $section
     */
    public function updateRole(User $user,Role $newRole,Section $section) {
        DB::update("UPDATE role_user SET role_id='".$newRole->id."' WHERE user_id='".$user->id."' AND section_id='".$section->id."'");
    }
}
