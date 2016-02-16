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

    public function roles() {
        return $this->hasMany('App\Models\RoleUser');
    }
    public function postSignupActions()
    {
        $role_id = Role::where('name', '=', 'student')->first();
        $role = new RoleUser;
        $role->user_id = $this->id;
        $role->role_id = $role_id->id;
    }
    public function slug() {
        return $this->first_name." ".$this->last_name." (#".$this->id.")";
    }
}
