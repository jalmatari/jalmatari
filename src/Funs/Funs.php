<?php

/*
 * jalmatari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Funs;

use App\Jalmatari\Models\book;
use Auth;
use DB;
use File;
use Form;
use Redirect;
use Route;
use Schema;

class Funs
{
    use TablesSourceFuns, HelperFuns, UsersAndGroups, AutoController;

    public static function OnLoadSite()
    {
        //book::ReorderPagesByIndexOrderNumber();

        // This Method Called from Jalmatari\Http\Middleware\AdminAuth::handle
        /*$time = (int) static::Setting('maximum_execution_time');
        if ($time > 30)
            set_time_limit($time);*/

    }

    public static function MenuBadge($num, $color = 'red', $moreZero = true, $class = '')
    {
        $html = '';
        if (!$moreZero || $num > 0)
            $html = '<small class="label pull-right bg-' . $color . ' ' . $class . '">' . $num . '</small>';

        return $html;
    }

    public static function MenuFirstBadge($num, $color = 'red', $moreZero = true)
    {
        return static::MenuBadge($num, $color, $moreZero, 'first-menu-lbl');
    }


}
