<?php
require_once("./clases/Neumatico.php");
use CortaMatias\Neumatico;

$marca = isset($_POST["marca"]) ? $_POST["marca"] : "sin marca";
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : "sin medidas";
$neumatico = new Neumatico($marca, $medidas, -1);
$retorno = Neumatico::verificarNeumaticoJSON($neumatico);
echo json_encode($retorno);
?>