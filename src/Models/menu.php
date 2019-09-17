<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

use DB;
use Jalmatari\Funs\Funs;

class menu extends myModel
{

    public $timestamps = false;

    public function subMenus()
    {
        return $this->hasMany('Jalmatari\Models\menu', 'parent');
    }

    public static function RoutesNamesList($data = null)
    {
        $routes = routes::MainRoutes()->flatten();

        $routes = $routes->sortBy('route')
            ->pluck('route', 'route');
        if (!is_null($data)&&$data!==0)
            $routes->prepend($data, $data);

        return $routes;
    }

    public static function SubMenusList($parent_id = 0, $subPrefix = '|----- ', $subPrefixType = '|----- ')
    {
        $menus = collect();
        $parents = menu::where('parent', $parent_id)->orderBy('ord')->get()->toArray();
        foreach ($parents as $parent) {
            $parent = (object) $parent;
            $menus->push($parent);
            $subMenues = static::SubMenusList($parent->id, $subPrefix . $subPrefixType, $subPrefixType);
            $subMenues = $subMenues->map(function ($subMenu) use ($subPrefixType) {
                $subMenu = $subMenu;
                $subMenu->title = str_replace('|----- |-----', '|&nbsp;&nbsp;&nbsp;&nbsp; |-----', $subPrefixType . $subMenu->title);

                return $subMenu;

            });
            $menus = $menus->merge($subMenues);
        }

        return $menus;
    }

    public static function GetMenuParents($parent_id = 0)
    {
        $selectedMenu = [];
        $menus = static::SubMenusList(0)->groupBy('kind')->toArray();
        $menus = array_map(function ($menu) {
            return array_pluck($menu, 'title', 'id');
        }, $menus);
        foreach ($menus as $kind)
            foreach ($kind as $id => $title)
                if ($id == $parent_id)
                    $selectedMenu = [ $id => $title ];
        $main_menu = [ '0' => 'قائمة رئيسية' ];
        $menus += $main_menu;
        if (count($selectedMenu) == 0)
            $selectedMenu = $main_menu;
        $menus = $selectedMenu + $menus;


        return $menus;
    }

}
