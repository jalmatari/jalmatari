<?php

use Jalmatari\Funs\Funs;

Route::get("artisan/{name}", [ 'as' => 'artisan', 'uses' => 'Jalmatari\Http\Controllers\Core\MyBaseController@artisan' ]);


$middlewares = Funs::MainRoutes(true);

foreach ($middlewares as $middleware => $routes)
    Route::group([ "middleware" => [ 'web','Lang', $middleware ] ], function () use ($routes) {
        foreach ($routes as $route)
            Funs::AddRoute($route);
    });


Route::any('jalmatari/elfinder-connector', [
    "middleware" => [ 'web','Lang','AdminAuth' ],
    'uses'       => function () {
        include public_path('jalmatari/plugins/jalmatari/php/connector.php');
    }
]);

//Auth routes
Route::group([ 'namespace' => 'Jalmatari\Http\Controllers', "middleware" => [ 'web','Lang' ] ], function () {
    Auth::routes();
});
