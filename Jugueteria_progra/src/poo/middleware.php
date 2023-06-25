<?php
require_once("imiddleware.php");

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class MW implements IMiddleware
{


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

    public function JuguetesHTML(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = $handler->handle($request);
        $retorno = (string)$response->getBody();
        $status = 200;   
        $data = json_decode($retorno, true);
        $juguetes = $data['lista'];
 
        $html = '<table>';
        $html .= '<tr><th>ID</th><th>Marca</th><th>Precio</th><th>Path Foto</th></tr>';

        foreach ($juguetes as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $item["id"] . '</td>';
            $html .= '<td>' . $item["marca"] . '</td>';
            $html .= '<td>' . $item["precio"] . '</td>';
            $html .= '<td>' . $item["path_foto"] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $retorno = array("tabla" => $html);
        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($retorno));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function UsuarioPropietario(Request $request, RequestHandler $handler): ResponseMW
    {
        $token = $request->getHeader("token")[0];
        $dataUsuario = Autentificadora::obtenerPayLoad($token);
        $usuario = json_decode($dataUsuario->payload->data);
        if ($usuario->perfil == "propietario") {
            $response = $handler->handle($request);
            $retorno = (string)$response->getBody();
            $status = 200;
        } else {
            $retorno = array("exito" => false, "mensaje" => "No tiene permisos para realizar esta accion");
            $status = 403;
        }
        $response = new ResponseMW($status);
        $response->getBody()->write(json_encode($retorno));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function UsuarioHTML(Request $request, RequestHandler $handler): ResponseMW
    {
        $response = $handler->handle($request);
        $retorno = (string)$response->getBody();
        $status = 200;
        $data = json_decode($retorno, true);
        $usuarios = $data['lista'];
        $html = '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Correo</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Perfil</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($usuarios as $usuario) {
            $html .= '<tr>';
            $html .= '<td>' . $usuario['id'] . '</td>';
            $html .= '<td>' . $usuario['correo'] . '</td>';
            $html .= '<td>' . $usuario['nombre'] . '</td>';
            $html .= '<td>' . $usuario['apellido'] . '</td>';
            $html .= '<td>' . $usuario['perfil'] . '</td>';
            $html .= '<td><img src="' . "../src/fotos" . $usuario['foto'] . '" alt="Foto"></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>
        </table>';
        $retorno = array("tabla" => $html);
        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($retorno));
        return $response->withHeader('Content-Type', 'application/json');
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

    public static function SetParamLogin(Request $request, RequestHandler $handler): ResponseMW
    { {
            $arrayDeParametros = $request->getParsedBody();
          
            if(isset($arrayDeParametros["user"]))   $objParams = json_decode($arrayDeParametros["user"]);
            else if(isset($arrayDeParametros["usuario"])) $objParams = json_decode($arrayDeParametros["usuario"]);
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

    public function VerificarParametrosBD(Request $request, RequestHandler $handler): ResponseMW
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $arrayDeParametros = $request->getParsedBody();
        $objParams = json_decode($arrayDeParametros["user"]);
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
}
