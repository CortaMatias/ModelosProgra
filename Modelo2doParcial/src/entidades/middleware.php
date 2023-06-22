<?php
require_once("imiddleware.php");

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class MW implements IMiddleware
{

  public function ListarUsuarioEncargado(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];

    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = isset(($dataUsuario->payload->data)) ? json_decode($dataUsuario->payload->data) : null;
    $perfil = isset($usuario->perfil) ? $usuario->perfil : null;
    $encargado = "encargado";
    $response = $handler->handle($request);
    $retorno = json_decode($response->getBody(), true);
    if ($perfil &&  $perfil == $encargado) {
      if (isset($retorno['lista']) && is_array($retorno['lista'])) {
        foreach ($retorno['lista'] as &$user) {
          unset($user['id']);
          unset($user['clave']);
        }
      }
    }
    $status = 200;
    $response = new ResponseMW($status);
    $response->getBody()->write(json_encode($retorno));
    return $response;
  }

  public function ListarUsuarioEmpleado(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];

    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = isset(($dataUsuario->payload->data)) ? json_decode($dataUsuario->payload->data) : null;
    $perfil = isset($usuario->perfil) ? $usuario->perfil : null;
    $empleado = "empleado";
    $response = $handler->handle($request);
    $retorno = json_decode($response->getBody(), true);
    if ($perfil &&  $perfil == $empleado) {
      if (isset($retorno['lista']) && is_array($retorno['lista'])) {
        foreach ($retorno['lista'] as &$user) {
          unset($user['correo']);
          unset($user['clave']);
          unset($user['perfil']);
        }
      }
    }
    $status = 200;
    $response = new ResponseMW($status);
    $response->getBody()->write(json_encode($retorno));
    return $response;
  }

  public function ListarUsuarioPropietario(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = isset(($dataUsuario->payload->data)) ? json_decode($dataUsuario->payload->data) : null;
    $perfil = isset($usuario->perfil) ? $usuario->perfil : null;
    $queryParams = $request->getQueryParams();

    if ($perfil && $perfil === "propietario") {
      $status = 200;
      $response = $handler->handle($request);
      $retorno = json_decode($response->getBody(), true);

      $apellido = $queryParams['apellido'] ?? null;

      // Verificar si el parámetro 'apellido' está vacío o indefinido
      if (empty($apellido)) {
        // Contar los apellidos distintos y la cantidad de repeticiones
        $apellidos = array_column($retorno['lista'], 'apellido');
        $cantidadApellidos = array_count_values($apellidos);
        $retorno = array("apellidos" => $cantidadApellidos);
      } else {
        // Filtrar los usuarios cuyo apellido coincida y contar la cantidad
        $usuariosCoincidentes = array_filter($retorno['lista'], function ($usuario) use ($apellido) {
          return $usuario['apellido'] === $apellido;
        });
        $cantidadUsuarios = count($usuariosCoincidentes);
        $retorno = array("apellidos" => $usuariosCoincidentes, "cantidad_apellidos" => $cantidadUsuarios);
      }
    } else {
      $status = 403;
      $retorno = array("mensaje" => "Sin permisos para realizar esta acción");
    }

    $response = new ResponseMW($status);
    $response->getBody()->write(json_encode($retorno));
    return $response;
  }


  public function MiddlewareListar(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];

    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = isset(($dataUsuario->payload->data)) ? json_decode($dataUsuario->payload->data) : null;
    $perfil = isset($usuario->perfil) ? $usuario->perfil : null;

    if ($perfil) {
      $response = $handler->handle($request);
      $retorno = json_decode($response->getBody(), true); //LE PONEMOS TRUE PARA UTILIZAR COMO ARrAY ASOCIATIVO, sino es un stdCLass
      //REMOVER ID
      if ($perfil === 'encargado') {
        if (isset($retorno['lista']) && is_array($retorno['lista'])) {
          foreach ($retorno['lista'] as &$auto) {
            unset($auto['id']);
          }
        }
      } else if ($perfil === 'empleado') {
        // Obtener los colores distintos de los autos
        $coloresDistintos = array_unique(array_column($retorno['lista'], 'color'));
        // Agregar la cantidad de colores distintos a los datos de respuesta
        $retorno = array("Cantidad de colores" => count($coloresDistintos));
      } else if ($perfil === "propietario") {
        $retorno = $retorno;
      }
    } else {
      $retorno = array("mensaje" => "Verificar Permisos");
    }
    $status = 200;
    $response = new ResponseMW($status);
    $response->getBody()->write(json_encode($retorno));
    return $response;
  }

  public function MiddlewareListarID(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = isset(($dataUsuario->payload->data)) ? json_decode($dataUsuario->payload->data) : null;
    $perfil = isset($usuario->perfil) ? $usuario->perfil : null;
    $queryParams = $request->getQueryParams();
    $id_auto = $queryParams['id_auto'] ?? null;

    if ($perfil && $perfil === "propietario") {
      $status = 200;
      $response = $handler->handle($request);
      $retorno = json_decode($response->getBody(), true);
      if (isset($queryParams['id_auto']) && !empty($queryParams['id_auto'])) {
        // Lógica para obtener auto por ID
        $id_auto = $queryParams['id_auto'];
        $autoEncontrado = null;
        foreach ($retorno['lista'] as $auto) {
          if ($auto['id'] == $id_auto) {
            $autoEncontrado = $auto;
            break;
          }
        }
        if ($autoEncontrado) {
          $retorno = $autoEncontrado;
        } else {
          $retorno = array("mensaje" => "No se encontró ningún auto con el ID proporcionado.");
          $status = 404;
        }
      }
    } else {
      $status = 403;
      $retorno = array("mensaje" => "Sin permisos para realizar esta accion");
    }
    $response = new ResponseMW($status);
    $response->getBody()->write(json_encode($retorno));
    return $response;
  }

  public function EmpleadoColores(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $response = $handler->handle($request);
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);
    $perfil = $usuario->perfil();
    $retorno = json_decode($response->getBody(), true);
    $status = $response->getStatusCode();

    $response = new ResponseMW($status);
    $response->getBody()->write(json_encode($retorno));
    return $response;
  }



  public function ValidarPropietario(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    if ($usuario->perfil == "propietario") {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    } else {
      $retorno = json_encode(array("propietario" =>  false, "mensaje" => "Usted no es propietario"));
      $status = 409;
    }
    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }

  public function ValidarEncargado(Request $request, RequestHandler $handler): ResponseMW
  {
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    if ($usuario->perfil == "encargado") {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
    } else {
      $retorno = json_encode(array("encargado" =>  false, "mensaje" => "Usted no es encargado"));
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



  public function SetParamLogin(Request $request, RequestHandler $handler): ResponseMW
  { {
      $arrayDeParametros = $request->getParsedBody();

      $objParams = isset($arrayDeParametros["usuario"]) ? json_decode($arrayDeParametros["usuario"]) : null;
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


  public function AutoValidate(Request $request, RequestHandler $handler): ResponseMW
  {
    $arrayDeParametros = $request->getParsedBody();
    $objParams = json_decode($arrayDeParametros["auto"]);

    $precio = $objParams->precio;
    $color = $objParams->color;

    if ($precio < 50000 || $precio > 600000) {
      $retorno = json_encode(array("mensaje" => "El precio debe estar entre 50,000 y 600,000"));
      $status = 409;
      // Precio fuera de rango
    } elseif ($color === "azul") {
      $retorno = json_encode(array("mensaje" => "El color no puede ser azul"));
      $status = 409;
      // Color no permitido
    } else {
      $response = $handler->handle($request);
      $retorno = (string)$response->getBody();
      $status = 200;
      // Pasa validación, permitir acceso al verbo de la API
    }

    $response = new ResponseMW($status);
    $response->getBody()->write($retorno);
    return $response;
  }
}
