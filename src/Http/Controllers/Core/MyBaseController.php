<?php

namespace Jalmatari\Http\Controllers\Core;

use Auth;
use AutoController;
use DB;
use HTML;
use Jalmatari\Funs\Funs;
use Jalmatari\JalmatariServiceProvider;
use Jalmatari\Models\menu;
use Jalmatari\Models\settings;
use Jalmatari\Models\tables;
use Redirect;

class MyBaseController extends Controller
{

    protected $layout;
    protected $mainRoute;
    protected $mainView;
    protected $table;
    protected $tableModel;
    protected $name;
    protected $listBtns = [ "edit", "delete" ];
    protected $customCols = [];
    protected $newEditData = null;
    protected $saveUpdateData = null;
    protected $customWhere = null;
    protected $settingsSection = 'main';
    protected $settingsViewFile = 'admin.settings.settings';
    protected $jalmatariDataTables = [];
    //private methods
    protected $privateMethods = [
        "__construct",
        "getData",
        "login_as",
        "update",
        "save",
        "delete",
        "api",
        "init",
        "index",
        "add",
        "edit",
        "getDataWithWhere",
        "publish",
        "actionToMulti",
        "view",
        "ListView",
        "middleware",
        "getMiddleware",
        "callAction",
        "missingMethod",
        "__call",
        "authorize",
        "authorizeForUser",
        "parseAbilityAndArguments",
        "normalizeGuessedAbilityName",
        "authorizeResource",
        "resourceAbilityMap",
        "resourceMethodsWithoutModels",
        "dispatch",
        "dispatchNow",
        "validateWith",
        "validate",
        "validateWithBag",
        "withErrorBag",
        "throwValidationException",
        "buildFailedValidationResponse",
        "formatValidationErrors",
        "errorBag",
        "getRedirectUrl",
        "getValidationFactory",
        "artisan",
        "getMethods",
        "settings",
        "orderMenus",
        "generateArtisanTable"
    ];

    public function __construct()
    {
        $this->init();
    }


    public function init($table = null, $isAdmin = true)
    {
        if (strpos(request()->path(), 'admin') !== false) {
            if (is_null($table)) {
                $table = get_class($this);
                $table = explode('\\', $table);
                $table = last($table);
                $table = substr($table, 0, -10);//remove last Controller from name of calss
                $table = strtolower($table);
            }
            if (\Schema::hasTable($table)) {
                $this->table = $this->mainRoute = $table;
                $this->tableModel = tables::where('name', $table)->first();
                $menu = menu::where('name', $table)->first();
                if ($menu)
                    $this->mainRoute = $menu->link;
                Funs::setTable($table);
            }
        }
    }


    public function index()
    {
        $this->init();
        $tableCols = Funs::TableCols($this->table);
        $tableCols = array_merge($tableCols, $this->customCols);
        if (count($this->listBtns))//if user want to remove Action btns
            $tableCols[] = 'الأوامر';
        $tableCols = array_merge([ Funs::Form("checkbox", [ "datatable-check-all", null, null, [ 'id' => "datatable-check-all" ] ]) ], $tableCols);

        return $this->ListView($tableCols);
    }


    public function add()
    {

        return Funs::NewEditView($this->mainRoute . '.save', $this->table, $this->newEditData, 2);
    }

    public function edit($id)
    {

        $table = (array) json_decode(json_encode(call_user_func([ $this->tableModel->fullName, 'find' ], $id), true));
        if (!is_null($this->newEditData))
            $table = array_merge($table, $this->newEditData);

        return Funs::NewEditView($this->mainRoute . '.edit', $this->table, $table);
    }

    public function update()
    {

        cache()->clear();
        if (Funs::SaveDataToTable($this->saveUpdateData, null, request('id')) == '"Updated"')
            return redirect()->route($this->mainRoute);
    }

    public function save()
    {

        cache()->clear();
        if (Funs::SaveDataToTable($this->saveUpdateData) == '"good"')
            return redirect()->route($this->mainRoute);
    }

    public function getData($data = [])
    {
        $tableColsAll = Funs::TableCols($this->table, true);
        $tableCols = Funs::TableCols($this->table);
        $tableCols = array_merge($tableCols, $this->customCols);

        if (count($this->listBtns))//if user want to remove Action btns
            $tableCols[] = [
                'id',
                'formatter' => function ($idCol, $row) {
                    return Funs::controllersIcons($idCol, $this->table, Funs::IsIn($row, "status", 0), $this->listBtns);

                }
            ];

        $tableCols = Funs::UnDuplicateArray(array_merge($tableCols, $tableColsAll));
        $tableCols = array_merge([
            [
                'id',
                'formatter' => function ($idCol, $row) {
                    return (string) Funs::Form("checkbox", [ "id[]", $idCol, "", [ "class" => "datatable-check-row" ] ]);
                }
            ]
        ], $tableCols);

        foreach ($tableCols as $key => $tableCol) {
            if (is_string($tableCol) && isset($data[ $tableCol ]))
                $tableCols[ $key ] = $data[ $tableCol ];
            elseif ('status' == $tableCol)
                $tableCols[ $key ] = [
                    'status',
                    'formatter' => function ($col, $row) {
                        return Funs::controllersIcons($row["id"], $this->table, $col, [ "status" ]);
                    }
                ];
            else if (in_array($tableCol, [ 'created_at', 'updated_at' ]))
                $tableCols[ $key ] = [
                    $tableCol,
                    'formatter' => function ($date) {

                        return Funs::Date($date);
                    }
                ];
            else if ($tableCol == "user_id")
                $tableCols[ $key ] = [
                    $tableCol,
                    'formatter' => function ($col) {
                        return Funs::UserName($col);
                    }
                ];
        }

        return $this->getDataWithWhere($tableCols);
    }

    public function getDataWithWhere($cols, $myWhere = null, $table = null, $id = 'id')
    {
        if (is_null($myWhere) && !is_null($this->customWhere))
            $myWhere = $this->customWhere;

        return Funs::getData($cols, $table, $id, $myWhere);
    }

    public function publish($id, $status = null)
    {
        $table = call_user_func([ $this->tableModel->fullName, 'find' ], $id);
        if (is_null($status))
            $status = $table->status == 1 ? 0 : 1;
        $table->status = $status;
        $table->save();

        $publish = $status == 1 ? 2 : 3;
        Funs::Sync($this->table, $id, $publish);

        return response()->json($id);
        //return redirect()->route($this->mainRoute);
    }

    public function delete($id)
    {

        call_user_func([ $this->tableModel->fullName, 'destroy' ], $id);
        Funs::Sync($this->table, $id, 0);
        if (request('returnedId'))
            $id = (int) request('returnedId');

        return response()->json($id);
    }


    public function actionToMulti()
    {
        $items = [];

        $ids = request('ids');
        if (count($ids) >= 1) {
            $type = request('type');
            if (in_array($type, [ 'publish', 'un_publish', 'delete' ])) {
                foreach ($ids as $id) {
                    if ($type == 'delete')
                        $items[] = $this->delete($id)->original;
                    else//'publish', 'un_publish'
                        $items[] = $this->publish($id, $type == 'publish' ? 1 : 0)->original;

                }
            }
            else {
                request()->merge([ 'ac' => $type ]);
                $items[] = $this->api();
            }
        }

        return response()->json($items);
    }

    public function ListView($cols, $table = null, $view = null, $title = 1, $other_view = null)
    {
        return Funs::ListView($cols, $table, $view, $title, $other_view);
    }

    public function getMethods()
    {
        return array_diff(get_class_methods($this), $this->privateMethods);
    }


    public function api()
    {

        $data = [];
        $ac = request('ac');
        if (!in_array($ac, $this->privateMethods) && method_exists($this, $ac))
            $data = $this->{$ac}();//call the function

        if (session()->has('alert')) {
            if (is_scalar($data))
                $data = (object) [ "data" => $data ];
            $data->withAlert = session('alert');
            session()->forget('alert');
        }
        if (is_scalar($data) && session()->has('plain-data')) {
            header("Content-Type: text/plain");
            die($data);
        }

        if (is_a($data, 'Illuminate\View\View', true) || is_a($data, 'Illuminate\Http\Response', true))
            return $data;

        return response()->json($data);
    }


    public function orderMenus()
    {
        $ordered = false;
        $table = $this->tableModel->fullName;
        $table = new $table;
        $table = $table::where('id', request('id'))->first();

        if (is_null($table->ord) || isset($table->ord)) {
            $orderBy = (int) request('order_by');
            $table->ord = $table->ord + $orderBy;
            $table->save();
            $ordered = true;
        }

        return $ordered;
    }


    public function settings()
    {
        return view('admin.settings.index', [
            "settings" => settings::all(),
            'section'  => $this->settingsSection,
            'names'    => json_decode(settings::find(1)->value),
            'sub_view' => $this->settingsViewFile,
            'title'    => "إعدادت الموقع"
        ]);
    }

    /**
     * @param string $name
     */
    public function artisan($name)
    {
        $tableName = $name;
        if ($name == 'generate_all_tables') {
            $tables = [
                "errors",
                "groups",
                "menu",
                "permissions",
                "settings",
                "sync",
                "tables",
                "tables_cols",
                "routes",
                "controllers",
                "users",
                "users_settings",
            ];
            $tableName = implode(',', $tables);
            foreach ($tables as $table)
                $this->generateArtisanTable($table);

        }
        else
            $this->generateArtisanTable($name);

        Funs::Abort(200, "Table with Name ($tableName) was created successfully!#home", true);
    }

    private function generateArtisanTable($tableName)
    {

        $path = JalmatariServiceProvider::path('database/migrations');
        $files = scandir($path);
        $fileName = '';
        foreach ($files as $file)
            if (strlen($file) > 10) {
                $file_ = substr($file, strpos($file, '_create_') + 8);
                $file_ = substr($file_, 0, strpos($file_, '_table.php'));
                if ($tableName == $file_) {
                    $fileName = $file_;
                    break;
                }
            }
        if ($fileName == '')
            Funs::Abort(503, "There is no migrate for Table with Name ($tableName)!", true);
        else {
            $path .= '/' . $file;
            $path = substr($path, strpos($path, 'vendor/'));
            $path = str_replace('Http/Controllers/../../', '', $path);
            if (count($this->jalmatariDataTables) == 0)
                $this->jalmatariDataTables = require JalmatariServiceProvider::path('database/insertDatabaseData.php');

            \Artisan::call('migrate --path ' . $path);
            //if it's Users or gorups table, generate users_groups table
            if (in_array($tableName, [ 'groups', 'users' ]))
                $this->generateArtisanTable('users_groups');
            if (isset($this->jalmatariDataTables[ $fileName ])) {
                DB::table($fileName)->insert($this->jalmatariDataTables[ $fileName ]);
                cache()->clear();
            }
        }
    }
}
