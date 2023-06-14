<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../entidades/usuario.php";
require_once __DIR__ . "/../entidades/middleware.php";
use \Slim\Routing\RouteCollectorProxy;
$app = AppFactory::create();

/*$app->get("/", function (Request $request, Response $response, $args){
  $response->getBody()->write("Hello World");
  return $response;
});*/

$app->group('/usuarios', function (RouteCollectorProxy $grupo) {   

  $grupo->get('/', Usuario::class . ':traerTodos');
  $grupo->get('/{id}', Usuario::class . ':traerUno');
  $grupo->post('/', Usuario::class . ':agregarUno');
  $grupo->post('/modificar', Usuario::class . ':modificarUno');
  $grupo->post('/login', Usuario::class . '::login');
  $grupo->delete('/{id}', Usuario::class . ':borrarUno');
});

$app->group('/credenciales', function (RouteCollectorProxy $grupo) {

  //EN LA RAIZ DEL GRUPO
  $grupo->get('/', function (Request $request, Response $response, array $args): Response {

    $response->getBody()->write("API => -GET- ");
    return $response;
  });
  
  $grupo->post('/', function (Request $request, Response $response, array $args): Response {

    $response->getBody()->write("API => -POST- ");
    return $response;
  });

})->add(MiddleWare::class . "::Verificar");

$app->group('/json', function (RouteCollectorProxy $grupo) {

  $grupo->get('/', function (Request $request, Response $response, array $args): Response {

    $datos = new stdclass();

    $datos->mensaje = "API => GET";

    $newResponse = $response->withStatus(200);
  
    $newResponse->getBody()->write(json_encode($datos));

    return $newResponse->withHeader('Content-Type', 'application/json');

  });

  $grupo->post('/', function (Request $request, Response $response, array $args): Response {

    $datos = new stdclass();

    $datos->mensaje = "API => POST";

    $newResponse = $response->withStatus(200);
  
    $newResponse->getBody()->write(json_encode($datos));

    return $newResponse->withHeader('Content-Type', 'application/json');

  });

})->add(MiddleWare::class . "::VerificarCredencialesJSON");

$app->group('/json_bd', function (RouteCollectorProxy $grupo) {

  $grupo->get('/', Usuario::class . ':TraerTodos'); 

  $grupo->post('/', Usuario::class . ':TraerTodos')->add(MiddleWare::class . "::VerificarUsuario");  
})->add(MiddleWare::class . "::VerificarInputs");

//CORRE LA APLICACIÓN.
$app->run();