<?php

namespace Jalmatari\Models;

class controllers extends myModel
{

    public function __construct($table = null)
    {
        parent::__construct($table);//you can add custom table name here
    }

    public static function TablesList($data = null)
    {
        $tables = tables::where('status', 1)->orderBy('name')->get()->pluck('name', 'id')->toArray();
        if (!is_null($data) && $data !== 0 && isset($tables[ $data ]))
            $tables = [ $data => $tables[ $data ] ]+ $tables;

        return $tables;
    }

    public function tableRow()
    {
        return $this->belongsTo(__NAMESPACE__ . '\tables', 'table_id');
    }

    public function getTableNameAttribute()
    {
        $tableName = '';
        if ($this->tableRow)
            $tableName = $this->tableRow->name;

        return $tableName;
    }
}
