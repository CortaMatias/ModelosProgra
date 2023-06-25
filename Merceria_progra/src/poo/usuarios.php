<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "accesoDatos.php";
require_once "islimeable.php";
require_once "autentificadora.php";

class Usuarios implements ISlimeable
{

  public int $id;
  public string $correo;
  public string $clave;
  public string $nombre;
  public string $apellido;
  public string $perfil;
  public string $foto;


  #region Agregar
  public function Agregar(Request $request, Response $response, array $args): Response
  {
    $parametros = $request->getParsedBody();
    $archivos = $request->getUploadedFiles();
    $usuario_param = json_decode($parametros["usuario"]);

    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $consultaId = $objetoAccesoDato->retornarConsulta("SELECT MAX(id) AS ultimoId FROM usuarios"); //Obtengo el ultimo id
    $consultaId->execute();
    $ultimoId = $consultaId->fetch(PDO::FETCH_ASSOC)['ultimoId'];
    $nuevoId = $ultimoId + 1;

    $usuario = new Usuarios();
    $usuario->nombre = $usuario_param->nombre;
    $usuario->apellido = $usuario_param->apellido;
    $usuario->clave = $usuario_param->clave;
    $usuario->correo = $usuario_param->correo;
    $usuario->perfil = $usuario_param->perfil;
    $usuario->correo = $usuario_param->correo;
    $usuario->ManejarFoto($archivos, $nuevoId);
    $agregado = $usuario->AgregarUsuario();
    $retorno = new stdClass();
    if ($agregado) {
      $retorno->exito = true;
      $retorno->mensaje = "Usuario agreado exitosamente";
      $retorno->status = 200;
    } else {
      $retorno->exito = false;
      $retorno->mensaje = "Error al agregar el Usuario";
      $retorno->status = 418;
    }
    $response->getBody()->write(json_encode($retorno));
    $response = $response->withStatus($retorno->status, $retorno->mensaje);
    return $response;
  }

  public function AgregarUsuario()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre, apellido,correo, clave,perfil,foto)"
      . "VALUES(:nombre,:apellido ,:correo, :clave, :perfil, :foto)");

    $consulta->bindValue(':nombre', $this->nombre,  PDO::PARAM_STR);
    $consulta->bindValue(':apellido', $this->apellido,  PDO::PARAM_STR);
    $consulta->bindValue(':correo', $this->correo,  PDO::PARAM_STR);
    $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
    $consulta->bindValue(':perfil', $this->perfil,  PDO::PARAM_STR);
    $consulta->bindValue(':foto', $this->foto,  PDO::PARAM_STR);
    return  $consulta->execute();
  }
  #endregion


  #region Listar
  public function Listar(Request $request, Response $response, array $args): Response
  {
    $retorno = new stdClass();
    $retorno->exito = true;
    $retorno->mensaje = "Lista de Usuarios";
    $retorno->lista = Usuarios::ListarUsuario();

    $response->getBody()->write(json_encode($retorno));
    return $response;
  }
  public static function ListarUsuario()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios");

    $consulta->execute();
    $usuarios = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuarios");

    return $usuarios;
  }
  #endregion

  #region Login
  public function Login(Request $request, Response $response, array $args): Response
  {
    $arrayDeParametros = $request->getParsedBody();
    $usuario_bd = Usuarios::LoginUsuario($arrayDeParametros['usuario']);
   
    if ($usuario_bd != null) {
      $dataUsuarioJWT = new stdClass();
      $dataUsuarioJWT->correo = $usuario_bd->correo;
      $dataUsuarioJWT->nombre = $usuario_bd->nombre;
      $dataUsuarioJWT->apellido = $usuario_bd->apellido;
      $dataUsuarioJWT->perfil = $usuario_bd->perfil;
      $dataUsuarioJWT->foto = $usuario_bd->foto;
      $jwt = Autentificadora::crearJWT(json_encode($dataUsuarioJWT), 2 * 60);
      $retorno = array("exito" => true, "mensaje" => "Usuario encontrado", "jwt" => $jwt, "status" => 200);
    } else {
      $retorno = array("exito" => false, "mensaje" => "Usuario no encontrado", "jwt" => null, "status" => 403);
    }

    $response = $response->withStatus($retorno["status"], $retorno["mensaje"]);
    $response->getBody()->write(json_encode($retorno));

    return $response;
  }

  public function LoginUsuario($params)
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $objParams = json_decode($params);
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios WHERE clave = :clave AND correo = :correo");

    $consulta->bindValue(':correo', $objParams->correo, PDO::PARAM_STR);
    $consulta->bindValue(':clave', strval($objParams->clave), PDO::PARAM_STR);

    $consulta->execute();

    $consulta->setFetchMode(PDO::FETCH_INTO, new Usuarios);

    $usuario = $consulta->fetch();

    return $usuario;
  }


  #endregion


  

  #region VerificarToken

  public function VerificarToken(Request $request, Response $response, array $args): Response
  {
    $token = $request->getHeader("token")[0];
    $retorno = Autentificadora::verificarJWT($token);
    $status = $retorno->verificado ? 200 : 403;
    $response = $response->withStatus($status);
    $response->getBody()->write(json_encode($retorno));
    return $response->withHeader('Content-Type', 'application/json');
  }

  #endregion

  public function ManejarFoto($archivos, $id)
  {
    $destino =  "../src/fotos/";
    $nombreAnterior = $archivos['foto']->getClientFilename();
    $extension = explode(".", $nombreAnterior);
    $extension = array_reverse($extension);
    $todoOk = true;

    if ($archivos['foto']->getSize() > 0) {
      $this->foto = $this->correo /* . "_" . $id */. "." . $extension[0];
      $path = $destino . $this->foto;
      $archivos['foto']->moveTo($path);
      if (!file_exists($path)) $todoOk = false;
    } else $this->foto = "null";
    return $todoOk;
  }
}
