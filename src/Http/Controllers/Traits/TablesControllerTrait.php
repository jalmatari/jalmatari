<?php

namespace Jalmatari\Http\Controllers\Traits;

use DB;
use File;
use Illuminate\Support\Arr;
use Jalmatari\Funs\Funs;
use Jalmatari\JalmatariServiceProvider;
use Jalmatari\Models\tables;
use Jalmatari\Models\tables_cols;


trait TablesControllerTrait
{
    public $prefix;
    public $dbName;

    public function tablesOfDataBase()
    {
        $this->prefix = Funs::DB_Prefix();
        $this->db = Funs::DB_Name();

        $tables = [];
        $dbTables = "SELECT *,REPLACE(table_name,'{$this->prefix}','') as name,"
            . " (select id from {$this->db}.{$this->prefix}tables where CONCAT('{$this->prefix}',name)=table_name) TABLE_ID"
            . " FROM information_schema.tables"
            . " WHERE table_schema = '{$this->db}'"
            . " and TABLE_NAME like '{$this->prefix}%' and TABLE_NAME not like '%copy%'"
            . " and TABLE_NAME NOT in ('{$this->prefix}password_resets', '{$this->prefix}migrations')";
        $dbTables = DB::select($dbTables);

        foreach ($dbTables as $table) {

            $table->IS_New_TABLE = !(bool) $table->TABLE_ID;
            //if It's New Table
            if ($table->IS_New_TABLE) {
                $tableComment = $this->parsingTableComment($table->TABLE_COMMENT);

                $table->TABLE_ID = DB::table('tables')->insertGetId(
                    [
                        'name'       => $table->name,
                        'title'      => $tableComment->title,
                        'new'        => $tableComment->new,
                        'edit'       => $tableComment->edit,
                        'created_at' => $table->CREATE_TIME,
                    ]
                );
            }
            $this->autoInsertColsOfTable($table);

        }
        $oldTables = tables::all()->pluck('name');
        $tables = collect($tables)->whereNotIn('name', $oldTables);

        return $tables;
    }

    public function autoInsertColsOfTable($table)
    {
        $cols = $this->colsOfTable($table)->toArray();

        //remove any cols that deleted from database table
        if (!$table->IS_New_TABLE)
            tables_cols::where('TABLE_ID', $table->TABLE_ID)
                ->whereNotIn('COLUMN_NAME', array_pluck($cols, 'COLUMN_NAME'))
                ->delete();

        foreach ($cols as $col) {
            $whereVals = Arr::only($col, [ 'TABLE_ID', 'COLUMN_NAME' ]);
            if (tables_cols::where($whereVals)->count() >= 1)
                tables_cols::where($whereVals)->update($col);
            else
                tables_cols::insert($col);
        }

    }

    public function parsingTableComment($comment)
    {

        $data = new \stdClass();
        $comment = str_replace([ '”', '“' ], '"', $comment);
        $comment = json_decode($comment);
        foreach ([ 'title', 'new', 'edit' ] as $key)
            $data->{$key} = Funs::IsIn($comment, $key, '');

        return $data;
    }


    public function parsingColumnComment($comment)
    {

        $data = new \stdClass();
        $comment = str_replace([ '”', '“' ], '"', $comment);
        $comment = json_decode($comment);
        foreach ([ 'title', 'type', 'para' ] as $key)
            $data->{$key} = Funs::IsIn($comment, $key, '');


        $data->{'source'} = Funs::IsIn($data->para, 'source', '');
        if (!is_string($data->{'source'}))
            $data->{'source'} = json_encode($data->{'source'}, JSON_UNESCAPED_UNICODE);
        unset($data->para->source);
        $data->attr = $data->para;
        if ($data->attr != '')
            $data->attr = json_encode($data->para, JSON_UNESCAPED_UNICODE);

        return $data;
    }

    public function colsOfTable($table)
    {
        $sql = "SELECT * FROM information_schema.columns WHERE table_name='{$table->TABLE_NAME}' and TABLE_SCHEMA='{$table->TABLE_SCHEMA}'";

        $results = DB::select($sql);
        $cols = [];
        foreach ($results as $result) {
            //$colComment = $this->parsingColumnComment($result->COLUMN_COMMENT);

            if (!(in_array($result->COLUMN_DEFAULT, [ null, '', 'CURRENT_TIMESTAMP' ])) && !is_numeric($result->COLUMN_DEFAULT))
                $result->COLUMN_DEFAULT = "'" . $result->COLUMN_DEFAULT . "'";

            $col = collect([
                'TABLE_ID'         => $table->TABLE_ID,
                'COLUMN_NAME'      => $result->COLUMN_NAME,
                'ORDINAL_POSITION' => $result->ORDINAL_POSITION,
                'COLUMN_DEFAULT'   => $result->COLUMN_DEFAULT,
                'IS_NULLABLE'      => $result->IS_NULLABLE,
                'DATA_TYPE'        => $result->DATA_TYPE,
                'COLUMN_TYPE'      => $result->COLUMN_TYPE,
                'EXTRA'            => $result->EXTRA,
                'COLUMN_COMMENT'   => $result->COLUMN_COMMENT,
                /*'TITLE'            => $colComment->title,
                'TYPE'             => $colComment->type,
                'SOURCE'           => $colComment->source,
                'ATTR'             => $colComment->attr,*/
            ]);

            $cols[] = $col;
        }

        return collect($cols);
    }

    public function changeCotrollerAfterCreated($controller)
    {
        $fileDire = app_path('Http/Controllers/' . $controller . '.php');
        $controllerFile = file(JalmatariServiceProvider::path() . '/inc/controller.template');
        $controllerFile = str_replace("controllerName", $controller, $controllerFile);
        $controllerFile = str_replace("2019", date("Y"), $controllerFile);
        File::put($fileDire, $controllerFile);
        chmod($fileDire, 0775);

    }

    public function changeModelAfterCreated($model)
    {

        $fileDire = app_path("Jalmatari/Models/{$model}.php");

        $modelFile = file(JalmatariServiceProvider::path() . '/inc/model.template');
        $modelFile = str_replace("modelName", $model, $modelFile);
        $modelFile = str_replace("2019", date("Y"), $modelFile);
        $model = tables::where('name', $model)->first();
        if (!$model->hasTimestamps)
            $modelFile = str_replace('//public $timestamps', 'public $timestamps', $modelFile);

        File::put($fileDire, $modelFile);
        chmod($fileDire, 0775);
        //Funs::Artisan('clear-compiled');
    }

}
