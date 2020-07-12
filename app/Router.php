<?php

namespace App;


/**
 * Class Router
 * @method Router get($route, $callable)
 * @method Router post($route, $callable)
 * @method Router put($route, $callable)
 * @method Router delete($route, $callable)
 */
class Router
{
    /**
     * @var array
     */
    private $routes = [];

    private $lastRecordedRoute;

    private $group;

    /**
     * @return string
     */
    public function method()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';
    }

    /**
     * @return string
     */
    public function uri(): string
    {
        $self = isset($_SERVER['PHP_SELF']) ? str_replace('index.php/', '', $_SERVER['PHP_SELF']) : '';

        $uri = isset($_SERVER['REQUEST_URI']) ? explode('?', $_SERVER['REQUEST_URI'])[0] : '';

        if ($self !== $uri) {
            $peaces = explode('/', $self);
            array_pop($peaces);
            $start = implode('/', $peaces);
            $search = '/' . preg_quote($start, '/') . '/';
            $uri = preg_replace($search, '', $uri, 1);
        }

        return $uri;
    }

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @param $name string
     * @param $arguments array
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    function __call($name, $arguments)
    {
		//exemplo ->post('','');  ->get('',''); 

        $path = isset($arguments[0]) ? $arguments[0] : '' ;

        $callback = isset($arguments[1]) ? $arguments[1] : '';

        return $this->on($name, $path, $callback);
    }

    /**
     * @param $method
     * @param $path
     * @param $callback
     * @return $this
     */
    public function on($method, $path, $callback): Router
    {
        $method = strtolower($method);

        if (!isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }

        $route = substr($path, 0, 1) !== '/' ? '/' . $path : $path;

        if($this->group)
            $route = $this->group.$route;

        preg_match_all("~\{\s* ([a-zA-Z_][a-zA-Z0-9_-]*) \}~x", $route, $keys, PREG_SET_ORDER);

        $data = [];

        foreach ($keys as $key) {
            $data[$key[1]] = null;
        }

        $this->routes[$method][$route] = [
            'function' => $callback,
            'data' => $data,
            'name' => null
        ];

        $this->lastRecordedRoute = &$this->routes[$method][$route];

        return $this;
    }


    /**
     * @param $nameRouter
     * @return $this
     */

    public function name($nameRouter): Router
    {
        $this->lastRecordedRoute['name'] = $nameRouter;

        return $this;
    }

    /**
     * The __invoke method is called when a script tries to call an object as a function.
     *
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.invoke
     *
     * @param $method
     * @param $uri
     * @return mixed
     */
    function __invoke($method, $uri)
    {
        return $this->run($method, $uri);
    }

    /**
     * @param $method
     * @param $uri
     * @return mixed|null
     */
    public function run($method, $uri)
    {
        $method = strtolower($method);

        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $element) {

            $route = str_replace('/', '\/', $route);
            $route = '/^'. preg_replace('~{([^}]*)}~', "([^\/]+)", $route) . '$/';

            if (preg_match($route, $uri, $parameters)) {

                //array_shift($parameters);// remove primeiro lemento do array que seria o path
                $i = 1;
                foreach ($element['data'] as &$value) {
                    $value = $parameters[$i++];
                }

                $parameters[0] = $element['data'];

                return $this->call($element['function'], $parameters);
            }
        }
        return null;
    }

    /**
     * @param $callback
     * @param $parameters
     * @return mixed
     */
    public function call($callback, $parameters)
    {
        if (is_callable($callback)) 
            return call_user_func_array($callback, $parameters);
        
		// não é uma função vai verifica se pode ser uma classe de controller

        $array = explode('@',$callback);

        $classNamespace = __NAMESPACE__."\Controller\\" . $array[0];

        if(class_exists($classNamespace)){

            $newClass = new $classNamespace;

            $method = isset($array[1]) ? $array[1] : 'index';

            if(method_exists($newClass, $method))
                return call_user_func_array(array($newClass, $method), $parameters);

            return null;
        }
    }

    public function group($name,  $callback): Router
    {

        $this->group = substr($name, 0, 1) !== '/' ? '/' . $name : $name;

        if (is_callable($callback))
            call_user_func_array($callback, [$this]);

		$this->group = null;

        return $this;
    }

   	public function __toString(): string
   	{
   		$routes = '';

   		foreach ($this->routes as $method => $uris) {

   			foreach($uris as $uri => $value)
   				$routes.= $method. ' -> '. $uri . ' '. ($value['name'] ? 'name -> '. $value['name'] : null) . PHP_EOL;
   		}

   		return $routes;
   	}

}