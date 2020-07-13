<?php

use App\Router;

echo '<pre>';

$router = new Router();

$router->cache(function($router){

	for($i = 0; $i < 600; $i++){

		$router->group('gruponome'.$i, function($r){

		    $r->get('teste/{id}:(\d+)/{name}:([a-z]+)', 'Controller@index')->name('teste1');

		    $r->get('teste2/{id2}/{name2}', 'Controller@index')->name('teste2');

		   
		});
	}
	
});

echo $router($router->method(), $router->uri());


