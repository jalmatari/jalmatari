<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Http\Controllers\Admin;

use Auth;
use AutoController;
use File;
use Illuminate\Support\Facades\Hash;
use Jalmatari\Funs\Funs;
use Jalmatari\Http\Controllers\Core\MyBaseController;
use Jalmatari\Models\permissions;
use Jalmatari\Models\users;
use Redirect;
use Validator;
use View;

class UsersController extends MyBaseController
{

    public function __construct()
    {
        $this->init();
    }


    public function getData($data = [])
    {
        $data = [
            "name"      => [ "name", 'code' => 'Funs::MenuUserinfo($row)' ],
            "job_title" => [ "job_title", 'code' => 'Funs::GetJobTitle($d)' ],
            //"group"     => [ "group", 'code' => 'Funs::UserGroups($row["id"])->pluck("name")->implode(" , ")' ]
        ];
        if (Funs::IsSpecialAuth('log_with_user'))
            $data["name"] = [
                "name",
                'formatter' => function ($name,$row) {
                    return Funs::MenuUserinfo($row)
                        . '<div class="text-center">'
                        . Funs::TableBtn("success",
                            'href="' . route("admin.users.login_as", $row["id"]) . '"',
                            " تسجيل الدخول كـ " . $name,
                            "user",
                            " تسجيل الدخول  ")
                        . "</div>";
                }
            ];

        //$_POST['columns'][9]['searchable'] = false;

        return parent::getData($data);

    }

    public function login_as($userId)
    {
        if (Funs::IsSpecialAuth('log_with_user')) {
            if (Auth::loginUsingId($userId)) {

                return redirect()->route('admin');
            }
        }

        return redirect()->back();
    }


    public function update()
    {
        $username = request('username');
        $users = users::where('username', $username)->get();
        $uersCount = $users->count();
        $id = request('id');
        if ($uersCount > 1 || ($uersCount == 1 && $users->first()->id != $id))
            return redirect()->back()->with('alert', "اسم المستخدم ({$username}) موجود مسبقاًّ،\\n يرجى التسجيل بحساب آخر.");
        $data = request()->all();

        $pass = $data['password'];
        if ($pass == "" || is_null($pass))
            unset($data['password']);
        else
            $data['password'] = Hash::make($pass);

        $arr2 = [];
        if (isset($data['permissions']))
            foreach ($data['permissions'] as $key => $val)
                $arr2[] = $key;
        $data['permissions'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);
        $groups = Funs::InInput('groups', []);
        Funs::InsertUserGroups($id, $groups);
        if (Funs::SaveDataToTable($data, null, $id) == '"Updated"')
            return redirect()->route($this->mainRoute);
    }


    public function save()
    {

        $data = request()->all();
        $data['password'] = Hash::make($data['password']);
        if (isset($data['photo']) && $data['photo'] == '')
            $data['photo'] = '/jalmatari/img/users/default-user.png';
        $arr2 = [];
        if (isset($data['permissions']))
            foreach ($data['permissions'] as $key => $val)
                $arr2[] = $key;
        $data['permissions'] = json_encode($arr2, JSON_UNESCAPED_UNICODE);
        $newUser = Funs::SaveDataToTable($data, null, null, 1);

        $groups = Funs::InInput('groups', []);
        Funs::InsertUserGroups($newUser->id, $groups);


        return redirect()->route($this->mainRoute);
    }


    public function delete($id)
    {
        if ($id == 1)
            return response()->json('لايمكن حذف مدير النظام سيسبب ذلك خطأ في النظام، يمكنك تعديله بدلاً من ذلك.');

        return parent::delete($id);
    }


    public function usersReports()
    {
        $users = users::all();
        $types = tdbr_ayat::tadarsTypesWithAr();

        return view('admin.users.users_reports', [ "users" => $users, 'counter' => 1, 'types' => $types ]);
    }

    public function permissions()
    {
        $data = request()->all();
        $user_permissions = json_encode(Funs::IsIn($data, 'selected_permissions', []));
        $job_permissions = permissions::where('id', $data['job_id'])->first();
        $permissions = (array) json_decode($job_permissions->permissions);
        $special_permissions = (array) json_decode($job_permissions->special_permissions);
        $permissions = array_merge($permissions, $special_permissions);

        return Funs::GetHtmlPermissionsForUser('permissions', $user_permissions, $permissions);


    }

}
