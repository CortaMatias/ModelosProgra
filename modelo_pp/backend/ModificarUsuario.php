<?php
require_once("./clases/Usuario.php");

$usuario_json = isset($_POST["usuario_json"]) ? $_POST["usuario_json"] : "sin usuario";

$usuario = new Usuario();
$usuario_decode = json_decode($usuario_json);  

$usuario->nombre = $usuario_decode->nombre;
$usuario->id = $usuario_decode->id;
$usuario->correo = $usuario_decode->correo;
$usuario->id_perfil = $usuario_decode->id_perfil;
$usuario->clave = $usuario_decode->clave;
if($usuario->Modificar()) $retorno = array("exito" => true, "mensaje" => "Usuario modificado con exito");
else $retorno = array("exito" => false, "mensaje" => "[ERROR] al modificar");

echo json_encode($retorno);
?>