<?php

namespace Jalmatari\Funs;

use Auth;
use DB;
use Jalmatari\Models\menu;
use Jalmatari\Models\permissions;
use Jalmatari\Models\settings;
use Jalmatari\Models\sync;
use Jalmatari\Models\tables;
use Route;

// All This Class Need Enhanced
trait Database
{

    public static $curMenu = null;
    public static $curParentMenus = null;
    public static $curRoute = null;
    public static $curMenus = null;

    /* Get Database table Prefix
     *
     */
    public static function DB_Prefix()
    {
        return DB::getTablePrefix();
    }

    public static function DB_Name()
    {
        return DB::getDatabaseName();
    }

    public static function Setting($name, $value = null)
    {
        if (is_null($value)) {
            $value = cache_('settings', function () { return settings::all(); })
                ->where('name', $name)
                ->first();

            if (!is_null($value))
                $value = $value->value;

            return $value;
        }


        return static::setSettings($name, $value);
    }


    public static function Settings($names, $insert = false)
    {
        if (!$insert)
            return settings::whereIn('name', $names)->get();

        foreach ($names as $name => $value)
            static::setSettings($name, $value);

    }

    public static function FirstSetting($setting)
    {
        $firstSetting = static::JsonSetting($setting);
        reset($firstSetting);
        $firstSetting = key($firstSetting);

        return $firstSetting;
    }

    public static function BoolSetting($setting)
    {
        return (static::Setting($setting) == 1);
    }

    public static function SettingAsArr($setting)
    {
        return static::ArrSetting($setting);
    }

    public static function JsonSetting($setting)
    {
        $setting = static::Setting($setting);
        $setting = json_decode($setting);

        return $setting;
    }

    public static function SettingJson($setting)
    {
        return static::JsonSetting($setting);
    }

    public static function ArrSetting($setting)
    {
        return (array) static::JsonSetting($setting);
    }

    public static function setSettings($name, $value)
    {
        $setting = settings::firstOrNew([ 'name' => $name ]);
        $setting->name = $name;
        $setting->value = $value;
        $setting->save();

        cache()->clear();
        return $setting;
    }


    public static function AdminMenus($parent = 0)
    {
        return cache_("AdminMenusCachedForUser_" . auth()->id() . "Parent_{$parent}", function () use ($parent) {
            $menus = [];
            if ($parent >= 0) {
                $kinds = [ 1 => 'الموقع', 2 => 'الإدارة' ];
                $menus = menu::where('parent', $parent);
                if (!static::IsAdmini())
                    $menus = $menus->where('status', 1);
                $menus = $menus->orderBy('ord')->get();
            }

            return $menus;
        });
    }

    public static function parentId($name)
    {
        return menu::where('name', '=', $name)->first()->parent;
    }

    public static function MenuParents($parent_id)
    {
        $parents = collect();
        if ($parent_id > 0) {
            $parents = menu::where('id', $parent_id)->get();
            $parent = $parents->first()->parent;
            if ($parent > 0)
                $parents = $parents->merge(static::MenuParents($parent));
        }

        return $parents;
    }

    public static function SubMenusOfMenu($menu_id)
    {
        $subMenus = collect();
        if ($menu_id > 0) {
            $subMenus = menu::where('parent', $menu_id)->get();
            $temSubMenus = $subMenus;
            foreach ($temSubMenus as $subMenu) {
                $temSubMenu = static::SubMenusOfMenu($subMenu->id);
                if ($temSubMenu->count() >= 1)
                    $subMenus = $subMenus->merge($temSubMenu);
            }
        }

        return $subMenus;
    }

    public static function InitCurrentRouteMenu()
    {
        //TODO Need enhanced
        if (is_null(static::$curRoute))
            static::$curRoute = Route::currentRouteName();
        if (is_null(static::$curMenu)) {
            static::$curMenus = collect();
            $curRoute = static::$curRoute;
            static::$curMenu = menu::where('link', $curRoute)->first();
            $curMenu = static::$curMenu;
            if (isset($curMenu->id)) {
                static::$curMenus = static::$curMenus->merge(static::MenuParents($curMenu->parent));//parent menues
                static::$curMenus = static::$curMenus->merge(static::SubMenusOfMenu($curMenu->id));//submenus
                static::$curMenus->push($curMenu);
            }
            if (is_null(static::$curMenu))
                static::$curMenu = collect();
        }
    }

    public static function isCurrentMenu($route)
    {
        static::InitCurrentRouteMenu();

        $curRoute = static::$curRoute;
        $isCurr = false;


        if ($route == $curRoute || static::$curMenus->where('link', $route)->count() >= 1) {
            $isCurr = true;
        }
        else {
            $curRoute = explode('.', $curRoute);
            $temRoute = $curRoute[0];
            $routes = [];
            unset($curRoute[0]);
            foreach ($curRoute as $curRout) {
                $temRoute .= '.' . $curRout;
                if ($temRoute == $route) {
                    $isCurr = true;
                    break;
                }
                $routes[] = $temRoute;
            }
            if (!$isCurr) {
                $parents = static::ParentsIdsOfRoutes($routes);
                $parents = menu::whereIn('id', $parents)->where('link', $route);
                $isCurr = $parents->count() >= 1;

            }
        }


        return $isCurr;
    }

    public static function ParentsIdsOfRoutes($routes = [])
    {
        $parents = [];
        $temParents = menu::whereIn('link', $routes)->get()->pluck('parent');
        $temParents = menu::whereIn('id', $temParents)->get();
        foreach ($temParents as $parent) {
            $parents[] = $parent->id;
            if ($parent->parent > 0)
                $parents = array_merge($parents, static::ParentsIdsOfRoutes([ $parent->link ]));
        }

        return $parents;
    }

    public static function ShowMenu($link)
    {
        $permissions = json_decode(permissions::where('status', '=', 1)->where('id', '=', Auth::user()->job_title)->first()->permissions);
        $permissions = array_merge($permissions, (array) json_decode(Auth::user()->permissions));
        $menu = menu::where('status', '=', 1)->where('link', '=', $link);
        if ($menu->count() >= 1) {
            $menu = $menu->first()->name;
        }
        $showMenu = in_array($menu, $permissions) || static::IsAdmini();

        return $showMenu;

    }


    public static function PrintMenu(menu $menu, $kind = 2)
    {
        $menuHtml = '';
        if (static::ShowMenu($menu->link)) {
            $subMenu = static::AdminMenus($menu->id);
            $isThereSubMenus = $subMenu->count() >= 1;
            $isActive = static::isCurrentMenu($menu->link);
            $isActive = $isActive ? ' active' : '';
            $menuHtml .= '<li class="' . $menu->class . ($isThereSubMenus ? " treeview" : "") . $isActive . '">'
                . '<a href="' . ($isThereSubMenus ? '#' : trim(route_($menu->link, null), '?')) . '" title="">'
                . '<i class="fa ' . $menu->icon . '"></i><span>' . __($menu->title) . '</span>';

            $menuHtml .= ($isThereSubMenus ? '<i class="fa fa-angle-left pull-right"></i>' : '') . '</a>';
            if ($isThereSubMenus) {
                $menuHtml .= '<ul class="treeview-menu">';
                foreach ($subMenu as $menu2)
                    $menuHtml .= static::PrintMenu($menu2);
                $menuHtml .= '</ul>';
            }
            $menuHtml .= '</li>';
        }


        return $menuHtml;
    }


    public static function ActionToMultiRoute()
    {
        $route = Route::currentRouteName();
        $route = explode('.', $route);
        if (count($route) >= 3)
            $route = array_slice($route, 0, 2);
        $route = implode('.', $route);
        $route = route_($route . '.actionToMulti');

        return $route;
    }

    /**
     * To keep synchronization
     * @param $table
     * @param $id
     * @param int $type [0=Deleted  1=Updated  2=published 3=unPublished]
     * @param int $userId
     */
    public static function Sync($table, $id, $type = 1, $userId = 0)
    {
        if ($userId == 0)
            $userId = static::UserId();
        sync::insert([
            'table'   => $table,
            'row_id'  => $id,
            'action'  => $type,
            'user_id' => $userId
        ]);
    }

    public static function DeleteSyncMulti($table, $where)
    {
        $model = 'Jalmatari\Models\\' . $table;

        $deleted = $model::where($where);
        static::SyncMulti($table, $deleted->get([ 'id' ])->pluck('id'), 0);
        $deleted->delete();
    }

    public static function SyncMulti($table, $ids, $type = 1)//$type Updated Or Deleted
    {
        $row = [ 'table' => $table, 'action' => $type, 'user_id' => static::UserId() ];
        $rows = [];

        foreach ($ids as $id) {
            $row['row_id'] = $id;
            $rows[] = $row;
        }
        sync::insert($rows);
    }

    public static function PrintDatabaseData()
    {
        $json = [];
        $tables = tables::all();
        $names = [];
        echo "<pre>";
        foreach ($tables as $table) {
            $tableName = $table->name;
            $cols = $table->cols->whereNotIn('name', [ 'id', 'created_at', 'updated_at' ])->pluck('name');

            $table = call_user_func([ get_class($table->model), 'select' ])
                ->select($cols->toArray())
                ->get();
            if ($table->count()) {
                $table = json_encode($table->toArray(), JSON_UNESCAPED_UNICODE);
                $table = str_replace([ '{', '}', '":', "'" ], [ '[', ']', '"=>', "\'" ], $table);
                $table = str_replace('"', "'", $table);
                $tableName = '$' . $tableName;
                echo $tableName . '=' . $table . ";\n";
                $names[] = $tableName;
            }
        }
        echo "</pre>";
        echo 'dd(' . implode(',', $names) . ');';
        die();
    }

}
