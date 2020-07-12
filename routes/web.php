<?php

use App\Router;

echo '<pre>';

$router = new Router();

$router->group('gruponome', function($r){

    $r->get('teste/{id}/{name}', 'Controller@index')->name('teste1');

    $r->get('teste2/{id2}/{name2}', 'Controller@index')->name('teste2');

});

$router->get('teste3/{id2}/{name2}', 'Controller@index')->name('teste3');

$router->delete('teste3/{id2}/{name2}', 'Controller@index')->name('teste4');

echo $router($router->method(), $router->uri());

echo $router;


