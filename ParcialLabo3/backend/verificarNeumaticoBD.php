<?php
require_once("./clases/NeumaticoBD.php");
require_once("./clases/Neumatico.php");
use CortaMatias\NeumaticoBD;
use CortaMatias\Neumatico;


$neumatico_json = isset($_POST["obj_neumatico"]) ? $_POST["obj_neumatico"] : "sin neumatico_json";

$neumaticoObj = json_decode($neumatico_json);

$neumaticoVerificar = new NeumaticoBD($neumaticoObj->marca,$neumaticoObj->medidas, -1,-1, "");

$neumaticos = $neumaticoVerificar->traer();

foreach($neumaticos as $neumatico){
  if($neumatico->Marca() == $neumaticoVerificar->Marca() && $neumatico->Medidas() == $neumaticoVerificar->Medidas()){
    echo $neumaticoVerificar->toJSON();
  }else echo "{}"; 
}
