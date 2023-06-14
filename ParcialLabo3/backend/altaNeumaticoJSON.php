<?php
require_once("./clases/Neumatico.php");
use CortaMatias\Neumatico;

$marca = isset($_POST["marca"]) ? $_POST["marca"] : "sin marca";
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : "sin medidas";
$precio = isset($_POST["precio"]) ? $_POST["precio"] : "sin precio";
$neumatico = new Neumatico($marca, $medidas, $precio);
 $retorno= $neumatico->guardarJSON("./archivos/neumaticos.json");
echo json_encode($retorno);

?>