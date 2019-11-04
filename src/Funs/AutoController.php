<?php

/** Class to do All Views Work, Like: index views, table view, new view and edit view.
 *
 *
 * @author jalmatari <jalmatari@gmail.com>
 * @version 1.0
 * @package jalmatari
 * @category Views
 * @copyright (c) 2019, jalmatari
 *
 */

namespace Jalmatari\Funs;

use DB;
use Jalmatari\Models\menu;
use Jalmatari\Models\myModel;
use Jalmatari\Models\tables;
use Schema;
use View;

trait AutoController
{

    protected static $listView;
    protected static $newView;
    protected static $editView;
    public static $table = null;
    public static $ColsTypes = [ "text", "textarea", "select", "checkbox", "hidden", "number", "radio", "selWithOptionData", "json" ];
    protected static $disabledCols = [];

    public static function setDisabledCols($cols)
    {
        static::$disabledCols = $cols;
    }

    /**
     * @param $table
     */
    public static function setTable($table)
    {
        $helpersPath = 'admin.helpers.';
        static::$table = $table;
        static::$listView = $helpersPath . 'ListView';
        static::$newView = $helpersPath . 'NewView';
        static::$editView = $helpersPath . 'EditView';
    }

    /**
     * To Create View with table Compatible with DataTabple class and his Ajax.
     *
     * @param   array $cols List of columns.
     * @param   sting $table Table name.
     * @param   string $view The View Path Ex:'Home.Index'.
     * @param   int $title The Title that is defined in Table comments,[1=title , 2=new ,3=edit ,title]..
     * @return  View                The final view that is defined in $view.
     */
    public static function ListView($cols, $table = null, $view = null, $title = 1, $other_view = null)
    {
        if (is_null($table)) {
            $table = static::$table;
        }
        if (is_null($other_view)) {
            $other_view = "none";
        }
        if (is_null($view)) {
            $view = static::$listView;
        }
        View::share('title', static::_t($table, $title));

        return view($view, [ 'cols' => static::ArabicCols($cols), 'name' => $table, 'other_view' => $other_view ]);
    }

    /**
     * * ****************** * Title * ****************** *
     *
     * Get Title from table Comments..
     *
     * @param   string $table The name of Table.
     * @param   int $kind The title you want [1=title , 2=new ,3=edit]. or it's title
     * @return  String          The Title that is defined in Table comments.
     */
    public static function _t($table = null, $kind = 1)
    {
        if (!in_array($kind, [ 1, 2, 3 ]))
            return $kind;

        if (is_null($table))
            $table = static::$table;

        $kinds = [ 'fake rather than zero index', 'title', 'new', 'edit' ];

        return myModel::getTitleForTable($table, $kinds[ $kind ]);
    }

    /** Get Arabic Texts For The Columns
     *
     * @param   array $cols The columns Array.
     * @param   null $table The tabel Name.
     * @return  array               The columns With Arabic Texts.
     */
    public static function ArabicCols($cols, $table = null)
    {

        if (is_null($table))
            $table = static::$table;
        $table = tables::where('name', $table)->first();
        $arabicCols = [];
        if (!$table)
            return $cols;

        $tableCols = $table->cols->pluck('TITLE', 'COLUMN_NAME')->toArray();
        $arabicCols = [];
        foreach ($cols as $index => $col)
            $cols[ $index ] = static::IsIn($tableCols, $col, $col);

        return $cols;
    }

    /** Get Table Columns
     *
     * @param   null $table The tabel Name.
     * @param bool $showAll
     * @param bool $toArray
     * @return array The columns With Arabic Texts.
     */
    public static function TableCols($table = null, $showAll = false, $toArray = true)
    {
        if (is_null($table))
            $table = static::$table;

        $cols = tables::where('name', $table)->first()->cols??null;
        if (is_null($cols))
            return [];

        if (!$showAll)
            $cols = $cols->where('SHOW_IN_LIST', 1);

        $cols = $cols->sortBy('ORDINAL_POSITION')
            ->pluck('COLUMN_NAME');

        if ($toArray)
            $cols = $cols->toArray();

        return $cols;
    }

    public static function getDataWhere($cols, $myWhere = null, $table = null, $id = 'id')
    {
        return static::getData($cols, $table, $id, $myWhere);
    }

    /** generate the dataTables
     *
     * @param   array $cols The columns names
     * @param   string $table Table Name
     * @param   string $id The primary Key
     * @param   string $myWhere add custom where in qurey.
     * @return  string              Json string for Ajax POST request.
     */
    public static function getData($cols, $table = null, $id = 'id', $myWhere = null)
    {
        if (is_null($table)) {
            $table = static::$table;
        }
        $arr = [];
        $i = 0;
        foreach ($cols as $rows) {
            if (is_array($rows)) {
                $code = static::IsIn($rows, 'formatter', null);
                if (is_null($code)) {
                    $code = static::IsIn($rows, 'code', null);
                    if (!is_null($code))
                        $code = function ($d, $row) use ($code) {
                            $str = '';
                            eval('$str = ' . $code . ';');

                            return $str;
                        };
                }
                $arr[] = [
                    'db'        => $rows[0],
                    'dt'        => $i,
                    'where'     => static::IsIn($rows, 'where', null),
                    'formatter' => $code
                ];
            }
            else
                $arr[] = [ 'db' => $rows, 'dt' => $i ];

            $i++;
        }

        return response()->json(SSP::getData($_POST, $table, $id, $arr, $myWhere), JSON_UNESCAPED_UNICODE);
    }

    /** to create 3 buttons [Edit,publish-unpublish,delete] for the table rows.
     *
     * @param   int $id id of table row
     * @param   string $route the route for the buttons
     * @param   tinyInt $statusVal status of publish
     * @param   array $btns what buttons shuold return, if empty=all;
     * @return  Html                  3 buttons
     */
    public static function controllersIcons($id, $route, $statusVal, $btns = [])
    {
        if (empty($btns))
            $btns = [ 'edit', 'status', 'delete' ];
        $btns_ = [
            'edit'    => [ 'info edit', 'edit', 'تحرير', 'edit' ],
            'delete'  => [ 'danger delete', 'delete', 'حذف', 'trash' ],
            'status'  => [ 'warning publish', 'publish', 'نشر', 'times' ],
            'status1' => [ 'success publish un - publish', 'publish', 'إلغاء النشر', 'check' ]
        ];

        $txt = "";
        foreach ($btns as $row) {
            if (is_array($row))
                $btn = $row;
            else {
                if ($row == 'status' && $statusVal)
                    $row .= '1';
                $btn = $btns_[ $row ];
            }
            $url = route_('admin.' . $route . '.' . $btn[1], $id);
            $txt .= static::TableBtn( $btn[0],
                "href='{$url}' id='{$btn[1]}_{$id}'",
                $btn[2],
                $btn[3],
                static::IsIn($btn, 4, ''));
        }
        if (count($btns) > 1)
            $txt = ' <div class="btn-group"> ' . $txt . '</div> ';

        return $txt;
    }

    /** Re format Json string to lines or tables
     *
     * @param   string $Str Json String.
     * @param   int $kind Type of Format [1=lines , 2=table]
     * @return  string          Formated Html String..
     */
    public static function deJsonArray($Str, $kind = 1)
    {
        $arr = json_decode($Str);
        $returnStr = '';
        if ($kind == 1) {
            $isFirst = 1;
            foreach ($arr as $row) {
                if ($isFirst != 1) {
                    $returnStr .= "<br />";
                }
                $returnStr .= ' <span class="label label-info">*</span> ' . $row;
                $isFirst++;
            }
        }

        return $returnStr;
    }

    /** Get Columns Names , types , data and other options
     *
     * @param   string $route The route for the form.
     * @param   string $table Table name to get the information from it and it comments.
     * @param   array $data To pass data to the columns.
     * @param   int $title To pass The Title that is defined in Table comments,[1=title , 2=new ,3=edit].
     * @param   string $include_view To include other path view to include it.
     * @return  View                    Return the final View with all it's parameters .
     */
    public static function NewEditView($route, $table = null, $data = null, $title = 2, $include_view = null)
    {

        if (is_null($table))
            $table = static::$table;
        $msg = 'There is no registered Table With name ' . $table;
        $table = tables::where('name', $table)->first();
        if (is_null($table))
            return redirect()->back()->with('alert', $msg);


        $cols = $table->colsForForm($data);

        foreach (static::$disabledCols as $row)
            unset($cols [ $row ]);

        if (static::IsIn($data, 'id', 0) > 0) {//is edit
            $title = 3;
            $route = [ $route, $data['id'] ];
        }

        $dataToPass = [ 'rows' => $cols, 'route' => $route, 'title' => static::_t(null, $title), 'name' => $table->name ];

        if (!is_null($include_view))
            $dataToPass['include_view'] = $include_view;
        foreach ($dataToPass['rows'] as $key => $avl)
            unset($data[ $key ]);
        if (is_array($data)&&count($data) >= 1)
            $dataToPass = array_merge($dataToPass, $data);


        return view(static::$newView, $dataToPass);
    }

    public static function SaveToTable($data = null, $table = null, $updateId = null, $affectedRow = true)
    {
        return static::SaveDataToTable($data, $table, $updateId, $affectedRow);
    }

    public static function SaveDataToTable($data = null, $table = null, $updateId = null, $affectedRow = false)
    {

        cache()->clear();
        $str = "good";
        if (is_null($data))
            $data = request()->all();
        if (is_null($table))
            $table = static::$table;
        $table = tables::where('name', $table)->first();
        $cols = $table->cols;

        $tableMOdel = $table->model;
        if (!is_null($updateId)) {
            $tableMOdel = call_user_func([ get_class($tableMOdel), 'find' ], $updateId);
            static::Sync($table->name, $updateId);
            $str = "Updated";
        }

        foreach ($cols as $col) {
            $col = $col->COLUMN_NAME;
            if (array_key_exists($col, $data) && !in_array($col, [ 'updated_at', 'created_at' ]))
                $tableMOdel->{$col} = $data[ $col ];
        }


        if ($cols->where('COLUMN_NAME', 'status')->count())
            $tableMOdel->status = static::IsIn($data, 'status', 0);


        if (method_exists($tableMOdel, 'beforeSaving'))
            $tableMOdel->beforeSaving();

        $tableMOdel->save();

        if (method_exists($tableMOdel, 'afterSaving'))
            $tableMOdel->afterSaving();

        session()->put('last_id', $tableMOdel->id);
        if ($affectedRow)
            return $tableMOdel;

        return json_encode($str);
    }

    public static function GetPluralWordForNum($num, $singularWord, $pluralWord, $OutNumWord = true)
    {
        $number = substr($num, -2, 2);
        $returnTxt = $singularWord;
        if ($number >= 2 && $number <= 10) {
            $returnTxt = $pluralWord;
        }
        if ($OutNumWord)
            $returnTxt = $num . ' ' . $returnTxt;

        return $returnTxt;
    }

    public static function GetPageTitle($title = '')
    {
        if ($title == '' || is_null($title)) {
            $rout = \Route::currentRouteName();
            $result = menu:: where('link', '=', $rout)->get();
            if ($result->count() >= 1) {
                $title = $result->first()->title;
            }
            else {
                $arr = explode('.', $rout);
                if (count($arr) > 2) {
                    $rout = implode('.', array_slice($arr, 0, 2));
                    $result = menu:: where('link', '=', $rout)->get();
                    if ($result->count() >= 1)
                        $title = $result->first()->title;
                }
            }
        }
        if (session()->has('title')) {
            if (session()->has('after_title'))
                $title .= ' ' . session()->get('title');
            else
                $title = session()->get('title') . ' ' . $title;
        }

        return $title;
    }

    public static function controllerMethods($className)
    {
        $actions = get_class_methods($className);
        if (is_array($actions)) {
            $disabledFuns = [ "__construct", "setupLayout", "init", "view", "ListView", "middleware", "getMiddleware", "callAction", "missingMethod", "__call", "authorize", "authorizeForUser", "parseAbilityAndArguments", "normalizeGuessedAbilityName", "authorizeResource", "resourceAbilityMap", "resourceMethodsWithoutModels", "dispatch", "dispatchNow", "validateWith", "validate", "validateWithBag", "withErrorBag", "throwValidationException", "buildFailedValidationResponse", "formatValidationErrors", "errorBag", "getRedirectUrl", "getValidationFactory" ];
            $actions = array_diff($actions, $disabledFuns);
        }
        else
            $actions = [];

        return $actions;
    }
}
