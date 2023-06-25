<?php
require_once("imiddleware.php");

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class MW implements IMiddleware
{

  public function ListarEmpleado(Request $request, RequestHandler $handler): ResponseMW
  {
      $token = $request->getHeader("token")[0];
      $dataUsuario = Autentificadora::obtenerPayLoad($token);
      $usuario = json_decode($dataUsuario->payload->data);
  
      if ($usuario->perfil == "empleado") {
          $response = $handler->handle($request);
          $retorno = json_decode((string)$response->getBody());  

          $listaMedias = $retorno->lista;
  
          $coloresDistintos = count(array_unique(array_column($listaMedias, 'color')));
  
          // Agregar la cantidad de colores distintos al mensaje de la respuesta
          $retorno->mensaje .= " Cantidad de colores distintos: " . $coloresDistintos;
  
          $status = 200;
          $response = new ResponseMW($status);
          $response->getBody()->write(json_encode($retorno));
          return $response;
      }
  
      return $handler->handle($request);
  }

  public function ListarPropietario(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    $parametros = $request->getQueryParams();
    $id = isset($parametros["id_medias"]) ? $parametros["id_medias"] : null;

    if ($usuario->perfil == "propietario") {
      $response = $handler->handle($request);
      $retorno = json_decode((string)$response->getBody());

      if ($retorno->exito) {
        if ($id !== null) {
          // Filtrar la lista por el ID especificado
          $retorno->lista = array_filter($retorno->lista, function ($medias) use ($id) {
            return $medias->ID == $id;
          });
        }
      }
      $status = 200;
      $response = new ResponseMW($status);
      $response->getBody()->write(json_encode($retorno));
      return $response;
    }

    return $handler->handle($request);
  }


  public function ListarEncargado(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    if ($usuario->perfil == "encargado") {
      $response = $handler->handle($request);
      $retorno = json_decode((string)$response->getBody());

      // Modificar la respuesta para eliminar el campo "ID"
      if ($retorno->exito) {
        foreach ($retorno->lista as &$medias) {
          unset($medias->ID);
        }
      }
      $status = 200;
      $response = new ResponseMW($status);
      $response->getBody()->write(json_encode($retorno));
      return $response;
    }

    return $handler->handle($request);
  }


  public function ValidarPermisosPut(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    if ($usuario->perfil == "encargado" || $usuario->perfil == "propietario") {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    } else {
      $retorno = json_encode(array("encargado" =>  false, "mensaje" => "Usted no tiene permisos, debe ser encargado o propietario"));
      $status = 409;
    }
    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }

  public function ValidarPermisosDelete(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    if ($usuario->perfil == "propietario") {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    } else {
      $retorno = json_encode(array("encargado" =>  false, "mensaje" => "Usted no tiene permisos, debe ser propietario"));
      $status = 409;
    }
    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }




  public function ValidarJWT(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $retorno = Autentificadora::verificarJWT($token);
    $status = $retorno->verificado ? 200 : 403;
    if ($retorno->verificado) {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    } else $retorno =  json_encode($retorno);
    $response = new ResponseMW($status);
    //Como viene de tipo STD CLASS lo parseo antes.
    $response->getBody()->write(($retorno));
    return $response;
  }

  public function VerificarParametrosBD(Request $request, RequestHandler $handler): ResponseMW
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $arrayDeParametros = $request->getParsedBody();
    $objParams = json_decode($arrayDeParametros["usuario"]);
    $correo = $objParams->correo;
    $clave = $objParams->clave;
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT correo, clave FROM usuarios WHERE correo = :correo AND clave = :clave");
    $consulta->bindValue(':correo', $correo, PDO::PARAM_STR);
    $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
    $consulta->execute();

    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultado) > 0) {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
      // Correo y contraseña válidos
    } else {
      $retorno = json_encode(array("mensaje" => "La combinacion de ese correo y contraseña no existe en la base de datos"));
      $status = 403;
      // Correo o contraseña inválidos
    }
    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }

  public function CorreoDuplicado(Request $request, RequestHandler $handler): ResponseMW
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $arrayDeParametros = $request->getParsedBody();
    $objParams = json_decode($arrayDeParametros["usuario"]);
    $correo = $objParams->correo;

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT correo FROM usuarios WHERE correo = :correo");
    $consulta->bindValue(':correo', $correo, PDO::PARAM_STR);
    $consulta->execute();

    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if (count($resultado) > 0) {
      $retorno = json_encode(array("mensaje" => "El correo ya está registrado en la base de datos"));
      $status = 403;
      // Correo ya existe
    } else {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
      // Correo válido
    }

    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }

  public function VerificarToken(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $retorno = Autentificadora::verificarJWT($token);
    $status = $retorno->verificado ? 200 : 403;
    if ($status == 403) {
      $response = new ResponseMW();
      $response = $response->withStatus($status);
      $response->getBody()->write(json_encode($retorno));
    } else {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    }
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function EmptyParamLogin(Request $request, RequestHandler $handler): ResponseMW
  {
    $arrayDeParametros = $request->getParsedBody();
    $objParams = json_decode($arrayDeParametros["usuario"]);
    $status = 409;
    if (empty($objParams->correo) && empty($objParams->clave)) {
      $retorno = json_encode(array("mensaje" => "Ambos campos están vacíos", "status" => $status));
    } else if (empty($objParams->correo)) {
      $retorno = json_encode(array("mensaje" => "Correo vacío", "status" => $status));
    } else if (empty($objParams->clave)) {
      $retorno = json_encode(array("mensaje" => "Clave vacía", "status" => $status));
    } else {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    }
    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }

  public static function SetParamLogin(Request $request, RequestHandler $handler): ResponseMW
  { {
      $arrayDeParametros = $request->getParsedBody();

      if (isset($arrayDeParametros["user"]))   $objParams = json_decode($arrayDeParametros["user"]);
      else if (isset($arrayDeParametros["usuario"])) $objParams = json_decode($arrayDeParametros["usuario"]);
      else $objParams = null;
      $status = 403;

      if ($objParams != null) {
        if (!isset($objParams->correo) && !isset($objParams->clave)) $retorno = json_encode(array("mensaje" => "parametros correo y clave no seteados", "status" => $status));
        if (!isset($objParams->correo))  $retorno = json_encode(array("mensaje" => "Correo no seteadoo", "status" => $status));
        if (!isset($objParams->clave))  $retorno = json_encode(array("mensaje" => "Clave no seteada",  "status" => $status));
        else if (isset($objParams->correo) && isset($objParams->clave)) {
          $response = $handler->handle($request);
          $retorno = (string)$response->getBody();
          $status = 200;
        }
      } else {
        $retorno = json_encode(array("mensaje" => "parametro usuario de la peticion HTTP no enviado", "status" => $status));
      }
      $response = new ResponseMW($status);
      $response->getBody()->write($retorno);
      return $response;
    }
  }
}
