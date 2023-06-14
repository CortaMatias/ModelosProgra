<?php

require_once("./clases/accesoDatos.php");
require_once("./clases/IBM.php");


use ejercicio_bd\AccesoDatos;

class Usuario implements IBM
{
    public int $id;
    public string $nombre;
    public string $correo;
    public string $clave;
    public int $id_perfil;
    public string $perfil;


    public function toJSON()
    {
        $alumno = array(
            "nombre" => $this->nombre,
            "correo" => $this->correo,
            "clave" => $this->clave
        );
        return json_encode($alumno);
    }

    public function GuardarEnArchivo()
    {
        //ABRO EL ARCHIVO
        $ar = fopen("./archivos/usuarios.json", "a"); //A - append()
        //ESCRIBO EN EL ARCHIVO CON FORMATO: CLAVE-VALOR_UNO-VALOR_DOS
        $cant = fwrite($ar, $this->toJSON() . "\r\n");
        if ($cant > 0) $retorno = array("exito" => true, "mensaje" => "agregado");
        else $retorno = array("exito" => false, "mensaje" => "ERROR");
        
        //CIERRO EL ARCHIVO
        fclose($ar);

        return $retorno;
    }

    public static function TraerTodosJSON()
    {
        $retorno = [];
        $ar = fopen("./archivos/usuarios.json", "r");
        while (!feof($ar)) {
            $linea = fgets($ar);
            $usuario_leido = json_decode($linea);
            if (isset($usuario_leido)) {
                $usuario = new Usuario();
                $usuario->nombre = $usuario_leido->nombre;
                $usuario->correo = $usuario_leido->correo;
                $usuario->clave = $usuario_leido->clave;
                array_push($retorno, $usuario);
            }
        }
        return $retorno;
    }

    public function Agregar()
    {

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre, correo, clave,id_perfil)"
            . "VALUES(:nombre, :correo, :clave, :id_perfil)");

        $consulta->bindValue(':nombre', $this->nombre,  PDO::PARAM_STR);
        $consulta->bindValue(':correo', $this->correo,  PDO::PARAM_STR);
        $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
        $consulta->bindValue(':id_perfil', $this->id_perfil,  PDO::PARAM_INT);
        return $consulta->execute();
    }


    public static function TraerTodos()
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios");

    $consulta->execute();

    $usuarios = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");

    return $usuarios;
}


    public static function TraerUno($params)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $objParams = json_decode($params);

        $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios WHERE clave = :clave AND correo = :correo");

        $consulta->bindValue(':correo', $objParams->correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', strval($objParams->clave), PDO::PARAM_STR);

        $consulta->execute();

        $consulta->setFetchMode(PDO::FETCH_INTO, new Usuario);

        $alumno = $consulta->fetch();


        if ($alumno instanceof Usuario) return $alumno;
        else return null;
    }

    public function Modificar()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->retornarConsulta("UPDATE usuarios SET correo = :correo,id_perfil = :id_perfil , nombre = :nombre, clave = :clave WHERE id = :id");

        $consulta->bindValue(':correo', $this->correo, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
        $consulta->bindValue(':id_perfil', $this->id_perfil, PDO::PARAM_INT);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $consulta->execute();
    }


    public static function Eliminar($id)
    {
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
}
