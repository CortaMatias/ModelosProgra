<?php
require_once("./clases/NeumaticoBD.php");
use CortaMatias\NeumaticoBD;

$marca = isset($_POST["marca"]) ? $_POST["marca"] : "sin marca";
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : "sin medidas";
$precio = isset($_POST["precio"]) ? $_POST["precio"] : "sin precio";

$neumatico = new NeumaticoBD($marca,$medidas,$precio,-1, NeumaticoBD::validarFoto($marca));
$existe = $neumatico->Existe($neumatico->traer());
if($existe) {
  $retorno = array("exito" => false, "mensaje" => "Ya existe un neumatico de esa marca y esa medida en la base de datos");
  unlink($neumatico->pathFoto());
}
else{
  $ok = $neumatico->agregar();
  if($ok)  $retorno = array("exito" => true, "mensaje" => "Neumatico agregado");
  else  $retorno = array("exito" => false, "mensaje" => "Error al agregar el neumatico");
}
echo json_encode($retorno);

