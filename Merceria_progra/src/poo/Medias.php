<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
require_once("autentificadora.php");

class Medias {
    public int $id;
    public string $marca;
    public int $precio;
    public int $talle;
    public string $color;


    #region Agregar
    public function Agregar(Request $request, Response $response, array $args): Response
    {
        $parametros = $request->getParsedBody();
        $medias_param = json_decode($parametros["medias"]);
        $medias = new Medias();
        $medias->marca = $medias_param->marca;
        $medias->precio = $medias_param->precio;
        $medias->talle = $medias_param->talle;
        $medias->color = $medias_param->color;

        $agregado = $medias->AgregarMedias();

        if ($agregado) {
            $retorno = new stdClass();
            $retorno->exito = true;
            $retorno->mensaje = "Medias agregadas exitosamente";
            $retorno->status = 200;
            $response->getBody()->write(json_encode($retorno));
            $response->withStatus($retorno->status);
        } else {
            $retorno = new stdClass();
            $retorno->exito = false;
            $retorno->mensaje = "Error al agregar las Medias";
            $retorno->status = 418;
            $response->getBody()->write(json_encode($retorno));
            $response->withStatus($retorno->status);
        }
        return $response;
    }

    public function AgregarMedias()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO medias (marca, precio, talle, color) VALUES(:marca, :precio, :talle, :color)");
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':talle', $this->talle, PDO::PARAM_INT);
        $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
        return  $consulta->execute();
    }
    #endregion

    #region Listar
    public function Listar(Request $request, Response $response, array $args): Response
    {
        $retorno = new stdClass();
        $retorno->exito = true;
        $retorno->mensaje = "Lista de Medias";
        $retorno->lista = Medias::ListarMedias();

        $response->getBody()->write(json_encode($retorno));
        return $response;
    }

    public static function ListarMedias()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM medias");

        $consulta->execute();
        $medias = $consulta->fetchAll(PDO::FETCH_CLASS, "Medias");

        return $medias;
    }
    #endregion

    #region Eliminar
    public function Eliminar(Request $request, Response $response, array $args): Response
    {
        $idMedias = $args['id_medias'];
        $token = $request->getHeader("token")[0];
        $dataUsuario = Autentificadora::obtenerPayLoad($token);
        $usuario = json_decode($dataUsuario->payload->data);

        if ($usuario->perfil == "propietario") {
            if (Medias::EliminarMedias($idMedias)) {
                $mensaje = "Las Medias con ID $idMedias han sido borradas correctamente.";
                $status = 200;
            } else {
                $mensaje = "No se pudieron encontrar las Medias con ID $idMedias.";
                $status = 404;
            }
        } else {
            $mensaje = "El usuario $usuario->nombre $usuario->apellido no tiene permisos para realizar esta acción, debe ser un propietario y es $usuario->perfil";
            $status = 418;
        }

        $response->getBody()->write(json_encode(array("mensaje" => $mensaje)));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public static function EliminarMedias($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->retornarConsulta("DELETE FROM medias WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        return $filasAfectadas > 0; // Devuelve true si se eliminó al menos una fila, false en caso contrario
    }
    #endregion

    #region Modificar
    public function Modificar(Request $request, Response $response, array $args): Response
    {
        $parametros = $request->getQueryParams();
        $medias_param = json_decode($parametros["medias"]);
        $id =$parametros["id_medias"];

        $token = $request->getHeader("token")[0];
        $dataUsuario = Autentificadora::obtenerPayLoad($token);
        $usuario = json_decode($dataUsuario->payload->data);

        if ($usuario->perfil == "encargado" || $usuario->perfil == "propietario")  {
            $medias = new Medias();
            $medias->id = $id;
            $medias->marca = $medias_param->marca;
            $medias->precio = $medias_param->precio;
            $medias->talle = $medias_param->talle;
            $medias->color = $medias_param->color;
            $modificado = $medias->ModificarMedias();
            if ($modificado) {
                $status = 200;
                $mensaje = "Medias modificadas exitosamente";
            } else {
                $mensaje = "Error al modificar las Medias, verificar que haya cambios a realizar en los valores";
                $status = 403;
            }
        } else {
            $mensaje = "Usted no es un encargado ni propietario, usted es $usuario->perfil y no tiene permisos para realizar esta acción";
            $status = 418;
        }

        $response->getBody()->write(json_encode(array("mensaje" => $mensaje)));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function ModificarMedias()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->retornarConsulta("UPDATE medias SET marca = :marca, precio = :precio, talle = :talle, color = :color WHERE id = :id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':talle', $this->talle, PDO::PARAM_INT);
        $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        return $filasAfectadas > 0; // Devuelve true si se modificó al menos una fila, false en caso contrario
    }
    #endregion

}
