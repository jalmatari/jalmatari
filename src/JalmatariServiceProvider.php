<?php

namespace Jalmatari;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;


class JalmatariServiceProvider extends ServiceProvider
{

    public static $middilewares = [ 'PublicAuth', 'UserAuth', 'AdminAuth' ];

    public static function path($fileName='')
    {
        $fileName=__DIR__.($fileName!=''?'/'.$fileName:'');
        return $fileName;
    }
    /**
     * Bootstrap the application services.
     */
    // \Illuminate\Routing\Router $router
    public function boot()
    {
        if (request()->has('logger')) {
            $count = 1;
            \DB::listen(function ($query) use (&$count) {
                dump($query->sql, $count++);
            });
        }
        session([ 'startMicroTime' => microtime(true) ]);
        $this->firstInit();

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->publishes([ __DIR__ . '/public' => public_path('jalmatari') ], 'jalmatari-public');
        $this->publishes([ __DIR__ . '/config.php' => config_path('jalmatari.php') ], 'jalmatari-config');
        //$this->loadMigrationsFrom(__DIR__.'/database/migrations');

        //include ('database/insertDatabaseData.php');
    }

    public function firstInit()
    {
        $this->registerHelpers();
        $this->app->bind(
            ExceptionHandler::class,
            JalmatariHandler::class
        );

        //Register Views
        view()->addLocation(__DIR__ . '/views');
        view()->replaceNamespace('Funs', [ __DIR__ . '/views' ]);


        // Register our custom Middleware
        $router = $this->app['router'];
        $method = app()->version() >= 5.4 ? 'aliasMiddleware' : 'middleware';
        $middilewares = static::$middilewares;
        $middilewares[] = 'AfterMiddleware';
        $middilewares[] = 'Lang';
        foreach ($middilewares as $name)//Three Middleware
            $router->{$method}($name, "Jalmatari\Http\Middleware\\$name");
        $this->registerControllers(__DIR__ . '/Http/Controllers');
    }

    /**
     * Register the application services.
     */
    public function register()
    {

    }

    public function registerControllers($path, $folder = '')
    {
        $controllers = scandir($path);
        foreach ($controllers as $controller) {
            if (substr($controller, 0, 1) != '.' && $controller != 'Traits') {
                if (strpos($controller, '.php') > 1) {
                    $controller = str_replace('.php', '', $controller);
                    $controller = 'Jalmatari\Http\Controllers\\' . $folder . $controller;

                    $this->app->make($controller);
                }
                else
                    $this->registerControllers($path . '/' . $controller, $folder . $controller . '\\');
            }
        }
    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        if (file_exists($file = __DIR__ . '/helpers.php'))
            require $file;

    }
}
