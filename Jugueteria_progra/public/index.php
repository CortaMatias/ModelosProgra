<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';
require "../src/poo/middleware.php";
require "../src/poo/usuarios.php";
require "../src/poo/juguetes.php";

//NECESARIO PARA GENERAR EL JWT
use Firebase\JWT\JWT;


$app = AppFactory::create();

//************************************************************************************************************//
//TEST
/*
$app->get('/', function (Request $request, Response $response, array $args) : Response {  

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
});
*/
//************************************************************************************************************//

//Usuarios
$app->get("/", Usuarios::class . ":Listar");

$app->post("/login", Usuarios::class . ":Login")
->add(MW::class . ":VerificarParametrosBD")
->add(MW::class . "::SetParamLogin");


$app->get("/login", Usuarios::class . ":VerificarToken");

//Juguetes
$app->post("/", Juguetes::class . ":Agregar")
->add(MW::class . ":VerificarToken");

$app->get("/juguetes", Juguetes::class . ":Listar");

$app->group('/toys', function (RouteCollectorProxy $grupo) {

  $grupo->delete("/{id_juguete}",Juguetes::class . ":Eliminar");
  $grupo->post("[/]", Juguetes::class . ":Modificar");


})->add(MW::class . ":VerificarToken");


$app->group('/tablas', function (RouteCollectorProxy $grupo) {

  $grupo->get("/usuarios",Usuarios::class . ":Listar")
  ->add(MW::class . ":UsuarioHTML");

  $grupo->get("/juguetes",Juguetes::class . ":Listar")
  ->add(MW::class . ":JuguetesHTML");

  $grupo->post("/usuarios", Usuarios::class . ":Listar")
  ->add(MW::class . ":UsuarioPropietario");

})->add(MW::class . ":VerificarToken");


$app->post("/usuarios", Usuarios::class . ":Agregar")
->add(MW::class . ":CorreoDuplicado")
->add(MW::class . "::SetParamLogin")
->add(MW::class . ":VerificarToken");


//CORRE LA APLICACIÓN.
$app->run();