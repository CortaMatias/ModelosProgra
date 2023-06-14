<?php
require_once("./clases/Empleado.php");

 $id = isset($_POST["id"]) ? $_POST["id"] : "sin id";
 $accion = isset($_POST["accion"]) ? $_POST["accion"] : "Sin accion";
 $correo = isset($_POST["correo"]) ? $_POST["correo"] : "Sin correo";
 $clave = isset($_POST["clave"]) ? $_POST["clave"] : "Sin clave";

 if($accion == "eliminarbd"){
  if(Empleado::Eliminar($id)) $retorno = array("exito" => true, "mensaje" => "Empleado eliminado");
  else  $retorno = array("exito" => false, "mensaje" => "Error al eliminar Empleado");
 }
 else if($accion == "eliminarArchivo"){
  $retorno = Empleado::EliminarArchivo($correo, $clave);
 }


echo json_encode($retorno);
?>