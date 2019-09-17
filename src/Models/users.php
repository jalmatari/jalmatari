<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

use DB;

class users extends myModel
{
    protected $table = 'users';
    public static $tableName = 'users';
    protected $fillable = [ 'id', 'username', 'name', 'password', 'email', 'permissions' ];


    public static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $groups = $user->groups->pluck('id');
            if ($groups->count() >= 1)
                users_groups::whereIn('group_id', $groups)->delete();
        });
    }

    public function groups()
    {
        return $this->hasMany(__NAMESPACE__.'\users_groups', 'user_id');
    }

    public function job()
    {
        return $this->belongsTo(__NAMESPACE__.'\permissions', 'job_title', 'id');
    }
}
