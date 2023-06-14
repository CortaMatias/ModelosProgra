<?php
require_once("./clases/NeumaticoBD.php");
use CortaMatias\NeumaticoBD;

if(count($_POST) > 0){

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : "sin neumatico_json";
$neumaticoObj = json_decode($neumatico_json);

$neumaticoEliminar = NeumaticoBD::ObtenerNeumatico($neumaticoObj->id);
if($neumaticoEliminar != false){
  $foto = $neumaticoEliminar["foto"];
  $neumatico = new NeumaticoBD($neumaticoObj->marca,$neumaticoObj->medidas, $neumaticoObj->precio,$neumaticoObj->id,$foto);
    if($neumatico->eliminar($neumatico->Id())){      
        $escrito = $neumatico->guardarEnArchivo();
        if($escrito["exito"]) $retorno = array("exito" => true, "mensaje" => "Neumatico eliminado con exito");        
    }else  $retorno = array("exito" => false, "mensaje" => "Error al eliminar el neumatico"); 
  }else $retorno = array("exito" => false, "mensaje" => "El neumatico no se encuentra en la base de datos"); 
  echo json_encode($retorno);
  
} else {  
  $elementos = NeumaticoBD::ListarArchivo();
  echo '<table style="border-collapse: collapse; width: 80%; padding: 10px; margin: 50px auto; text-align: center;">';
  echo '<tr><th style="border: 1px solid black; padding: 8px;">ID</th><th style="border: 1px solid black; padding: 8px;">Marca</th><th style="border: 1px solid black; padding: 8px;">Medidas</th><th style="border: 1px solid black; padding: 8px;">Precio</th><th style="border: 1px solid black; padding: 8px;">Path de la Foto</th><th style="border: 1px solid black; padding: 8px;">Foto</th></tr>';
  
  foreach ($elementos as $elemento) {
    $pathFoto = $elemento->PathFoto();
    $newPathFoto = str_replace("./neumaticosBorrados", "./backend/neumaticosBorrados", $pathFoto);
      echo '<tr>';
      echo '<td style="border: 1px solid black; padding: 8px;">' . $elemento->Id() . '</td>';
      echo '<td style="border: 1px solid black; padding: 8px;">' . $elemento->Marca() . '</td>';
      echo '<td style="border: 1px solid black; padding: 8px;">' . $elemento->Medidas() . '</td>';
      echo '<td style="border: 1px solid black; padding: 8px;">' . $elemento->Precio() . '</td>';
      echo '<td style="border: 1px solid black; padding: 8px;">' . $elemento->PathFoto() . '</td>';
      echo '<td style="border: 1px solid black; padding: 8px;"><img src="' . $newPathFoto . '" style="width: 80px; height: 80px;"></td>';
      echo '</tr>';
  }
  
  echo '</table>';
}



