<?php
require_once("./clases/Usuario.php");

$usuario_json = isset($_POST["usuario_json"]) ? $_POST["usuario_json"] : "sin usuario";

$usuario = Usuario::TraerUno($usuario_json);

if($usuario != NULL){
  $retorno=array("exito" => true, "mensaje" => "Encontrado");
} else $retorno=array("exito" => false, "mensaje" => "NO ENCONTRADO");

echo json_encode($retorno);


?>
