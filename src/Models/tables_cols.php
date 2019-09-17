<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

use DB;
use Jalmatari\Funs\Funs;

class tables_cols extends myModel
{
    public static $ColsTypes = [ "text", "textarea", "select", "checkbox", "hidden", "number", "radio", "selWithOptionData", "json" ];

    public static function isNullable($table, $col)
    {
        return static::where([ 'TABLE_NAME' => $table, 'COLUMN_NAME' => $col ])->first()->IS_NULLABLE == 'YES';
    }

    public static function isRequired($table, $col)
    {
        return !static::isNullable($table, $col);
    }

    public function getTableNameAttribute()
    {
        return $this->tableModel->name;
    }

    public function getNameAttribute()
    {
        return $this->COLUMN_NAME;
    }

    public function tableModel()
    {
        return $this->belongsTo(__NAMESPACE__ . '\tables', 'TABLE_ID');
    }

    public function initData()
    {
        if (is_null($this->data)) {
            if ($this->type == "checkbox")
                $this->data = true;
        }
        if (!is_null($this->SOURCE)&&$this->SOURCE!='') {
            $source = json_decode($this->SOURCE);
            if (isset($source->function))
                $this->data = Funs::CallTableFun($this->tableName, $source->function, $this->data);
        }
    }


    public function initFieldParameters()
    {
        $parameters = [];
        $para = json_decode($this->ATTR,true);
        if (is_array($para)) {
            foreach ($para as $key => $val)
                $parameters[ $key ] = $val;
        }
        if ($this->IS_NULLABLE == 'NO')
            $parameters["required"] = "required";
        $this->parameters = $parameters;
    }

    public function colForForm($data = null)
    {
        $this->data = $data;
        $this->type = static::$ColsTypes[ $this->TYPE ];
        $this->initFieldParameters();
        $this->initData();

        return [
            'title' => $this->TITLE,
            'type'  => $this->type,
            "data"  => $this->data,
            "other" => $this->parameters,
        ];;
    }
}
