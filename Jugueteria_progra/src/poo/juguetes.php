<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once("autentificadora.php");

class Juguetes implements ISlimeable
{
  public int $id;
  public string $marca;
  public int $precio;
  public string $path_foto;


  #region Agregar  
  public function Agregar(Request $request, Response $response, array $args): Response
  {
    $parametros = $request->getParsedBody();
    $juguete_param = json_decode($parametros["juguete_json"]);
    $juguete = new Juguetes();
    $juguete->marca = $juguete_param->marca;
    $juguete->precio = $juguete_param->precio;
    $archivos = $request->getUploadedFiles();
    $juguete->ManejarFoto($archivos);

    $agregado = $juguete->AgregarJuguetes();
    $retorno = new stdClass();
    if ($agregado) {
      $retorno->exito = true;
      $retorno->mensaje = "Juguete agregado exitosamente";
      $retorno->status = 200;
      $response->getBody()->write(json_encode($retorno));
      $response->withStatus($retorno->status);
    } else {
      $retorno->exito = false;
      $retorno->mensaje = "Error al agregar el juguete";
      $retorno->status = 418;
      $response->getBody()->write(json_encode($retorno));
      $response->withStatus($retorno->status);
    }
    return $response;
  }

  public function AgregarJuguetes()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO juguetes (marca, precio, path_foto)"
      . "VALUES(:marca, :precio, :path_foto)");

    $consulta->bindValue(':marca', $this->marca,  PDO::PARAM_STR);
    $consulta->bindValue(':precio', $this->precio,  PDO::PARAM_INT);
    $consulta->bindValue(':path_foto', $this->path_foto, PDO::PARAM_STR);
    return  $consulta->execute();
  }

  #endregion

  #region Listar
  public function Listar(Request $request, Response $response, array $args): Response
  {
    $retorno = new stdClass();
    $retorno->exito = true;
    $retorno->mensaje = "Lista de Juguetes";
    $retorno->lista = Juguetes::ListarJuguetes();

    $response->getBody()->write(json_encode($retorno));
    return $response;
  }
  public static function ListarJuguetes()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM juguetes");

    $consulta->execute();
    $juguetes = $consulta->fetchAll(PDO::FETCH_CLASS, "Juguetes");

    return $juguetes;
  }

  #endregion

  #region Eliminar  
  public function Eliminar(Request $request, Response $response, array $args): Response{
    $idJuguete = $args['id_juguete'];
    $token = $request->getHeader("token")[0];
    $dataUsuario = Autentificadora::obtenerPayLoad($token);
    $usuario = json_decode($dataUsuario->payload->data);

   // if($usuario->perfil == "propietario"){
      if (Juguetes::EliminarJuguete($idJuguete)) {
        $mensaje = "El juguete con ID $idJuguete ha sido borrado correctamente.";
        $status = 200;
    } else {
        $mensaje = "No se pudo encontrar el juguete con ID $idJuguete.";
        $status = 404;
    }    
    /*} else {
      $mensaje = "El usuario $usuario->nombre $usuario->apellido no tiene permisos para realizar esta acción, debe ser un propietario y es $usuario->perfil";
      $status = 418;
    }  */  
    $response->getBody()->write(json_encode(array("mensaje" => $mensaje)));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
  }

  public static function EliminarJuguete($id)
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("DELETE FROM juguetes WHERE id = :id");
    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    $consulta->execute();

    $filasAfectadas = $consulta->rowCount();

    return $filasAfectadas > 0; // Devuelve true si se eliminó al menos una fila, false en caso contrario
  }

  #endregion

  #region Modificar

  public function Modificar(Request $request, Response $response, array $args): Response {
    $parametros = $request->getParsedBody();
    $juguete_param = json_decode($parametros["juguete"]);
    //$id = json_decode($parametros["juguete_id"]);
    $id = $juguete_param->id_juguete;
    $archivos = $request->getUploadedFiles();
      
      $juguete = new Juguetes();
      $juguete->id =  $id;
      $juguete->marca = $juguete_param->marca;
      $juguete->precio = $juguete_param->precio;
      $juguete->ManejarFoto($archivos, true); //con true le agrega el modificado al path
      $modificado = $juguete->ModificarJuguete();      
      if($modificado){
        $status = 200;
        $mensaje = "Juguete modificado exitosamente";
      } else {
        $mensaje = "Error al modificar el juguete";
        $status = 403;
      }    
    $response->getBody()->write(json_encode(array("mensaje" => $mensaje)));
    return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
  }

  public function ModificarJuguete()
  {
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("UPDATE juguetes SET marca = :marca, precio = :precio, path_foto = :path_foto WHERE id = :id");
    $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
    $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
    $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
    $consulta->bindValue(':path_foto', $this->path_foto, PDO::PARAM_STR);
    $consulta->execute();

    $filasAfectadas = $consulta->rowCount();

    return $filasAfectadas > 0; // Devuelve true si se modificó al menos una fila, false en caso contrario
  }

  #endregion

  public function ManejarFoto($archivos, $modificar = false)
  {
    $destino =  "../src/fotos/";
    $nombreAnterior = $archivos['foto']->getClientFilename();
    $extension = explode(".", $nombreAnterior);
    $extension = array_reverse($extension);
    $todoOk = true;

    if ($archivos['foto']->getSize() > 0) {
      if($modificar) $this->path_foto = $this->marca . "_". "modificado". "." . $extension[0];
      else $this->path_foto = $this->marca . "." . $extension[0];
      $path = $destino . $this->path_foto;
      $archivos['foto']->moveTo($path);
      if (!file_exists($path)) $todoOk = false;
    } else $this->path_foto = "null";
    return $todoOk;
  }



  
}
