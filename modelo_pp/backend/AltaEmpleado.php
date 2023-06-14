<?php
require_once("./clases/Empleado.php");



$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "sin nombre";
$correo = isset($_POST["correo"]) ? $_POST["correo"] : "sin correo";
$clave = isset($_POST["clave"]) ? $_POST["clave"] : "sin clave";
$id_perfil = isset($_POST["id_perfil"]) ? $_POST["id_perfil"] : "sin id_perfil";
$sueldo = isset($_POST["sueldo"]) ? $_POST["sueldo"] : "sin sueldo";
$foto = isset($_POST["foto"]) ? $_POST["foto"] : "sin foto";
$accion = isset($_POST["accion"]) ? $_POST["accion"] : "sin accion";


$empleado = new Empleado();
$empleado->nombre = $nombre;
$empleado->correo = $correo;
$empleado->clave = $clave;
$empleado->id_perfil = $id_perfil;
$empleado->sueldo = intval($sueldo);
$empleado->foto = Empleado::validarFoto($empleado->nombre);

if($accion == "agregarbd"){
  if($empleado->Agregar()){
    $retorno = array("exito" => true, "mensaje" => "Empleado agregado");
  } else $retorno = array("exito" => false, "mensaje" => "Empleado no agregado [ERROR]");
}else if ($accion == "agregarArchivo"){
  $retorno = $empleado->AgregarArchivo();
}else $retorno = $accion;

echo json_encode($retorno);





?>