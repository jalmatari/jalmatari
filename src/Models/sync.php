<?php

namespace Jalmatari\Models;

class sync extends myModel
{
    public function __construct($table = null)
    {
        parent::__construct($table);//you can add custom table name here
    }

    public function __get($name)
    {
        $methodname = 'get' . ucfirst(strtolower($name));
        if (method_exists($this, $methodname))
            return $this->$methodname();

        return null;
    }

    public static function ItemsForTable($table,$ids)
    {
        $table='Jalmatari\Models\\' . $table;

        return $table::whereIn('id',$ids)->get();
    }

}
