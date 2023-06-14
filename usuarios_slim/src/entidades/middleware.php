<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once __DIR__ . "./usuario.php";

class MiddleWare
{

  public static function VerificarCredenciales(Request $request, RequestHandler $handler): ResponseMW
  {
    $responseMW = new ResponseMW();

    $params = $request->getParsedBody();

    $after = "Vuelvo del verificar credenciales";

    $response = $handler->handle($request);
    $responseAPI = (string) $response->getBody();

    if ($request->getMethod() === "GET") {
      $before = "No necesita credenciales para GET";
      $responseMW->getBody()->write("$before <br> $responseAPI <br> $after");
    } else if ($request->getMethod() === "POST") {
      $before = "Verifico credenciales: ";
      if ($params["perfil"] === "administrador") {
        $before .= "Bienvenido " . $params["nombre"];
        $responseMW->getBody()->write("$before <br> $responseAPI <br> $after");
      } else {
        $before .= "No tienes habilitado el ingreso";
        $responseMW->getBody()->write("$before <br> $after");
      }
    }
    return $responseMW;
  }

  public static function VerificarCredencialesJSON(Request $request, RequestHandler $handler): ResponseMW
  {
    $responseMW = new ResponseMW();
    $response = $handler->handle($request);
    $responseAPI = $response->getBody();

    $params = $request->getParsedBody();

    if ($request->getMethod() === "GET") {
      $responseMW = $responseMW->withBody($responseAPI);
    } else if ($request->getMethod() === "POST") {
      $data = json_decode($params["obj_json"]);
      if ($data->perfil === "administrador")
        $responseMW = $responseMW->withBody($responseAPI);
      else {
        $responseMW = $responseMW->withHeader('Content-Type', 'application/json');
        $responseMW->getBody()->write(json_encode(array("mensaje" => "ERORR. " . $data->nombre . " sin permisos")));
      }
    }
    return $responseMW;
  }

  public static function VerificarUsuario(Request $request, RequestHandler $handler): ResponseMW
  {
    $responseMW = new ResponseMW();
    $data = $request->getParsedBody();
    $existe = Usuario::TraerUnUsuarioLogin($data['usuario']);

    if ($existe) {
      $response = $handler->handle($request);
      $responseAPI = $response->getBody();
      $responseMW = $responseMW->withBody($responseAPI);
    } else {
      $responseMW = $responseMW->withHeader('Content-Type', 'application/json');
      $responseMW->getBody()->write(json_encode(array("mensaje" => "ERORR. Correo o clave incorrecta")));
    }
    return $responseMW;
  }

  public static function VerificarInputs(Request $request, RequestHandler $handler): ResponseMW
  {
    $responseMW = new ResponseMW();
    $responseMW = $responseMW->withStatus(403, "ERROR");
    $todoOk = false;
    $respuesta = new stdClass();
    $respuesta->mensaje = "";

    if ($request->getMethod() === "GET") {
      $responseMW = $handler->handle($request);  
      $responseMW = $responseMW->withStatus(200, "OK");
      $todoOk =  true;
    } else {
      $data = $request->getParsedBody();
      if (isset($data["usuario"])) {
        $data_json = $data["usuario"];
        $data_obj = json_decode($data_json);
        if (!isset($data_obj->correo)) $respuesta->mensaje = "Falta el atributo correo";
        if (!isset($data_obj->clave)) $respuesta->mensaje = "Falta el atributo clave";

        if(isset($data_obj->correo)  &&  isset($data_obj->clave)){
          $responseMW = $handler->handle($request);
          $responseMW = $responseMW->withStatus(200, "OK");
          $todoOk = true;
        }        
      }else $respuesta->mensaje = "Falta parametro usuario";
    }
    if(!$todoOk) $responseMW->getBody()->write(json_encode($respuesta));

    return $responseMW;
  }
}
