<?php
require_once("./clases/Neumatico.php");
use CortaMatias\Neumatico;

$neumaticos = Neumatico::traerJSON("./archivos/neumaticos.json");
$retorno = "[";
foreach($neumaticos as $neumatico){
  $retorno .= $neumatico->toJSON() . ",";
}
$retorno =  substr($retorno,0,-1);//Saca la ultima coma
$retorno .= "]";
echo $retorno;

?>
