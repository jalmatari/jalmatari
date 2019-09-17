<?php

/*
 * Jamal Al-Matari 2019.
 * jalmatari@gmail.com
 */

namespace Jalmatari\Http\Controllers\Admin;

use Jalmatari\Funs\Funs;
use Jalmatari\Http\Controllers\Core\MyBaseController;
use Jalmatari\Models\controllers;

class RoutesController extends MyBaseController
{

    public function __construct()
    {
        $this->init();
        $this->customCols = [ 'others' ];
    }

    public function getData($data = [])
    {

        $data = [
            'route'  => [
                'route',
                'formatter' => function ($field, $row) {

                    $data = $this->routeDetiles(
                        $row['type'],
                        $row['middleware'],
                        $row['route'],
                        $row['id_required'],
                        $row['controller_id'],
                        $row['action']
                    );

                    return "<div dir=ltr class=text-left>" .
                        Funs::IsIn([
                            "<span class=\"text-red text-bold\">GET</span>",
                            "<span class=\"text-green text-bold\">POST</span>"
                        ],
                            $row['type'], "NONE!")
                        . " {$data->routeName}<br><small>" . url($data->url) . "</small></div>";
                }
            ],
            'action' => [
                'action',
                'formatter' => function ($field, $row) {
                    return controllers::find($row['controller_id'])->name . "@" . $field;
                }
            ],
            'others' => [
                'others',
                'formatter' => function ($field, $row) {
                    $cols = [
                        'PublicAuth' => '<i class="fa fa-globe" data-toggle="tooltip" title="عرض المسار للجميع"></i>',
                        'UserAuth'   => '<i class="fa fa-user-circle-o" data-toggle="tooltip" title="عرض المسار للمدراء"></i>',
                        'AdminAuth'  => '<i class="fa fa-user-secret" data-toggle="tooltip" title="مسار خاص بلوحة التحكم"></i>'
                    ];

                    return '<div><span class="text-' . ($row['id_required'] ? 'green' : 'red') . '">' . '&nbsp;<i class="fa fa-asterisk" data-toggle="tooltip" title="يتطلب متغير كـ id"></i> </span>'
                        . '<span class="text-green">' . '&nbsp;' . Funs::IsIn($cols, $row['middleware']) . ' </span></div>';
                }
            ],
        ];

        $_POST['columns'][4]['searchable'] = false;

        return parent::getData($data); // TODO: Change the autogenerated stub
    }


    public function expandedTr()
    {
        dd(request()->all());
    }

    public function getRoute()
    {
        $data = $this->routeDetiles(request('type'),
            request('middleware'),
            request('route'),
            request('id_required'),
            request('controller_id'),
            request('action')
        );

        return '<div class="pull-left route">'
            . 'Route::' . $data->type . '("' . $data->url . $data->withIdUrl . '",[ "middleware" => "' . $data->middleware . '",as"=>"' . $data->routeName . '","uses"=>"' . $data->controllerName . '@' . $data->action . '"])' . $data->withIdFun
            . '</div><br><div class="pull-left url">' . url($data->url) . $data->withIdUrl . $data->namespace . '</div>';

    }

    private function routeDetiles($type, $middleware, $route, $idRequired, $controllerId, $action)
    {
        $type = $type ? 'post' : 'get';
        $middleware = $middleware;
        $isAdmin = $middleware == 'AdminAuth';
        $routeName = str_replace([ '\\', '/' ], '.', $route);
        $idRequired = $idRequired == 1;
        //Controller
        $namespace = '';
        $controller = controllers::find($controllerId);

        $controllerName = $controller->name;
        if (strlen($controller->namespace) >= 1)
            $namespace = '<br><br><sub class="text-green">*Note: Controller uses namespace (' . $controller->namespace . '\\' . $controllerName . ').</sub>';

        //Url
        $isContotollerHasPrefix = strlen($controller->url_prefix) >= 1;
        if ($isAdmin)
            $routeName = 'admin.' . ($isContotollerHasPrefix ? $controller->url_prefix . '.' : '') . $routeName;
        $url = str_replace('.', '/', $routeName);
        $withIdUrl = $withIdFun = '';

        if ($idRequired) {
            $withIdUrl = '/{id}';
            $withIdFun = "->where('id', '\\d+')";
        }
        $data = (object) [
            'type'           => $type,
            'url'            => $url,
            'middleware'     => $middleware,
            'routeName'      => $routeName,
            'controllerName' => $controllerName,
            'action'         => $action,
            'namespace'      => $namespace,
            'idRequired'     => $idRequired,
            'withIdUrl'      => $withIdUrl,
            'withIdFun'      => $withIdFun,
        ];

        return $data;

    }

    public function jamal()
    {
        dd(__CLASS__ . ' - Jamal');
    }
}