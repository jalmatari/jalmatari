<?php

namespace Jalmatari\Funs;

use Auth;
use DB;
use Jalmatari\Models\menu;
use Jalmatari\Models\permissions;
use Jalmatari\Models\routes;
use Redirect;
use Route;
use Symfony\Component\HttpFoundation\Response;

trait MyAuth
{

    public static function IsAuth($user = null)
    {
        if (is_null($user))
            $user = auth()->user();
        $curRoute = Route::currentRouteName();

        if (auth()->check()) {
            return cache_("authUser{$user->id}ForRoute{$curRoute}", function () use ($user, $curRoute) {
                $permissions = permissions::where([ 'status' => 1, 'id' => $user->job_title ])
                    ->first()
                    ->permissions;
                $permissions = json_decode($permissions);
                $permissions = array_merge($permissions, (array) json_decode($user->permissions));

                $menu = menu::where('status', 1)->where('link', $curRoute);
                if ($menu->count() >= 1)
                    $menu = $menu->first()->name;

                $allowedRoutes = routes::select(DB::raw("concat('admin.',route) admin_route"))
                    ->whereIn('middleware', [ 'AdminAuth', 'UserAuth' ])
                    ->get()
                    ->pluck('admin_route')
                    ->toArray();

                $arrCurRoute = explode('.', $curRoute);
                $subRoute = '';
                if (count($arrCurRoute) >= 2) {
                    $subRoute = $arrCurRoute[1];
                }

                return (
                        in_array($menu, $permissions)
                        || in_array($subRoute, $permissions)
                        || in_array($subRoute, $allowedRoutes)
                        || in_array($curRoute, $allowedRoutes)
                    ) || static::IsAdmini();
            });
        }

        return redirect()->guest('admin/login');
    }

    public static function IsSpecialAuth($permission, $user = null)
    {
        if (is_null($user) && !auth()->check())
            return false;

        if (is_null($user))
            $user = Auth::user();
        $specialPremissions = permissions::where('id', $user->job_title)->first()->special_permissions;
        $specialPremissions = json_decode($specialPremissions);
        $specialPremissions = array_merge($specialPremissions, (array) json_decode($user->permissions));

        return in_array($permission, $specialPremissions);
    }

    public static function AddRoute($route)
    {
        $router = Route::{$route->type}($route->url, $route->pars);
        if ($route->id_required)
            $router->where("id", '\d+');
    }

    public static function MainRoutes($splitSiteAdmin = false)
    {
        return routes::MainRoutes($splitSiteAdmin);
    }

    public static function IsAdmini()
    {
        return env('IS_ADMINI', false);
    }

    public static function Abort($code, $message = '', $force = false, array $headers = [])
    {
        if ($force || substr(request()->path(), 0, 7) != 'artisan') {
            preg_match_all('!\d+!', $code, $matches);
            $statusCode = intval(implode('', $matches[0]));
            $statusTexts = Response::$statusTexts;

            if (!isset($statusTexts[ $statusCode ]))
                $statusCode = 501;
            http_response_code($statusCode);
            $title = $statusTexts[ $statusCode ];
            $message = str_replace('#home', '<a href="/">Go Home</a>', $message);
            $data = [
                'title'      => $title,
                'code'       => $code,
                'message'    => $message,
                'statusCode' => $statusCode
            ];

            die(view('Funs::errors.error', $data)->render());
        }

    }
}
