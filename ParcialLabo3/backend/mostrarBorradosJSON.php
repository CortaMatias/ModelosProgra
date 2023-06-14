<?php
require_once("./clases/NeumaticoBD.php");
require_once("./clases/Neumatico.php");
use CortaMatias\NeumaticoBD;
use CortaMatias\Neumatico;
$retorno = "";
$borrados = NeumaticoBD::mostrarBorradosJSON();
var_dump($borrados);
foreach($borrados as $borrado){
  $retorno .= $borrado->toJson();
}
echo $retorno;
