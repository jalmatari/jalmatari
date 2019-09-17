<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

class groups extends myModel
{

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($group) {
            $users = $group->users->pluck('id');
            if ($users->count() >= 1)
                users_groups::whereIn('user_id', $users)->delete();
        });
    }


    public function users()
    {
        return $this->hasMany('Jalmatari\Models\users_groups', 'group_id');
    }
}
