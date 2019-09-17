<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Jalmatari\Funs\Funs;

class myModel extends Model
{
    protected $table = '';
    public static $tableName = '';
    protected $fillable = [];

    public function __construct( $attributes = [])
    {
        $this->init(is_string($attributes)?$attributes:null);//you can add custom table name here
        parent::__construct(is_array($attributes)?$attributes:[]);
    }

    public function init($table = null)
    {
        if (is_null($table) || !is_string($table)) {
            $table = get_class($this);
            $table = explode('\\', $table);
            $table = $table[ count($table) - 1 ];
            $table = strtolower($table);
        }

        $this->table = $table;
        static::$tableName = $table;
        $this->getFillableCols();
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    protected function getFillableCols()
    {
        if (!is_string($this->table))
            $this->table = '';
        if ($this->table != 'users') {
            $fillable = $this->TableCols($this->table);
            //ToDo make sure it's secure.. in this step....
            $this->fillable = $fillable;
        }
    }

    public static function getTitleForTable($table, $type)
    {
        if (!is_string($table))
            $table = is_string(static::$tableName) ? static::$tableName : '';

        $table = tables::where('name', $table)->first();

        $title = (!is_null($table)) ? $table->{$type} : '';

        return $title;
    }

    public static function gettitle($type)
    {
        if (!is_string(static::$tableName))
            static::$tableName = '';
        $title = '';

        $table = tables::where('name', static::$tableName)->first();
        if (!is_null($table))
            $title = $table->{$type};

        return $title;
        dd($type, $table);
        $sql = "SELECT table_comment  FROM INFORMATION_SCHEMA.TABLES  WHERE  table_name='" . DB::getTablePrefix() . static::$tableName . "'";
        $title = DB::select($sql);
        if (count($title) >= 1)
            $title = $title[ count($title) - 1 ];
        if (isset($title->table_comment)) {
            $title = json_decode(str_replace('”', '"', $title->table_comment));
            if (isset($title->{$type}))
                $title = $title->{$type};
            else
                $title = '';
        }
        else {
            $title = '';
        }

        return $title;
    }

    public function TableCols($table = null)
    {
        if (is_null($table))
            $table = static::$table;
        $cachedName = $table . '_TableCols';

        return cache_($cachedName, function () use ($table, $cachedName) {
            $table = Funs::DB_Prefix() . $table;
            $dbName = Funs::DB_Name();
            $cols = DB::select("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name='$table' and TABLE_SCHEMA='$dbName'");

            return array_pluck($cols, 'COLUMN_NAME');
        });;
    }

    public function log($type = 0, $val = null, $colName = 'name')
    {
        if (is_null($val))
            $val = $this->{$colName};
        users_logs::InsertLog($val, $type);
    }


    public function user()
    {
        return $this->belongsTo('Jalmatari\Models\users', 'user_id');
    }


    public function getUserNameAttribute()
    {
        return cache_('user_name_' . $this->user_id, function () {
            $user = 'زائر';
            if ($this->user_id != 0)
                $user = Funs::IsIn($this->user, 'name', 'مستخدم تم حذفه!');

            return $user;
        });
    }


    public function getUserImgAttribute()
    {
        return cache_('user_img_' . $this->user_id, function () {
            $photo = '/jalmatari/img/users/default-user.png';
            if ($this->user_id != 0)
                $photo = Funs::IsIn($this->user, 'photo', 'مستخدم تم حذفه!') ?? $photo;

            return $photo;
        });
    }

    public function getJobNameAttribute()
    {
        $user = $this->user;

        return Funs::IsIn(Funs::IsIn($user, 'job'), 'name', 'مستخدم تم حذفه!');
    }

    public function dateHjri()
    {
        return $this->hjri_date;
    }

    public function getHjriDateAttribute()
    {
        return Funs::Hijri($this->created_at);
    }

    public static function paginateFooter($paginator, $append = [])
    {


        return Funs::paginateFooter($paginator, $append);
    }

    public static function dd()
    {
        $modelName = static::ModelName();
        $class = new \ReflectionClass('Jalmatari\Models\\' . $modelName);
        $methods = $class->getMethods();
        $methodsArr = [];
        foreach ($methods as $method) {
            $isStatic = $method->isStatic();
            $className = $method->class;
            $className = explode("\\", $className);
            $className = $className[ count($className) - 1 ];
            $methodsArr[] = [ "name" => $className . ($isStatic ? "::" : "()->") . $method->name, "static" => $method->isStatic() ? "static" : "none-static" ];
        }
        $methods = collect($methodsArr);
        $methods = $methods->groupBy('static');
        $methodsArr = [];
        foreach ($methods as $key => $method)
            $methodsArr[ $key ] = $method->pluck('name')->toArray();
        dd([ $modelName => $methodsArr ]);
    }

    public static function ModelName()
    {

        $class = new static();

        return $class->getTable();
    }

}
