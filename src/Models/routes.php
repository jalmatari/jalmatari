<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Models;

use Jalmatari\Funs\Funs;
use Jalmatari\JalmatariServiceProvider;

class routes extends myModel
{
    public static $defaultRoutes = [
        [ 'type' => 0, 'route' => '', 'action' => 'index', 'id_required' => false, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => '', 'action' => 'getData', 'id_required' => false, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => 'actionToMulti', 'action' => 'actionToMulti', 'id_required' => false, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => 'save', 'action' => 'save', 'id_required' => false, 'middleware' => 'AdminAuth' ],
        [ 'type' => 0, 'route' => 'add', 'action' => 'add', 'id_required' => false, 'middleware' => 'AdminAuth' ],
        [ 'type' => 0, 'route' => 'edit', 'action' => 'edit', 'id_required' => true, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => 'edit', 'action' => 'update', 'id_required' => true, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => 'delete', 'action' => 'delete', 'id_required' => true, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => 'publish', 'action' => 'publish', 'id_required' => true, 'middleware' => 'AdminAuth' ],
        [ 'type' => 1, 'route' => 'api', 'action' => 'api', 'id_required' => false, 'middleware' => 'AdminAuth' ],
    ];

    public function __construct($table = null)
    {
        parent::__construct($table);//you can add custom table name here
    }

    public function beforeSaving()
    {
        //if (is_null($this->table_name))
        //$this->table_name = '';
    }

    public static function ControllersList($controllerId = null)
    {

        $controllers = controllers::select('*')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        if (!is_null($controllerId) && $controllerId != 0 && isset($controllers[ $controllerId ]))
            $controllers = [ $controllerId => $controllers[ $controllerId ] ] + $controllers;

        return $controllers;
    }

    public static function ControllersActionsList($data = null)
    {
        $actions = [];
        $controllers = controllers::select('*')
            ->orderBy('name')
            ->get();
        $selected = [];
        foreach ($controllers as $controller) {
            $controllerName = $controller->name;
            if (strlen($controller->namespace) >= 1)
                $controllerName = $controller->namespace . '\\' . $controllerName;
            $methods = Funs::controllerMethods($controllerName);
            $methods = Funs::ArrayByValuesAsKeys($methods);

            $actions[ $controller->id ] = $methods;
        }

        if (!is_null($data) && $data !== 0)
            $actions = [ $data => $data ] + $actions;

        return $actions;
    }

    public static function RoutesTypesList($data = null)
    {
        if ($data == 0)
            $types = [ 'GET', 'POST' ];
        else
            $types = [ 1 => 'POST', 0 => 'GET' ];

        return $types;
    }

    public static function WithOrWithoutId($data = 0)
    {
        if ($data == 0)
            $types = [ 'بدون id', 'يتطلب id' ];
        else
            $types = [ 1 => 'يتطلب id', 0 => 'بدون id' ];

        return $types;
    }

    public static function MiddlewareList($data = null)
    {
        $middilewares = JalmatariServiceProvider::$middilewares;

        $middilewares = Funs::ArrayByValuesAsKeys($middilewares);
        if (!is_null($data) && $data !== 0 && isset($middilewares[ $data ]))
            $middilewares = [ $middilewares[ $data ] => $middilewares[ $data ] ] + $middilewares;

        return $middilewares;
    }

    public function controller()
    {
        return $this->belongsTo(__NAMESPACE__ . '\controllers', 'controller_id');
    }

    public function getControllerNameAttribute()
    {
        return $this->controller->name;
    }

    public function getNamespaceAttribute()
    {
        return $this->controller->namespace;
    }

    public function getControllerForRoutesAttribute()
    {
        return $this->controller;
    }


    public static function MainRoutes($splitSiteAdmin = false)
    {

        return cache_('mainRoutesCached',
            function () use ($splitSiteAdmin) {
                $routes = collect([]);

                if (substr(request()->path(), 0, 7) != 'artisan') {

                    //get default routes
                    $controllers = explode('/', request()->path());

                    $controllers = controllers::where('status', 1)->get();//status = itHasDefaultRoutes
                    $defualtRoutes = static::$defaultRoutes;
                    foreach ($controllers as $controller)
                        foreach ($defualtRoutes as $dRoute)
                            $routes->push(static::RouteRow($controller, $dRoute));


                    $route_ = static::where('status', 1)->get();
                    $route_->pluck('controller');
                    foreach ($route_ as $dRoute)
                        $routes->push(static::RouteRow($dRoute->controllerForRoutes, $dRoute->toArray()));

                }
                if ($splitSiteAdmin)
                    $routes = $routes->groupBy('middleware');

                return $routes;
            });
    }

    public static function RouteRow($controller, $dRoute)
    {


        $routeName = '.' . (strlen($dRoute['route']) >= 1 ? '.' . $dRoute['route'] : '');
        if ($dRoute['middleware'] == 'AdminAuth')//not use table in path and admin if it public
            $routeName = 'admin.' . $controller->tableName . $routeName;
        $routeName = trim($routeName, '.');
        $routeName = str_replace('..', '.', $routeName);
        $routeName = str_replace('..', '.', $routeName);
        $routeName = str_replace('admin.admin', 'admin', $routeName);

        $dRoute['route'] = $routeName;
        $dRoute['url'] = str_replace('.', '/', $routeName) . ($dRoute['id_required'] ? '/{id' . ($controller->tableName == 'settings' ? '?' : '') . '}' : '');

        $dRoute['controller_id'] = $controller->id;
        $dRoute['namespace'] = $controller->namespace . (strlen($controller->namespace) >= 0 ? '\\' : '');
        $dRoute['controller_name'] = $controller->name;
        $dRoute['status'] = 1;
        $dRoute['type'] = [ 'get', 'post' ][ $dRoute['type'] ];
        $dRoute['pars'] = [
            "as"   => $dRoute['route'],
            "uses" => $dRoute['namespace'] . $controller->name . "@" . $dRoute['action']
        ];

        return (object) $dRoute;
    }
}
