<?php

require_once("./clases/NeumaticoBD.php");
require_once("./clases/Neumatico.php");
use CortaMatias\NeumaticoBD;
use CortaMatias\Neumatico;


$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : "sin neumatico_json";
$neumaticoObj = json_decode($neumatico_json);
$neumatico = new NeumaticoBD($neumaticoObj->marca,$neumaticoObj->medidas, $neumaticoObj->precio,$neumaticoObj->id);
if($neumatico->Existe(NeumaticoBD::traer())){
  $ok = $neumatico->modificar();
  if($ok) $retorno = array("exito" => true, "mensaje" => "Neumatico modificado con exito"); 
  else $retorno = array("exito" => false, "mensaje" => "Error al modificar el neumatico"); 
}else $retorno = array("exito" => false, "mensaje" => "El neumatico no se encuentra en la base de datos"); 

echo json_encode($retorno);

