<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';
require "../src/poo/middleware.php";
require "../src/poo/usuarios.php";
require "../src/poo/Medias.php";


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
});*/



//************************************************************************************************************//

//Usuarios
$app->get("/medias", Medias::class . ":Listar")
->add(MW::class . ":ListarEncargado")
->add(MW::class . ":ListarPropietario")
->add(MW::class . ":ListarEmpleado");

$app->post("[/]", Medias::class . ":Agregar");

$app->post("/usuarios", Usuarios::class . ":Agregar");

$app->post("/login", Usuarios::class . ":Login")
->add(MW::class . ":VerificarParametrosBD")
->add(MW::class . ":EmptyParamLogin")
->add(MW::class . ":SetParamLogin");

$app->get("/login", Usuarios::class . ":VerificarToken");

$app->delete("/{id_medias}", Medias::class . ":Eliminar")
->add(MW::class . ":ValidarPermisosDelete")
->add(MW::class . ":ValidarJWT");

$app->put('/', Medias::class . ':Modificar')
->add(MW::class . ":ValidarPermisosPut")
->add(MW::class . ":ValidarJWT");



//CORRE LA APLICACIÓN.
$app->run();