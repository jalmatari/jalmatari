<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Http\Controllers\Admin;

use Auth;
use AutoController;
use DB;
use Input;
use Jalmatari\Http\Controllers\Core\MyBaseController;
use Jalmatari\Models\settings;
use Redirect;
use Schema;
use View;

class SettingsController extends MyBaseController
{

    public function __construct()
    {
        $this->init();
    }


    public function index()
    {
        return view('admin.settings.index', [
            "settings" => settings::all(),
            "section"  => 'main',
            'sub_view' => 'admin.settings.settings',
            'title'    => "إعدادت الموقع"
        ]);
    }

    public function updateTable($arr, $name, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        $settings = settings::where('name', '=', $name)->firstOrFail();
        $settings->value = $value;
        $settings->save();
    }

    public function update()
    {

        $checkBoxes = settings::where('type', '=', 'checkbox')->get();
        $lists = settings::where('type', '=', 'list')->get();
        $arr = request()->all();

        foreach ($lists as $list) {
            $listArr = json_decode($list->value);
            $listArr1 = [];
            foreach ($listArr as $listKey => $listVal)
                $listArr1[ $listKey ] = $listVal;
            $temArr = [ $arr[ $list->name ] => $listArr1[ $arr[ $list->name ] ] ];
            unset($listArr1[ $arr[ $list->name ] ]);
            $listArr1 = $temArr + $listArr1;
            $arr[ $list->name ] = json_encode($listArr1, JSON_UNESCAPED_UNICODE);
        }
        array_shift($arr);
        //array_shift($arr);
        foreach ($arr as $key => $value) {
            $name = $key;
            $value = $arr[ $key ];
            $this->updateTable($arr, $key, $value);
        }
        foreach ($checkBoxes as $row) {
            if (array_key_exists($row->name, request()->all()))
                $this->updateTable($arr, $row->name, 1);
            else
                $this->updateTable($arr, $row->name, 0);
        }
        cache()->clear();

        return redirect()->back();
    }

    public function save()
    {
        $arr = Schema::getColumnListing('settings');
        array_shift($arr);

        $settings = new settings;
        foreach ($arr as $row)
            $settings->{$row} = request($row);
        $settings->save();

        return redirect()->route('admin.settings');
    }

}
