<?php
require_once("./clases/NeumaticoBD.php");
use CortaMatias\NeumaticoBD;

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : "sin neumatico_json";
$neumaticoObj = json_decode($neumatico_json);
//{"marca":"BridgeStone", "medidas":"195-55-R16", "precio":33000}
$neumatico = new NeumaticoBD($neumaticoObj->marca,$neumaticoObj->medidas,$neumaticoObj->precio);
$ok = $neumatico->agregar();
if($ok) $retorno = array("exito" => true, "mensaje" => "Neumatico agregado");
else $retorno = array("exito" => false, "mensaje" => "No se pudo agregar el neumatico");
echo json_encode($retorno);