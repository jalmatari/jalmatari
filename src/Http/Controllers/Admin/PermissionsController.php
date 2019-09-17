<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Http\Controllers\Admin;

use Auth;
use AutoController;
use Input;
use Jalmatari\Funs\Funs;
use Jalmatari\Http\Controllers\Core\MyBaseController;
use Redirect;
use View;

class PermissionsController extends MyBaseController
{

    public function __construct()
    {
        $this->init();
    }

    public function update()
    {
        $arr = request()->all();
        $arr2 = [];
        if (isset($arr['permissions']))
            foreach ($arr['permissions'] as $key => $val)
                $arr2[] = $key;
        $arr['permissions'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);
        $arr2 = [];
        if (isset($arr['special_permissions']))
            foreach ($arr['special_permissions'] as $key => $val)
                $arr2[] = $key;
        $arr['special_permissions'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);
        if (Funs::SaveDataToTable($arr, null, request('id')) == '"Updated"')
            return redirect()->route($this->mainRoute);
    }


    public function save()
    {
        $arr = request()->all();
        $arr2 = [];
        if (isset($arr['permissions']))
            foreach ($arr['permissions'] as $key => $val)
                $arr2[] = $key;
        $arr['permissions'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);
        $arr2 = [];
        if (isset($arr['special_permissions']))
            foreach ($arr['special_permissions'] as $key => $val)
                $arr2[] = $key;
        $arr['special_permissions'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);
        if (Funs::SaveDataToTable($arr) == '"good"')
            return redirect()->route($this->mainRoute);
    }

    public function getData($data = [])
    {
        $special_permissions = config('jalmatari.permissions');
        $data = [
            'permissions' => [
                'permissions',
                'formatter' => function ($col, $row) use ($special_permissions) {

                    $txt = '';
                    $col2 = json_decode($row['special_permissions']);
                    if (count($col2) >= 1)
                        $txt = '<div class="clearfix"></div><h5 class="text-aqua">صلاحيات خاصة:</h5>';
                    foreach ($col2 as $permission)
                        if(isset($special_permissions[ $permission ]))
                        $txt .= '<div class="permission-menu text-muted">
<i class="fa fa-unlock-alt text-aqua"></i> ' . $special_permissions[ $permission ] . '</div>';

                    return Funs::getPermissionsMenu($col) . $txt;
                }
            ]
        ];

        return parent::getData($data);
    }

    public function delete($id)
    {
        if ($id == 2)
            return response()->json('لايمكن حذف مدير النظام سيسبب ذلك خطأ في النظام، يمكنك تعديله بدلاً من ذلك.');

        return parent::delete($id);
    }


}
