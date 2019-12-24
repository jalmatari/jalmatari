<?php
/**
 * Created by PhpStorm.
 * User: jalmatari
 * Date: 13/7/2019
 */

namespace Jalmatari\Funs;

use Jalmatari\Models\groups;
use Jalmatari\Models\Menu;
use Jalmatari\Models\permissions;
use Jalmatari\Models\users;
use Jalmatari\Models\users_groups;
use Jalmatari\Models\users_settings;
use Auth;
//TODO: Need Improvements specially This Things That Can Replaced by Models
trait UsersAndGroups
{
    public static function Users()
    {
        return users::all();
    }

    public static function GetUserName($uid, $name = false, $job = false, $withSmallTag = true)
    {
        if ($uid == 0)
            return "زائر";
        $user = users::where('id', '=', $uid)->first();
        if (is_null($user))
            return 'مستخدم تم حذفه!';
        $val = $user;
        if ($name && $job) {
            $val = $user->name . ($withSmallTag ?
                    ' <small class="text-yellow job-name">(' . static::GetJobTitle($user->job_title) . ")</small>" : ' (' . static::GetJobTitle($user->job_title) . ')');
        }
        elseif ($name) {
            $val = $user->name;
        }
        elseif ($job) {
            $val = static::GetJobTitle($user->job_title);
        }

        return $val;
    }

    public static function UserName($uid, $job = false, $withSmallColoredTag = false)
    {
        $name = true;

        return static::GetUserName($uid, $name, $job, $withSmallColoredTag);
    }

    public static function getPermissionsMenu($jsonText)
    {
        $html = "";
        $permissions = json_decode($jsonText);
        if (empty($permissions)) {
            return '<div class="text-red"><i class="fa fa-close"></i> &nbsp; لايوجد أي صلاحيات لدخول لوحة التحكم &nbsp; </div>';
        }

        $menus =menu::where('status', 1)->get()->toArray();
        foreach ($menus as $row)
            $html .= (in_array($row['name'], $permissions) ? '<div class="permission-menu "><i class="fa ' . $row['icon'] . ' text-yellow"></i> <sub>' . $row['title'] . '</sub></div>' : '');


        return $html;
    }

    public static function GetHtmlPermissionsForUser($name, $permissions, $job_permissions)
    {
        $menus = menu::where('status', '=', '1')->orderBy('parent', 'asc')->orderBy('ord', 'asc')->get();
        $html = '<div class="user_permissions_fileds col-md-12"><div class="permissions_fileds">'
            . '<div class="box-header  with-border">'
            . '<h3 class="box-title text-blue">إضافة صلاحيات آخرى للمستخدم، بالإضافة إلى صلاحيات الوظيفة :</h3><div class="clear"></div></div>';
        $permissions = (array) json_decode($permissions);
        $permissions = (is_null($permissions) ? [] : $permissions);
        foreach ($menus as $row) {
            $disabled = false;
            if (in_array($row->name, $job_permissions)) {
                $disabled = true;
            }

            $html .=
                '<div class="permission_filed col-md-3 check ">'
                . '<div class="lbl">' .
                static::Form('label', [ $name . $disabled . '[' . $row->name . ']', $row->title ])
                /*. (($row->parent == 0)
                ? '<span class="label label-info" style="margin-right: 5px;">رئيسية</span>'
                : '<span class="label" style="margin-right: 5px;">فرعية</span>')*/
                . '</div><div class="chk">'
                . static::Form('checkbox', [ $name . $disabled . '[' . $row->name . ']', $row->name, (in_array($row->name, $permissions) || in_array($row->name, $job_permissions)), [ 'class' => 'checker ' . ($disabled ? '' : 'permission_checker'), ($disabled ? 'disabled' : '') ] ])
                . '</div></div>';

        }
        $html .= '</div> <div class="h4  text-blue">صلاحيات خاصة :</div><hr>';

        $specialPermissions = j_config('permissions');
        foreach ($specialPermissions as $permission => $title) {
            $disabled = false;
            if (in_array($permission, $job_permissions)) {
                $disabled = true;
            }

            $html .=
                '<div class="permission_filed col-md-3 check ">'
                . '<div class="lbl">' .
                static::Form('label', [ $name . $disabled . '[' . $permission . ']', $title ])
                . '</div><div class="chk">'
                . static::Form('checkbox', [ $name . $disabled . '[' . $permission . ']', $permission, (in_array($permission, $permissions) || in_array($permission, $job_permissions)), [ 'class' => 'checker ' . ($disabled ? '' : 'permission_checker'), ($disabled ? 'disabled' : '') ] ])
                . '</div></div>';
        }

        return $html . '</div>';
    }

    public static function getGroupUsersMenu($group_id)
    {
        $html = '<ul class="users-list clearfix">';
        $users = static::GroupUsers($group_id);
        if (count($users) <= 0) {
            return '<li class="text-red"><i class="fa fa-close"></i> &nbsp; لايوجد أعضاء في هذه المجموعة! &nbsp; </li>';
        }

        foreach ($users as $row) {
            $datetime = explode(" ", $row['updated_at']);

            $job_title = permissions::find($row['job_title'])->name;
            $html .= '<li  data-toggle="tooltip" title="' . $row['username'] . '">'
                . '<img src="' . $row['photo'] . '" alt="' . $row['name'] . '">'
                . '<a class="users-list-name" href="' . route_('admin.users.edit', $row['id']) . '">' . $row['name'] . '</a>'
                . '<br><small class="text-green"> (' . $job_title . ')</small>'
                . '<span class="users-list-date">' . static::convertHijri($datetime[0]) . ' ' . $datetime[1] . '</span></li> ';
        }

        return $html . '</ul>';
    }


    /**
     * Get User Informations
     *
     * @param int $id User Id
     * @return object UserInfos
     */

    public static function User($id = null)
    {
        return static::GetUser($id);
    }

    public static function GetUser($userId = null)
    {
        $userId = static::UserId($userId);

        $user = users::where('id', $userId)->first();
        unset($user->password);
        unset($user->remember_token);

        return $user;
    }

    public static function GetJobTitle($id)
    {
        $job = static::GetJob($id);
        if (!is_null($job))
            $job = $job->name;
        else
            $job = 'تم حذف الوظيفة من النظام!!';

        return $job;
    }

    public static function GetJob($id)
    {
        return permissions::where('id', '=', $id)->first();
    }

    public static function MenuUserinfo($row)
    {
        return '<div class="user-panel text-center">
                <div class="col-md-12 image">
                    <img src="' . $row['photo'] . '" class="img-circle" alt="User Image">
                </div>
                <div class="col-md-12">
                    <p class="text-yellow">' . $row['name'] . '</p>
                    <small class="text-red">(' . $row['username'] . ')</small>
                </div>
            </div>';
    }

    public static function GetListAllUsers($withJob = 1, $job_id = 0)
    {
        $users = null;
        if ($job_id == 0) {
            $users = users::where('status', '1')->get();
        }
        else {
            $users = users::where('status', '1')->where('job_title', $job_id)->get();
        }

        $final_arr = [];
        foreach ($users as $user) {
            if (!$withJob) {
                $final_arr[ $user->id ] = $user->name;
            }
            else {
                $final_arr[ $user->id ] = $user->name . ' (' . static::GetJobTitle($user->job_title) . ')';
            }

        }

        return $final_arr;
    }

    public static function GetListAllUsersInMyGroups($withJob = 1, $emailes = false)
    {
        $curUserGroups = json_decode(Auth::user()->group);
        $users = users::where('status', '1')->get();
        $final_arr = [];
        foreach ($users as $user) {
            $userGroups = json_decode($user->group);
            foreach ($userGroups as $userGroup) {
                if (in_array($userGroup, $curUserGroups) || static::IsSpecialAuth('send_messages_to_all')) {
                    $tem = '';
                    if (!$withJob) {
                        $tem = $user->name;
                    }
                    else {
                        $tem = $user->name . ' (' . static::GetJobTitle($user->job_title) . ')';
                    }
                    if ($emailes) {
                        if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            $tem .= ' [' . $user->email . ']';
                        }
                        else {
                            $final_arr['disabled'][] = $user->id;
                            $tem .= ' * لايوجد بريد إلكتروني.';
                        }
                        $final_arr['data'][ $user->id ] = $tem;
                    }
                    else {
                        $final_arr[ $user->id ] = $tem;
                    }

                    break;
                }
            }
        }

        return $final_arr;

    }

    public static function GetUserProjectSectionsList($section_id = 0)
    {
        $final_arr = [];
        $groups = static::GetUser(0, 0, 0, 1)->group;
        foreach ($groups as $group) {
            $preFix = ((count($groups) > 1) ? '  - مُسند لمجموعة (' . $group->name . ')' : '');
            $section_ids = (array) $group->section_ids;
            foreach ($section_ids as $row) {
                $row = (object) $row;
                if ($section_id == $row->id) {
                    $final_arr = [ $row->id => $row->title . $preFix ] + $final_arr;
                }
                else {
                    $final_arr[ $row->id ] = $row->title . $preFix;
                }
            }
        }

        return $final_arr;
    }

    public static function GetUsersIdsWithAuth($auth)
    {
        $users = users::where('status', 1)->get();
        $ids = [];
        foreach ($users as $user) {
            if (static::IsSpecialAuth($auth, $user))
                $ids[] = $user->id;
        }

        return $ids;
    }


    public static function GroupForUsers($users = [])
    {
        $users = static::GetUnDuplicateArr($users);
        $groups = groups::all();
        $group_id = 1;
        foreach ($groups as $group) {
            $group_users = json_decode($group->users);
            if (count(array_intersect($users, $group_users)) == count($users))
                $group_id = $group->id;
        }

        return $group_id;
    }

    public static function UsersOfGroup($group_id, $usersObject = false)
    {

        $users = groups::where('id', $group_id)->first();
        if (is_null($users))
            $users = [];
        else
            $users = json_decode($users->users);
        if ($usersObject)
            $users = users::whereIn('id', $users)->get()->toArray();

        return $users;
    }

    public static function UsersForTagStatus($tagStatus)
    {
        $transJobs = static::SettingAsArr('jobs_for_tasks');
        $job_id = static::IsIn($transJobs, $tagStatus, 0);
        $users = users::where('job_title', $job_id)->get();

        return $users;
    }

    public static function UsersListWithJobGroups($byGroup = false)
    {
        $users = users::orderBy('job_title')->get();
        $usersList = [];
        $tem_arr = [];
        $job_id = 0;
        $job_title = '';
        foreach ($users as $user) {
            if ($job_id != $user->job_title && $job_id != 0) {
                $usersList[ $job_title ] = $tem_arr;
                $tem_arr = [];
            }
            $user_name = $user->name;
            if ($byGroup) {
                $group = $user->groups->pluck('group_id')->toArray();
                $group = static::Groups($group)->pluck('name')->toArray();
                $user_name .= ' (' . implode(' , ', $group) . ') ';

            }
            $tem_arr[ $user->id ] = $user_name;
            $job_id = $user->job_title;
            $job_title = $user->job->name;

        }
        if (count($tem_arr) >= 1)
            $usersList[ $job_title ] = $tem_arr;

        return $usersList;
    }

    /**
     * @param $group_id
     * @param $job_id
     * @param array $mustUsers users model
     * @param null $group groups model
     * @return int
     */
    public static function UserOfGroupForJob($group_id, $job_id, $mustUsers = [], $group = null)
    {
        $userId = 0;
        $groupUsers = [];
        if (is_null($group))
            $group = groups::where('id', $group_id)->first();
        if (!is_null($group)) {
            $groupUsers = json_decode($group->users);
            $groupUsers = users::whereIn('id', $groupUsers)->get();
        }

        foreach ($groupUsers as $user) {
            if ($user->job_title == $job_id) {
                $userId = $user->id;
                break;
            }
        }
        foreach ($mustUsers as $user) {
            if (!is_a($user, 'Illuminate\Database\Eloquent\Collection')) {
                $user = users::where('id', $user)->first();
            }
            if (!is_null($user) && $user->job_title == $job_id) {
                $userId = $user->id;
                break;
            }
        }

        return $userId;
    }

    public static function UserGroups($userId)
    {
        $users_groups = users_groups::where('user_id', $userId)->get();
        $groups = $users_groups->pluck('group');

        return $groups;
    }

    public static function GroupUsers($groupId)
    {
        $users_groups = users_groups::where('group_id', $groupId)->get();
        $users = $users_groups->pluck('user');
        $users = $users->filter(function ($item) {
            return !is_null($item);
        });

        return $users;
    }

    public static function InsertUserGroups($userId, $groups)
    {
        users_groups::where('user_id', $userId)->delete();
        foreach ($groups as $group) {
            users_groups::insert([ 'user_id' => $userId, 'group_id' => $group ]);
        }

    }

    public static function InsertGroupUsers($groupId, $users)
    {
        users_groups::where('group_id', $groupId)->delete();
        foreach ($users as $user) {
            users_groups::insert([ 'group_id' => $groupId, 'user_id' => $user ]);
        }

    }

    public static function Groups($groups_ids)
    {
        $groups = groups::whereIn('id', $groups_ids)->get();

        return $groups;
    }

    public static function UserId($userId = null)
    {
        if (($userId == 0 || is_null($userId)) && auth()->check())
            $userId = auth()->user()->id;

        return $userId;
    }

    public static function UserSetting($name, $value = null, $userId = null)
    {
        $isOtherUser = !is_null($userId);
        if (auth()->check() || $isOtherUser) {
            if (!$isOtherUser)
                $userId = static::UserId();
            if (is_null($value)) {
                $value = users_settings::where([ 'setting' => $name, 'user_id' => $userId ])->first();
                if (!is_null($value))
                    $value = $value->value;

                return $value;
            }
            $setting = users_settings::firstOrNew([ 'setting' => $name, 'user_id' => $userId ]);
            $setting->setting = $name;
            $setting->value = $value;
            $setting->user_id = static::UserId();
            $setting->save();

            return $value;
        }
        else {
            return null;
        }
    }
}
