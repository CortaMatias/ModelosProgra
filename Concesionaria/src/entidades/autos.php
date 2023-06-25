<?php


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once("autentificadora.php");

class Autos implements ISlimeable
{
  public int $id;
  public string $color;
  public string $marca;
  public int $precio;
  public string $modelo;


  #region Agregar  
  public function Agregar(Request $request, Response $response, array $args): Response
  {
    $parametros = $request->getParsedBody();
    $auto_param = json_decode($parametros["auto"]);
    $auto = new Autos();
    $auto->color = $auto_param->color;
    $auto->marca = $auto_param->marca;
    $auto->modelo = $auto_param->modelo;
    $auto->precio = $auto_param->precio;

   $agregado = $auto->AgregarAutos();

    if ($agregado) {
      $retorno = new stdClass();
      $retorno->exito = true;
      $retorno->mensaje = "Auto agreado exitosamente";
      $retorno->status = 200;
      $response->getBody()->write(json_encode($retorno));
      $response->withStatus($retorno->status);
    } else {
      $retorno = new stdClass();
      $retorno->exito = false;
      $retorno->mensaje = "Error al agregar el Auto";
      $retorno->status = 418;
      $response->getBody()->write(json_encode($retorno));
      $response->withStatus($retorno->status);
    }
    return $response;
  }

  public function AgregarAutos()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO autos (color, marca, precio,modelo)"
      . "VALUES(:color,:marca,:precio,:modelo)");

    $consulta->bindValue(':color', $this->color,  PDO::PARAM_STR);
    $consulta->bindValue(':marca', $this->marca,  PDO::PARAM_STR);
    $consulta->bindValue(':precio', $this->precio,  PDO::PARAM_INT);
    $consulta->bindValue(':modelo', $this->modelo, PDO::PARAM_STR);
    return  $consulta->execute();
  }

  #endregion

  #region Listar
  public function Listar(Request $request, Response $response, array $args): Response
  {
    $retorno = new stdClass();
    $retorno->exito = true;
    $retorno->mensaje = "Lista de Autos";
    $retorno->lista = Autos::ListarAutos();

    $response->getBody()->write(json_encode($retorno));
    return $response;
  }
  public static function ListarAutos()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM autos");

    $consulta->execute();
    $autos = $consulta->fetchAll(PDO::FETCH_CLASS, "Autos");

    return $autos;
  }

  #endregion

  #region Eliminar  
  public function Eliminar(Request $request, Response $response, array $args): Response{
    $idAuto = $args['id_auto'];
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

    if($usuario->perfil == "propietario"){
      if (Autos::EliminarAuto($idAuto)) {
        $mensaje = "El auto con ID $idAuto ha sido borrado correctamente.";
        $status = 200;
    } else {
        $mensaje = "No se pudo encontrar el auto con ID $idAuto.";
        $status = 404;
    }    
    } else {
      $mensaje = "El usuario $usuario->nombre $usuario->apellido no tiene permisos para realizar esta accion, debe ser un propietario y es $usuario->perfil";
      $status = 418;
    }    

    $response->getBody()->write(json_encode(array("mensaje" => $mensaje)));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
  }

  public static function EliminarAuto($id)
  {
      $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
  
      $consulta = $objetoAccesoDato->retornarConsulta("DELETE FROM autos WHERE id = :id");
      $consulta->bindValue(':id', $id, PDO::PARAM_INT);
      $consulta->execute();
  
      $filasAfectadas = $consulta->rowCount();
  
      return $filasAfectadas > 0; // Devuelve true si se eliminó al menos una fila, false en caso contrario
  }
  
  #endregion

  #region Modificar

  public function Modificar(Request $request, Response $response, array $args): Response {
    $parametros = $request->getParsedBody();
    $auto_param = json_decode($parametros["auto"]);
    $id = json_decode($parametros["auto_id"]);
    
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);
    if($usuario->perfil == "encargado"){
      $auto = new Autos();
      $auto->id =  $id;
      $auto->color = $auto_param->color;
      $auto->marca = $auto_param->marca;
      $auto->modelo = $auto_param->modelo;
      $auto->precio = $auto_param->precio;  
      $modificado = $auto->ModificarAuto();      
      if($modificado){
        $status = 200;
        $mensaje = "Auto modificado exitosamente";
      } else {
        $mensaje = "Error al modificar el auto";
        $status = 403;
      }
    } else {
      $mensaje = "Usted no es un encargado, usted es $usuario->perfil y no tiene permisos para realizar esta accion";
      $status = 418;
    }

    $response->getBody()->write(json_encode(array("mensaje" => $mensaje)));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
  }

  public function ModificarAuto()
  {
      $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
  
      $consulta = $objetoAccesoDato->retornarConsulta("UPDATE autos SET color = :color, marca = :marca, precio = :precio, modelo = :modelo WHERE id = :id");
      $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
      $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
      $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
      $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
      $consulta->bindValue(':modelo', $this->modelo, PDO::PARAM_STR);
      $consulta->execute();
  
      $filasAfectadas = $consulta->rowCount();
  
      return $filasAfectadas > 0; // Devuelve true si se modificó al menos una fila, false en caso contrario
  }
  
  #endregion
}
