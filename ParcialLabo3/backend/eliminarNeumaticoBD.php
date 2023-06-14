<?php
require_once("./clases/NeumaticoBD.php");
require_once("./clases/Neumatico.php");
use CortaMatias\NeumaticoBD;
use CortaMatias\Neumatico;


$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : "sin neumatico_json";
$neumaticoObj = json_decode($neumatico_json);

$ok = NeumaticoBD::eliminar($neumaticoObj->id);
if($ok) {
 $neumatico = new Neumatico($neumaticoObj->marca, $neumaticoObj->medidas, 
 $neumaticoObj->precio);
 $neumatico->guardarJSON("./archivos/neumaticos_eliminados.json");
 $retorno = array("exito" => true, "mensaje" => "Neumatico eliminado con exito"); 
} else $retorno = array("exito" => false, "mensaje" => "Error al eliminar el neumatico"); 

echo json_encode($retorno);