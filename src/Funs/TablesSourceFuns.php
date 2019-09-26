<?php
/**
 * Created by PhpStorm.
 * User: jalmatari
 * Date: 13/7/2019
 */

namespace Jalmatari\Funs;


use Jalmatari\Models\author;
use Jalmatari\Models\groups;
use Jalmatari\Models\library;
use Jalmatari\Models\Menu;
use Jalmatari\Models\paths;
use Jalmatari\Models\permissions;
use Jalmatari\Models\sections;
use Jalmatari\Models\sora;
use Jalmatari\Models\tags;
use Jalmatari\Models\tdbr_sources;
use Jalmatari\Models\tdbr_tadabor_cats;
use Jalmatari\Models\users;
use Auth;
use DB;
use Session;
//TODO Need Enhance or distribute Methods To connected Models
trait TablesSourceFuns
{


    public static function GetUserJobs($job_title = 0)
    {
        $jobs = permissions::where('status', 1)->get();
        $jobs = static::GetArrWithFirstItemFromObject($jobs, $job_title, 'id', 'name');

        return $jobs;
    }


    public static function GetUserGroups($groups = [])
    {
        $resuls = groups::where('status', 1)->get();
        $theData = [];
        $final_arr = [];
        foreach ($resuls as $row) {
            if (in_array($row->id, $groups)) {
                $final_arr['selected'][] = $row->id;
            }
            $theData[ $row->id ] = $row->name;
        }
        if (!empty($groups)) {
            $final_arr['data'] = $theData;
        }
        else {
            $final_arr = $theData;
        }

        return $final_arr;
    }

    public static function GetGroupsList($group_id = 0)
    {
        if ($group_id == 0) {
            $group_id = Auth::user()->group;
            $group_id = json_decode($group_id);
            $group_id = static::IsIn($group_id, 0, 0);
        }
        $groups = groups::where('status', '=', '1')->get();
        $groups = static::GetArrWithFirstItemFromObject($groups, $group_id, 'id', 'name');

        return $groups;
    }


    public static function HtmlGroupUsersToCheck($group_id)
    {

        $users = users::where('status', '=', '1')->orderBy('job_title', 'asc')->get();
        $html = '<div class="users_fileds">'
            . '<div class="box-header  with-border">'
            . '<input type="checkbox" id="usersAllCheck" class="checker check-all-inputs" data-group-name="user_checker" /> &nbsp; '
            . '<h3 class="box-title">قائمة الموظفين</h3><div class="clear"></div></div>';
        $usersIds = static::GroupUsers($group_id)->pluck("id")->toArray();
        $lastUserJob = 0;
        foreach ($users as $user) {
            $job_title = $user->job->name;
            $cssClass = [ 'class' => 'checker user_checker' ];
            if ($lastUserJob != $user->job_title) {
                if ($lastUserJob > 0) {
                    $html .= '</ul></blockquote>';
                }
                $html .= '<blockquote><small class="text-yellow">' . $job_title . '</small><ul class="users-list clearfix">';
            }
            $lastUserJob = $user->job_title;
            //dd(Funs::UserGroups($user->id)->pluck('name'));
            $html .=
                '<li>'
                . '<span class="user-chk">'
                . static::Form('checkbox', [ 'users' . '[' . $user->id . ']', $user->name, in_array($user->id, $usersIds), $cssClass ])
                . '</span>'
                . '<a class="users-list-name" href="' . route_('admin.users.edit', $user['id']) . '">' . $user['name'] . '</a>'
                . '<sup class="user-job text-green">(' . $job_title . ')</sup> '
                . '<br><h6 class="text-light-blue">(' . implode(" , ", Funs::UserGroups($user->id)->pluck("name")->toArray()) . ')</h6> '
                . '</li> ';
        }

        return $html . '</ul></blockquote></div>';
    }

    public static function fun_get_permissions_fileds($name, $text)
    {

        $menus = menu::where('status', '=', '1')->orderBy('parent', 'asc')->orderBy('ord', 'asc')->get();
        $html = '<div class="permissions_fileds">'
            . '<div class="box-header  with-border">'
            . '<input type="checkbox" id="permissionsAllCheck" class="checker check-all-inputs" data-group-name="permission_checker" /> &nbsp; '
            . '<h3 class="box-title">قائمة الصلاحيات</h3><div class="clear"></div></div>';
        $permissions = json_decode($text);
        $permissions = (is_null($permissions) ? [] : $permissions);
        foreach ($menus as $row) {
            $html .=
                '<div class="permission_filed col-md-3 check form-group">'
                . '<div class="lbl">' .
                static::Form('label', [ $name . '[' . $row->name . ']', $row->title ])
                /*. (($row->parent == 0)
                ? '<span class="label label-info" style="margin-right: 5px;">رئيسية</span>'
                : '<span class="label" style="margin-right: 5px;">فرعية</span>')*/
                . '</div><div class="chk">'
                . static::Form('checkbox', [ $name . '[' . $row->name . ']', $row->id, in_array($row->name, $permissions), [ 'class' => 'checker permission_checker' ] ])
                . '</div></div>';
        }

        return $html . '</div>';
    }

    public static function fun_get_special_permissions_fileds($name, $text)
    {

        $specialPermissions = j_config('permissions');
        $html = '<div class="permissions_fileds  col-md-12">'
            . '<div class="box-header  with-border"></div>';
        $permissions = json_decode($text);
        $permissions = (is_null($permissions) ? [] : $permissions);
        foreach ($specialPermissions as $permission => $title) {
            $html .=
                '<div class="permission_filed col-md-3 check form-group">'
                . '<div class="lbl">' .
                static::Form('label', [ $name . '[' . $permission . ']', $title ])
                . '</div><div class="chk">'
                . static::Form('checkbox', [ $name . '[' . $permission . ']', $permission, in_array($permission, $permissions), [ 'class' => 'checker permission_checker' ] ])
                . '</div></div>';
        }

        return $html . '</div>';
    }

    public static function GetMultiListData($name, $data = '')
    {
        return static::CallStaticFun('GetListFor' . ucfirst($name), $data);
    }

    public static function UsersList($with_job = false)
    {
        $name = 'name';
        if ($with_job)
            $name = DB::raw('concat(name," (",(select name from jalm_permissions where id=job_title),")") as name');

        return users::select($name, 'id')->pluck('name', 'id');
    }

    public static function FontAwesomeIcons($icon = '')
    {
        return [ 'fa-info-circle' => '<i class="fa fa-info-circle"></i> fa-info-circle' ];
    }

    public static function ActiveUserId($userId = null)
    {
        return static::UserId($userId);
    }
}
