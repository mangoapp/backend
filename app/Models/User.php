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
     * Runs if you call $user->roles
     */
    public function getRolesAttribute() {
        $pivots = RoleUser::where('user_id',$this->id)->get();
        $data = array();
        foreach($pivots as $pivot) {
            $role = Role::where('id',$pivot->role_id)->first();
            $section = Section::where('id',$pivot->section_id)->first();
            $roleData = array(
                "id" => $role->id,
                "name" => $role->name,
                "display_name" => $role->name,
                "description" => $role->description,
                "level" => $role->level,
                "created_at" => $role->created_at,
                "updated_at" => $role->updated_at,
                "deleted_at" => $role->deleted_at,
                "section" => array(
                    "id" => $section->id,
                    "name" => $section->name,
                ),
                "course" => array(
                    "id" => $section->course->id,
                    "name" => $section->course->name,
                )
            );
            array_push($data,$roleData);
        }
        return $data;
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
     * Returns all the announcemens posted by this user
     */
    public function announcements() {
        return $this->hasMany('App\Models\Announcement');
    }

    /**
     * Returns all the notifications for this user
     */
    public function notifications() {
        return $this->hasMany('App\Models\Notification');
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
    
    public static function v4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
