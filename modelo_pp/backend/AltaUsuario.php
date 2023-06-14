<?php
require_once("./clases/Usuario.php");

$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "sin nombre";
$correo = isset($_POST["correo"]) ? $_POST["correo"] : "sin correo";
$clave = isset($_POST["clave"]) ? $_POST["clave"] : "sin clave";
$id_perfil = isset($_POST["id_perfil"]) ? $_POST["id_perfil"] : "sin id_perfil";

$usuario = new Usuario();
$usuario->nombre = $nombre;
$usuario->correo = $correo;
$usuario->clave = $clave;
$usuario->id_perfil = $id_perfil;
if($usuario->Agregar()){
  $retorno = array("exito" => true, "mensaje" => "Usuario agregado");
} else $retorno = array("exito" => false, "mensaje" => "Usuario no agregado [ERROR]");
echo json_encode($retorno);

?>
