<?php
 require_once("./clases/Usuario.php");


 $_accion = isset($_POST["opcion"]) ? $_POST["opcion"] : "sin accion";
 $_nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "sin nombre";
 $clave = isset($_POST["clave"]) ? $_POST["clave"] : "sin clave"; 
$_correo = isset($_POST["correo"]) ? $_POST["correo"] : "sin correo"; 

$usuario =  new Usuario();
$usuario->nombre = $_nombre;
$usuario->clave = $clave;
$usuario->correo = $_correo;
echo json_encode($usuario->GuardarEnArchivo());
?>
