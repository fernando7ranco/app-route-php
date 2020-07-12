<?php

namespace App\Controller;

class Controller {

	public function index($data){

		var_dump($data);

		return '<h1>controller method index</h1>';
	}

}