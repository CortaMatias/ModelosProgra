<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';
require "../src/entidades/usuarios.php";
require "../src/entidades/autos.php";
require "../src/entidades/middleware.php";

//NECESARIO PARA GENERAR EL JWT
use Firebase\JWT\JWT;


$app = AppFactory::create();

//************************************************************************************************************//

/*$app->get('/', function (Request $request, Response $response, array $args) : Response {  

  $datos = new stdclass();

  $datos->mensaje = "API => GET";

  $newResponse = $response->withStatus(200);
  $newResponse->getBody()->write(json_encode($datos));

  return $newResponse->withHeader('Content-Type', 'application/json');
});

$app->post('/', function (Request $request, Response $response, array $args) : Response { 

  $datos = new stdclass();

  $datos->mensaje = "API => POST";

  $newResponse = $response->withStatus(200);
  $newResponse->getBody()->write(json_encode($datos));
  
  return $newResponse->withHeader('Content-Type', 'application/json');
});

$app->put('/', function (Request $request, Response $response, array $args) : Response {  

  $datos = new stdclass();

  $datos->mensaje = "API => PUT";

  $newResponse = $response->withStatus(200);
  $newResponse->getBody()->write(json_encode($datos));
  
  return $newResponse->withHeader('Content-Type', 'application/json');
});

$app->delete('/', function (Request $request, Response $response, array $args) : Response {  

  $datos = new stdclass();

  $datos->mensaje = "API => DELETE";

  $newResponse = $response->withStatus(200);
  $newResponse->getBody()->write(json_encode($datos));
  
  return $newResponse->withHeader('Content-Type', 'application/json');
});*/

//************************************************************************************************************//

$app->post("/usuarios" , Usuarios::class . ":Agregar")
->add(MW::class . ":CorreoDuplicado")
->add(MW::class . ":EmptyParamLogin")
->add(MW::class . ":SetParamLogin");

$app->get("/", Usuarios::class . ":Listar")
->add(MW::class . ":ListarUsuarioEncargado")
->add(MW::class . ":ListarUsuarioEmpleado")
->add(MW::class . ":ListarUsuarioPropietario");

$app->post("/", Autos::class . ":Agregar")
->add(MW::class . ":AutoValidate");

//$app->get("/autos", Autos::class . ":Listar")->add(MW::class . ":MiddlewareListar");

$app->group('/autos', function (RouteCollectorProxy $grupo) {
  // Ruta sin parámetro
  $grupo->get('', Autos::class . ":Listar")->add(MW::class . ":MiddlewareListar");
  // Ruta con parámetro
  $grupo->get('/', Autos::class . ":Listar")
  ->add(MW::class . ":MiddlewareListarID");
});


$app->post("/login", Usuarios::class . ":Login")
->add(MW::class . ":VerificarParametrosBD")
->add(MW::class . ":EmptyParamLogin")
->add(MW::class . ":SetParamLogin");

$app->get("/login", Usuarios::class . ":VerificarToken");

$app->delete("/{id_auto}",Autos::class . ":Eliminar")->add(MW::class . ":ValidarPropietario")->add(MW::class . ":ValidarJWT");

$app->post("/modificar",Autos::class . ":Modificar")->add(MW::class . ":ValidarEncargado")->add(MW::class . ":ValidarJWT");






//CORRE LA APLICACIÓN.
$app->run();