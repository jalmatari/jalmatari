<?php

use Jalmatari\Funs\Funs;

Route::get("artisan/{name}", [ 'as' => 'artisan', 'uses' => 'Jalmatari\Http\Controllers\Core\MyBaseController@artisan' ]);


$middlewares = Funs::MainRoutes(true);

foreach ($middlewares as $middleware => $routes)
    Route::group([ "middleware" => [ 'web', 'Lang', $middleware ] ], function () use ($routes) {
        foreach ($routes as $route)
            Funs::AddRoute($route);
    });


Route::group([ 'prefix' => 'jalmatari/', 'as' => 'jalmatari.', "middleware" => [ 'web', 'Lang', 'auth', 'AdminAuth' ] ], function () {
    $nameSpace = 'Jalmatari\Http\Controllers\Admin\AdminController@';
    Route::any('elfinder/connector', [ 'as' => 'elfinder.connector', 'uses' => $nameSpace . 'elfinderConnector' ]);
    Route::any('elfinder/ckeditor', [ 'as' => 'elfinder.ckeditor', 'uses' => $nameSpace . 'elfinderCkeditor' ]);

    Route::get('documentation', [ 'as' => 'documentation', 'uses' => $nameSpace . 'documentation' ]);

    Route::get('publish/config', [ 'as' => 'publish.config', 'uses' => $nameSpace . 'publishConfig' ]);
});

//Auth routes
Route::group([ 'namespace' => 'Jalmatari\Http\Controllers', "middleware" => [ 'web', 'Lang' ] ], function () {
    Auth::routes();
    Route::any('api/git/upgrade', [ 'as' => 'api.git.upgrade', 'uses' => 'Core\APIController@gitUpgradeVersion' ]);
});
