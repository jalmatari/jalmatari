<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Http\Controllers\Admin;

use Auth;
use AutoController;
use Jalmatari\Models\groups;
use Jalmatari\Funs\Funs;
use Redirect;
use View;
use Jalmatari\Http\Controllers\Core\MyBaseController;

class GroupsController extends MyBaseController
{

    public function __construct()
    {
        $this->init();
        $this->customCols = [ 'users' ];
    }

    public function getData($data = [])
    {

        $_POST['columns'][3]['searchable'] = false;
        $data = [ 'users' => [ 'users', 'code' => 'Funs::getGroupUsersMenu($row["id"])' ] ];

        return parent::getData($data);
    }


    public function update()
    {
        $arr = request()->all();

        $id = request('id');
        $users = Funs::InInput('users', []);
        $users = array_keys($users);
        Funs::InsertGroupUsers($id, $users);


        $arr2 = [];
        if (isset($arr['section_ids']))
            foreach ($arr['section_ids'] as $row)
                $arr2[] = $row;
        $arr['section_ids'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);

        $arr2 = [];
        if (isset($arr['book_id']))
            foreach ($arr['book_id'] as $row)
                $arr2[] = (int) $row;
        $arr['book_id'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);


        if (Funs::SaveDataToTable($arr, null, $id) == '"Updated"')
            return redirect()->route($this->mainRoute);
    }


    public function save()
    {
        $arr = request()->all();
        $arr2 = [];
        if (isset($arr['section_ids']))
            foreach ($arr['section_ids'] as $row)
                $arr2[] = $row;
        $arr['section_ids'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);

        $arr2 = [];
        if (isset($arr['book_id']))
            foreach ($arr['book_id'] as $row)
                $arr2[] = (int) $row;
        $arr['book_id'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);


        $newGroup = Funs::SaveDataToTable($arr, null, null, true);

        $users = Funs::InInput('users', []);
        $users = array_keys($users);
        Funs::InsertGroupUsers($newGroup->id, $users);

        return redirect()->route($this->mainRoute);
    }

    public function delete($id)
    {
        if ($id == 1)
            return response()->json('لايمكن حذف المجموعة الرئيسية سيسبب ذلك خطأ في النظام، يمكنك تعديلها بدلاً من ذلك.');

        return parent::delete($id);
    }

    public function api()
    {
        $kind = (request('kind'));
        if (is_null($kind))
            return response()->json("not-found");
        $g = request()->all();
        $data = $g;
        $section_id = (isset($g['section_id']) ? $g['section_id'] : 0);
        $book_id = ((isset($g['book_id']) && $g['book_id'] != "") ? $g['book_id'] : []);
        $group_id = (isset($g['group_id']) ? $g['group_id'] : 0);
        $sections = null;
        if ($kind == "book") {
            if (is_array($book_id) && !empty($book_id)) {
                foreach ($book_id as $book) {
                    $book_name = Funs::getBookName($book, 1);
                    $sections = Funs::GetBookSectionsList([ 'book_id' => $book, 'section_id' => $section_id ]);
                    $sections_in_other_groups = groups::select('section_ids')
                        ->where('id', '!=', $group_id)
                        ->where(function ($query) use ($book) {
                            $query->where('book_id', 'like', "[" . $book . ",%")
                                ->orWhere('book_id', 'like', "%," . $book . "]")
                                ->orWhere('book_id', 'like', "," . $book . ",%")
                                ->orWhere('book_id', "[" . $book . "]");
                        })
                        ->get()->toArray();
                    $sections_this_groups = groups::select('section_ids')
                        ->where('id', $group_id)
                        ->where(function ($query) use ($book) {
                            $query->where('book_id', 'like', "[" . $book . ",%")
                                ->orWhere('book_id', 'like', "%," . $book . "]")
                                ->orWhere('book_id', 'like', "," . $book . ",%")
                                ->orWhere('book_id', "[" . $book . "]");
                        })
                        ->get()->toArray();
                    $sections_this_groups_arr = [];
                    $sections_in_other_groups_arr = [];
                    foreach ($sections_in_other_groups as $section_in)
                        $sections_in_other_groups_arr = array_merge($sections_in_other_groups_arr, json_decode($section_in['section_ids']));
                    foreach ($sections_this_groups as $sections_this)
                        $sections_this_groups_arr = array_merge($sections_this_groups_arr, json_decode($sections_this['section_ids']));

                    //dd($sections_in_other_groups_arr,$sections_this_groups_arr);
                    foreach ($sections as $key => $val)
                        $data['sections'][] = [
                            'key'      => $book . '_' . $key,
                            'value'    => $val . ' - ' . $book_name,
                            'disabled' => in_array($book . '_' . $key, $sections_in_other_groups_arr),
                            'selected' => in_array($book . '_' . $key, $sections_this_groups_arr)
                        ];

                }
            }
            else {
                $data['sections'] = [];
            }
        }

        return response()->json($data);
    }

}
