<?php

if (!function_exists('route_')) {
    function route_()
    {

        $args = func_get_args();
        if (Route::has($args[0]))
            return call_user_func_array('route', $args);

        return 'not-found';
    }
}

//to Generate auto class="active"
if (!function_exists('is_active_menu')) {
    function is_active_menu($route, $withClassWord = true)
    {
        $returnStr = '';
        $curRoute = request()->route()->getName();
        if (is_array($route)) {
            foreach ($route as $subRoute) {
                $isCurrent = strpos($curRoute, $subRoute) === 0;
                if ($isCurrent)
                    break;
            }
        }
        else
            $isCurrent = strpos($curRoute, $route) === 0;
        if ($isCurrent && !$withClassWord)
            $returnStr = ' active';
        else if ($isCurrent && $withClassWord)
            $returnStr = ' class="active" ';

        return $returnStr;
    }
}

//to get month in secunds
if (!function_exists('month_seconds')) {
    function month_seconds()
    {
        return 60 * 60 * 24 * 30;
    }
}

//to get cache
if (!function_exists('cache_')) {
    function cache_($varName, $default, $expire = null)
    {
        return cache()->remember($varName, $expire ?? month_seconds(), $default);
    }
}


//to get view cached
if (!function_exists('viewCache_')) {
    function viewCache_($cachName, $viewName)
    {
        return cache_($cachName . 'ViewForUserId' . auth()->id(), function () use ($viewName) {
            return view($viewName)->render();
        });
    }
}

//to get/set settings
if (!function_exists('setting')) {
    function setting($settingName, $val = null)
    {
        return \Jalmatari\Funs\Funs::Setting($settingName, $val);
    }
}