<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "accesoDatos.php";
require_once "islimeable.php";

class Usuario implements ISlimeable
{
  public int $id;
  public string $nombre;
  public string $apellido;
  public string $correo;
  public string $foto;
  public int $id_perfil;
  public string $clave;
  public string $perfil;
  private array  $perfiles = [
    1 => 'Administrador',
    2 => 'Empleado',
    3 => 'Invitado',
    4 => 'Supervisor',
    5 => 'super_admin'
  ];


#region Login
public static function login(Request $request, Response $response): Response {
  $arrayDeParametros = $request->getParsedBody();
  $usuario_bd = Usuario::TraerUnUsuarioLogin($arrayDeParametros['usuario']);

  if($usuario_bd != null) $retorno = array("exito" => true, "mensaje" => "Usuario encontrado");
  else $retorno = array("exito" => false, "mensaje" => "Usuario no encontrado");
  
  $newResponse = $response->withStatus(200, "OK");
  $newResponse->getBody()->write(json_encode($retorno));

  return $newResponse->withHeader('Content-Type', 'application/json');
}

public static function TraerUnUsuarioLogin($params) {
  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
  $objParams = json_decode($params);
  $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios WHERE clave = :clave AND correo = :correo");

  $consulta->bindValue(':correo', $objParams->correo, PDO::PARAM_STR);
  $consulta->bindValue(':clave', strval($objParams->clave), PDO::PARAM_STR);

  $consulta->execute();

  $consulta->setFetchMode(PDO::FETCH_INTO, new Usuario);

  $usuario = $consulta->fetch();

  if ($usuario instanceof Usuario) {
    $usuario->perfil = $usuario->perfiles[$usuario->id_perfil];
    return $usuario;
  } else return null;
}
#endregion

#region TraerTodos
public function traerTodos(Request $request, Response $response, array $args): Response  {
  $usuarios = Usuario::TraerTodosUsuarios();

  $newResponse = $response->withStatus(200, "OK");
  $newResponse->getBody()->write(json_encode($usuarios));

  return $newResponse->withHeader('Content-Type', 'application/json');
}

public static function TraerTodosUsuarios()  {
  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

  $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios");

  $consulta->execute();
  $usuarios = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");

  foreach ($usuarios as $usuario) $usuario->perfil = $usuario->determinarPerfil();

  return $usuarios;
}
#endregion

#region TraerUno
public function traerUno(Request $request, Response $response, array $args): Response  {
  $data = $args['id'];
  $usuario = Usuario::TraerUnUsuario($data);
  $newResponse = $response->withStatus(200, "OK");
  $newResponse->getBody()->write(json_encode($usuario));

  return $newResponse->withHeader('Content-Type', 'application/json');
}

 public static function TraerUnUsuario($params)  {
  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
  //$objParams = json_decode($params);
  $id = $params;
  $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios WHERE id = :id");

  //$consulta->bindValue(':correo', $objParams->correo, PDO::PARAM_STR);
  $consulta->bindValue(':id', $id, PDO::PARAM_INT);
  //$consulta->bindValue(':clave', strval($objParams->clave), PDO::PARAM_STR);

  $consulta->execute();

  $consulta->setFetchMode(PDO::FETCH_INTO, new Usuario);

  $usuario = $consulta->fetch();

  if ($usuario instanceof Usuario) {
    $usuario->perfil = $usuario->perfiles[$usuario->id_perfil];
    return $usuario;
  } else return null;
}
#endregion

#region AgregarUno
public function agregarUno(Request $request, Response $response, array $args): Response  {
  $arrayDeParametros = $request->getParsedBody();
  $archivos = $request->getUploadedFiles();
  $destino = __DIR__ . "/../fotos/";
  $nombreAnterior = $archivos['foto']->getClientFilename();
  $extension = explode(".", $nombreAnterior);
  $extension = array_reverse($extension);

  $obj_usuario = json_decode($arrayDeParametros['usuario']); 
  $usuario = new Usuario();
  $usuario->nombre = $obj_usuario->nombre;
  $usuario->apellido = $obj_usuario->apellido;
  $usuario->correo = $obj_usuario->correo;
  $usuario->clave = $obj_usuario->clave;
  $usuario->id_perfil =  $obj_usuario->id_perfil;
  $usuario->perfil = $this->perfiles[$usuario->id_perfil];

  if ($archivos['foto']->getSize() > 0) {
    $path = $destino . $usuario->nombre . "-" . $usuario->perfil . "." . $extension[0];
    $usuario->foto = $usuario->nombre . "-" . $usuario->perfil . "." . $extension[0];     
    $archivos['foto']->moveTo($path);
  } else $usuario->foto = "null";
  $id_agregado = $usuario->Agregar();

  $response->getBody()->write("Usuario agregado con el id $id_agregado!");

  return $response;
}
  public function Agregar() {
  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

  $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre, apellido,correo, clave,id_perfil,foto)"
    . "VALUES(:nombre,:apellido ,:correo, :clave, :id_perfil, :foto)");

  $consulta->bindValue(':nombre', $this->nombre,  PDO::PARAM_STR);
  $consulta->bindValue(':apellido', $this->apellido,  PDO::PARAM_STR);
  $consulta->bindValue(':correo', $this->correo,  PDO::PARAM_STR);
  $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
  $consulta->bindValue(':id_perfil', $this->id_perfil,  PDO::PARAM_INT);
  $consulta->bindValue(':foto', $this->foto,  PDO::PARAM_STR);
  $consulta->execute();
  return $objetoAccesoDato->retornarUltimoIdInsertado();
}
#endregion

#region Modificar
public function modificarUno(Request $request, Response $response, array $args): Response {
  $arrayDeParametros = $request->getParsedBody();
  $archivos = $request->getUploadedFiles();
  $destino = __DIR__ . "/../fotos/";
  $nombreAnterior = $archivos['foto']->getClientFilename();
  $extension = explode(".", $nombreAnterior);
  $extension = array_reverse($extension);
  $obj = json_decode(($arrayDeParametros["cadenaJson"]));
  $usuario_bd = Usuario::TraerUnUsuario($obj->id);


  $usuario = new Usuario();
  $usuario->nombre = $obj->nombre;
  $usuario->apellido = $obj->apellido;
  $usuario->id = $obj->id;
  $usuario->id_perfil = $obj->id_perfil;
  $usuario->correo = $obj->correo;
  $usuario->clave = $obj->clave;
  $usuario->perfil = $this->perfiles[$usuario->id_perfil];

  if ($archivos['foto']->getSize() > 0) {
    unlink($destino . $usuario_bd->foto);
    $path = $destino . $usuario->nombre . "-" . $usuario->perfil . "." . $extension[0];
    $usuario->foto = $usuario->nombre . "-" . $usuario->perfil . "." . $extension[0];     
    $archivos['foto']->moveTo($path);
  } else $usuario->foto = "null";

  $ok = $usuario->Modificar();

  if ($ok) $retorno = array("exito" => true, "mensaje" => "Usuario modificado");
  else  $retorno = array("exito" => false, "mensaje" => "Error al modificar");    

  $newResponse = $response->withStatus(200, "OK");
  $newResponse->getBody()->write(json_encode($retorno));

  return $newResponse->withHeader('Content-Type', 'application/json');
}


public function Modificar() {
  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

  $consulta = $objetoAccesoDato->retornarConsulta("UPDATE usuarios SET apellido = :apellido, foto = :foto, correo = :correo,id_perfil = :id_perfil , nombre = :nombre, clave = :clave WHERE id = :id");

  $consulta->bindValue(':correo', $this->correo, PDO::PARAM_STR);
  $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
  $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
  $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
  $consulta->bindValue(':id_perfil', $this->id_perfil, PDO::PARAM_INT);
  $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
  $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);

  return $consulta->execute();
}
#endregion

#region Borrar
public function borrarUno(Request $request, Response $response, array $args): Response {
  $id = $args['id'];
  $usuario_bd = Usuario::TraerUnUsuario($id);
  $ok = Usuario::Eliminar($id);
  if ($ok){ 
    $destino = __DIR__ . "/../fotos/";
    unlink($destino . $usuario_bd->foto);
    $retorno = array("exito" => true, "mensaje" => "Usuario eliminado");
  } 
  else $retorno = array("exito" => false, "mensaje" => "Error al eliminar");

  $newResponse = $response->withStatus(200, "OK");
  $newResponse->getBody()->write(json_encode($retorno));

  return $newResponse->withHeader('Content-Type', 'application/json');
}

public static function Eliminar($id) {
  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

  $consulta = $objetoAccesoDato->retornarConsulta("DELETE FROM usuarios WHERE id = :id");

  $consulta->bindValue(':id', intval($id), PDO::PARAM_INT);

  $eliminado = $consulta->execute();

  if ($consulta->rowCount() > 0) {
    return true;
  } else {
    return false;
  }
}
public function determinarPerfil(): string  {
  return $this->perfiles[$this->id_perfil];
}
#endregion

}
