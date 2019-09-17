<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;


class settings extends myModel {

    protected $table = 'settings';
    public static $tableName = 'settings';
    public $timestamps = false;
    protected $guarded = array("name", "desc", "type", "value");

}
