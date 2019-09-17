<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

use DB,Schema;
use Jalmatari\Funs\Funs;

class tables extends myModel
{
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($table) {
            Schema::dropIfExists($table->name);
            tables_cols::where('TABLE_ID',$table->id)->delete();
        });
    }


    public function nameWithPrefix()
    {
        return Funs::DB_Prefix() . $this->name;
    }

    public function afterSaving()
    {
        $comment = [
            'title' => $this->title,
            'new'   => $this->new,
            'edit'  => $this->edit,
        ];

        $comment = json_encode($comment, JSON_UNESCAPED_UNICODE);

        $sql = "ALTER TABLE " . $this->nameWithPrefix() . " COMMENT '{$comment}'";
        DB::statement($sql);
    }

    public function cols()
    {

        return $this->hasMany(__NAMESPACE__ . '\tables_cols', 'TABLE_ID');
            //->orderBy('ORDINAL_POSITION');
    }

    public static function showAddBtn($name)
    {
        return static::where('name', $name)->first()->show_add_btn == 1;
    }

    public static function addBtn($name)
    {
        return static::where('name', $name)->first()->new;
    }

    public function getModelAttribute()
    {
        $tableMOdel = new  $this->fullName;

        return $tableMOdel;
    }

    public function getFullNameAttribute()
    {

        return $this->namespace . $this->name;
    }

    public function colsForForm($data)
    {
        $colsRows = $this->cols;
        $cols = [];
        foreach ($colsRows as $col)
            $cols[ $col->name ] = $col->colForForm($data[ $col->name ]);

        return $cols;
    }

    public function getHasTimestampsAttribute()
    {
        return $this->cols->filter(function ($item) {
                return in_array(strtolower($item->COLUMN_NAME), [ 'updated_at', 'created_at' ]);
            })->count() == 2;
    }
}
