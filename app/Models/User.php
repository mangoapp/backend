<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'firstname', 'lastname', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Returns all roles associated with this user across all sections
     */
    public function roles() {
        return $this->belongsToMany('App\Models\Role');
    }

    /**
     * Returns the role associated with this user for a specific section
     * @param Section $section
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function role(Section $section) {
        $relation = RoleUser::where('section_id',$section->id)->where('user_id',$this->id)->first();
        if(!$relation)
            return null;
        $role = Role::where('id',$relation->role_id)->first();
        return $role;
    }

    /**
     * Returns all the sections associated with this user, regardless of role
     */
    public function sections() {
        return $this->belongsToMany('App\Models\Section','role_user');
    }

    /**
     * Inserts the user into the specified section as a student
     * @param Section $section
     */
    public function postSignupActions(Section $section) {
        $studentRole = Role::where('name', '=', 'student')->first();
        $pivot = new RoleUser;
        $pivot->user_id = $this->id;
        $pivot->role_id = $studentRole->id;
        $pivot->section_id = $section->id;
        $pivot->save();
    }

    public function slug() {
        return $this->firstname." ".$this->lastname." (#".$this->id.")";
    }
}
