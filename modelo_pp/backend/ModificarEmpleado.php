<?php
require_once("./clases/Empleado.php");

$empleado_json = isset($_POST["empleado_json"]) ? $_POST["empleado_json"] : "sin empleado";
$accion = isset($_POST["accion"]) ? $_POST["accion"] : "sin accion";

$empleado_decode = json_decode($empleado_json);
$empleado = new Empleado();
$empleado->nombre = $empleado_decode->nombre;
$empleado->correo = $empleado_decode->correo;
$empleado->clave = $empleado_decode->clave;
$empleado->id_perfil = $empleado_decode->id_perfil;
$empleado->sueldo = $empleado_decode->sueldo;
$empleado->foto = Empleado::validarFoto($empleado->nombre);

if($accion == "modificarbd"){
  $empleado->id = $empleado_decode->id;
  $modificar =$empleado->Modificar();
if($modificar) $retorno  = array("exito" => true, "mensaje" => "Modificado correctamente");
else $retorno  = array("exito" => false, "mensaje" => "ERROR al modificar");

} else if($accion == "modificarArchivo"){  
  $retorno = $empleado->ModificarArchivo();
}

echo json_encode($retorno);

?>