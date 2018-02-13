<?php

define('__APP_ROOT__', dirname(__DIR__));

require __APP_ROOT__ . '/vendor/autoload.php';

use App\Router;

$router = new Router();

$router
    ->on('GET', 'home','\Controller@index')
	
    ->post('/(\d+)/(\d+)/(\d+)', function ($param1, $param2, $param3) {
        var_dump([$param1, $param2, $param3]);
    })
    ->get('/view/(\w+)', function ($view) {
		return view($view);
    })
    ->get('/(.*)', function ($uri) {
        var_dump($uri);
    });

echo $router($router->method(), $router->uri());
