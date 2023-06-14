<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();


//*********************************************************************************************//
//01.- CONFIGURO LOS VERBOS GET, POST, PUT Y DELETE
//*********************************************************************************************//
//firma correcta para el standard -> (request, response, array) => response

$app->get('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("GET => Bienvenido!!! a SlimFramework 4");
    return $response;
});

$app->post('/', function (Request $request, Response $response, array $args) : Response { 

    $response->getBody()->write("POST => Bienvenido!!! a SlimFramework 4");
    return $response;
});

$app->put('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("PUT => Bienvenido!!! a SlimFramework 4");
    return $response;
});

$app->delete('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("DELETE => Bienvenido!!! a SlimFramework 4");
    return $response;
});

//*********************************************************************************************//
//02.- RUTEOS
//*********************************************************************************************//

$app->get('/ruteo/', function (Request $request, Response $response, array $args) : Response {    

    $response->getBody()->write("Ruteo, sin params");
    return $response;
});

$app->get('/ruteo/{param}', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write("Ruteo, con params -> " . $args["param"]);
    return $response;
});

$app->get('/ruteoOpcional[/]', function (Request $request, Response $response, array $args) : Response { 

    $response->getBody()->write("Ruteo, sin params y / opcional");
    return $response;
});

$app->get('/ruteoOpcional/{param}/[{otro}]', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("Ruteo, con params opcional -> " . $args["param"]);
    return $response;
});

//*********************************************************************************************//
//03.- VARIOS PARAMETROS
//*********************************************************************************************//

$app->get('/ruteoOpcional/parametros/{param1}/{param2}', function (Request $request, Response $response, array $args) : Response { 

    $response->getBody()->write("Ruteo, con parametros -> " . $args["param1"]  . "-" . $args["param2"]);
    return $response;
});

//*********************************************************************************************//
//04.- ATENDER TODOS LOS VERBOS DE HTTP -> ANY
//*********************************************************************************************//

$app->any('/cualquiera/[{id}]', function (Request $request, Response $response, array $args) : Response {
        
    $id = isset($args['id']) ? $args['id'] : "---";

    $response->getBody()->write("Método: " . $request->getMethod());

    $response->getBody()->write(" Cualquier verbo de HTTP. Parametro: {$id}");

    return $response;
});

//*********************************************************************************************//
//04.- ATENDER ALGUNOS VERBOS DE HTTP -> MAP
//*********************************************************************************************//

$app->map(['GET', 'POST'], '/mapeado', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write("Método: " . $request->getMethod());

    $response->getBody()->write(" Sólo POST y GET");

    return $response;
});

//*********************************************************************************************//
//04.- AGRUPACION DE VERBOS POR RUTA -> GROUP
//*********************************************************************************************//

$app->group('/saludo', function(\Slim\Routing\RouteCollectorProxy $grupo) {

    $grupo->get('/', function (Request $request, Response $response, array $args) : Response {

        $response->getBody()->write("HOLA, bienvenido a la apirest...");

        return $response;
    });

    $grupo->get('/{nombre}', function (Request $request, Response $response, array $args) : Response {

        $nombre = $args['nombre'];

        $response->getBody()->write("HOLA, bienvenido <h1>{$nombre}</h1> a la apirest.");

        return $response;
    });
 
     $grupo->post('/', function (Request $request, Response $response, array $args) : Response { 

        $response->getBody()->write("HOLA, bienvenido a la apirest por POST");

        return $response;
    });
     
});

//*********************************************************************************************//
//05.- AGRUPACION DE RUTAS Y MAPEO
//*********************************************************************************************//

$app->group('/usuario/{id:[0-9]+}', function(\Slim\Routing\RouteCollectorProxy $grupo) {

    $grupo->map(['POST', 'DELETE'], '', function (Request $request, Response $response, array $args) : Response {

        $response->getBody()->write("id = " . $args["id"]);
        $response->getBody()->write(" Accedo al usuario por ID (con POST o DELETE) ");

        return $response;
    });

    $grupo->get('/nombres', function (Request $request, Response $response, array $args) : Response {

        $response->getBody()->write("id = " . $args["id"]);
        $response->getBody()->write("Accedo al usuario por ID y accedo a /nombres (con GET) ");

        return $response;
    });

});

//*********************************************************************************************//
//06.- PARAMETROS Y RETORNOS (JSON)
//*********************************************************************************************//

$app->get('/datos/', function (Request $request, Response $response, array $args) : Response {     
    
    //CREO RETORNO (ARRAY).
    $datos = array('nombre' => 'rogelio','apellido' => 'agua', 'edad' => 40);

    //INDICO CÓDIGO DE ESTADO Y MENSAJE ASOCIADO.
    $newResponse = $response->withStatus(200, "Éxito!!! JSON enviado.");
  
    //GENERO EL JSON A PARTIR DEL ARRAY.
    $newResponse->getBody()->write(json_encode($datos));

    //INDICO EL TIPO DE CONTENIDO QUE SE RETORNARÁ (EN EL HEADER).
    return $newResponse->withHeader('Content-Type', 'application/json');
});

$app->post('/datos/', function (Request $request, Response $response, array $args) : Response {   

    //OBTENGO LOS PARAMETROS (POST). 
    $arrayDeParametros = $request->getParsedBody();

    //CREO RETORNO (OBJETO)
    $objeto = new stdclass();
    $objeto->nombre = $arrayDeParametros['nombre'];
    $objeto->apellido = $arrayDeParametros['apellido'];
    $objeto->edad = $arrayDeParametros['edad'];
    
    //INDICO CÓDIGO DE ESTADO Y MENSAJE ASOCIADO.
    $newResponse = $response->withStatus(403, "No, no!!! JSON enviado.");

    //GENERO EL JSON A PARTIR DEL OBJETO.
    $newResponse->getBody()->write(json_encode($objeto));

    //INDICO EL TIPO DE CONTENIDO QUE SE RETORNARÁ (EN EL HEADER).
    return $newResponse->withHeader('Content-Type', 'application/json');
});

$app->post('/datos/edit', function (Request $request, Response $response, array $args) : Response { 

    //OBTENGO LOS PARAMETROS (POST).
    $arrayDeParametros = $request->getParsedBody();

    //'DECODEO' A OBJETO DE PHP
    $obj = json_decode(($arrayDeParametros["cadenaJson"]));

    var_dump($obj);

    return $response;
});

//*********************************************************************************************//
/*07.- POO Y SLIM 4*/
//*********************************************************************************************//

//*********************************************************************************************//
/*LLAMADA A METODOS DE INSTANCIA DE UNA CLASE*/
//*********************************************************************************************//

require_once __DIR__ . "/../Poo/cd.php";
use \Slim\Routing\RouteCollectorProxy;

$app->group('/cd', function (RouteCollectorProxy $grupo) {   

    $grupo->get('/', Cd::class . ':traerTodos');
    $grupo->get('/{id}', \Cd::class . ':traerUno');
    $grupo->post('/', \Cd::class . ':agregarUno');
    $grupo->put('/{cadenaJson}', \Cd::class . ':modificarUno');
    $grupo->delete('/{id}', \Cd::class . ':borrarUno');

});


//CORRE LA APLICACIÓN.
$app->run();