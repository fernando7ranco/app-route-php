# app-route-php
simples controle de rotas dinâmicas com PHP, dando acesso as classes Controller do sistema

# Instalação autoload de classes e helpers 
comando: composer dump-autoload. e modrewrite ativo no servidor

## Documentação

###### Para começar a usar a voce precisa criar uma nova instância de rotas

As rotas mais básicas aceitam URI e [Closure](https://www.php.net/manual/pt_BR/class.closure.php), fornecendo um método muito simples e expressivo de definição de rotas:

```php
use App\Router;

$router = new Router();

$router->get('home', function(){
    return 'Hello World';
});

echo $router($router->method(), $router->uri());

```

###### Parâmetros da rota

Os parâmetros de rota sempre estão dentro de chaves e devem consistir em caracteres alfabéticos e não podem conter um caractere especial. Em vez de usar o caractere especial, use um sublinhado ( ). Os parâmetros de rota são injetados nos retornos / controladores de chamada de rota com base em sua ordem - os nomes dos argumentos de

```php
use App\Router;

$router = new Router();

$router->get('user/{id}', function ($data) {
    return 'User '.$data['id'];
});

$router->get('user/{id}/grup/{grup}', function ($data) {
   return 'User '.$data['id'] . ' group '.$data['group'];
});

echo $router($router->method(), $router->uri());

```

###### Restrições de expressão regular
Você pode restringir o formato dos seus parâmetros de rota usando a seguinte sintaxe {...}:(..), parâmetro e uma expressão regular que define como o parâmetro deve ser restringido
```php
use App\Router;

$router = new Router();

$router->get('user/{id}:(\d+)', function ($data) {
    return 'User '.$data['id'];
});

$router->get('user/{id}:(\d+)/grup/{grup}:([a-z])', function ($data) {
   return 'User '.$data['id'] . ' group '.$data['group'];
});

echo $router($router->method(), $router->uri());

```

###### Rotas para controller
Você pode acessar diretamento metodos de um controller com seguinte sintaxe nome da classe controle  @ metodo a ser acessado: ContollerClassName@methodAccess
```php
use App\Router;

$router = new Router();

$router->get('user/{id}:(\d+)', 'MyController@show');

echo $router($router->method(), $router->uri());

```

###### Grupos de rotas
Delimitadores de espaço para nome e barras nos prefixos de URI são adicionados automaticamente, quando apropriado.

```php
use App\Router;

$router = new Router();

$router->group('user', function($ro){

  $ro->get('show/{id}:(\d+)', 'MyController@show');
  $ro->post('create', 'MyController@create');
  $ro->put('edit/{id}:(\d+)', 'MyController@update');
  $ro->delete('delete/{id}:(\d+)', 'MyController@delete');
  
});

echo $router($router->method(), $router->uri());

```

###### Visualizar as rotas registradas
Você pode acessa diretamento metodos de um controller
```php
use App\Router;

$router = new Router();

$router->get('user/show/{id}:(\d+)', 'Controller@show');
$router->post('user/create', 'Controller@create');
$router->put('user/edit/{id}:(\d+)', 'Controller@update');
$router->delete('user/delete/{id}:(\d+)', 'Controller@delete');

// echo $router($router->method(), $router->uri());

echo $router;
```

###### Cache de rotas de controllers
metodo cache recebe dois paramentros cache(callable $callback, string $dir = '')
$callback Closure 
$dir caminho do arquivo que armazenara o cache das rotas, por padrão é usado o mesmo diretorio da classe Router
```php
use App\Router;

$router = new Router();

$router->cache(function($router){
	for($i = 0; $i < 600; $i++){
		$router->group('gruponome'.$i, function($r){
		    $r->get('teste/{id}:(\d+)/{name}:([a-z]+)', 'Controller@index');
		    $r->get('teste2/{id2}/{name2}', 'Controller@index');
		});
	}
});

echo $router($router->method(), $router->uri());

```


