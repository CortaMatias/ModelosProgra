<?php
require_once("./clases/NeumaticoBD.php");

use CortaMatias\NeumaticoBD;

if (count($_POST) > 0) {

  $neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : "sin neumatico_json";
  $neumaticoObj = json_decode($neumatico_json);

  $neumaticoModificar = NeumaticoBD::ObtenerNeumatico($neumaticoObj->id);
  if ($neumaticoModificar != false) {
    $foto = $neumaticoModificar["foto"];
    if ($foto == null) { //Si no tiene foto el que traigo      
      $neumatico = new NeumaticoBD($neumaticoObj->marca, $neumaticoObj->medidas, $neumaticoObj->precio, $neumaticoObj->id, NeumaticoBD::validarFoto($neumaticoObj->marca));
      if ($neumatico->modificar()) {
        $retorno = array("exito" => true, "mensaje" => "Neumatico modificado");
      } else {
        $retorno = array("exito" => false, "mensaje" => "Error al modificar el neumatico");
        unlink($neumatico->PathFoto());
      }
    } 
    else 
    { //si tiene foto
      $neumatico = new NeumaticoBD($neumaticoObj->marca, $neumaticoObj->medidas, $neumaticoObj->precio, $neumaticoObj->id, NeumaticoBD::validarFoto($neumaticoObj->marca));
      if ($neumatico->modificar()) {
        $hora = str_replace(':', '', date('H:i:s'));
        $extension = pathinfo($foto, PATHINFO_EXTENSION);
        $pathModificar = "./neumaticosModificados/" . $neumatico->Id() . "." . $neumatico->Marca() . "." ."modificado" . "." . $hora. "." . $extension;
        if (rename($foto, $pathModificar)) $retorno = array("exito" => true, "mensaje" => "Neumatico modificado");
        else{ $retorno = array("exito" => false, "mensaje" => "Error al modificar los registros de archivos"); unlink($neumatico->PathFoto()); } 
      } else { $retorno = array("exito" => false, "mensaje" => "Error al modificar el neumatico"); unlink($neumatico->PathFoto()); }
    }
  } else $retorno = array("exito" => false, "mensaje" => "El neumatico no se encuentra en la base de datos");
  echo json_encode($retorno);
}
