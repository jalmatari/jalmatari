<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */
namespace Jalmatari\Models;

use DB;

class users_groups extends myModel
{

    protected $table = 'users_groups';
    public static $tableName = 'users_groups';
    protected $fillable = [
        'user_id',
        'group_id',
    ];



    public function group()
    {

        return $this->belongsTo(__NAMESPACE__.'\groups', 'group_id', 'id');
    }


    public function groups()
    {
        return $this->hasMany(__NAMESPACE__.'\groups', 'group_id', 'id');
    }


}
