<?php
require_once("./clases/Usuario.php");

$id = isset($_POST["id"]) ? $_POST["id"] : "sin id";
$accion = isset($_POST["accion"]) ? $_POST["accion"] : "sin accion";

if (isset($accion)){
  $succes = Usuario::Eliminar($id);
    if($succes) $retorno = array("exito" => true, "mensaje" => "Usuario eliminado correctamente");
    else $retorno = array("exito" => false, "mensaje" => "No se pudo eliminar correctamente");
    echo json_encode($retorno);
} else {
  echo($accion);
}

?>
