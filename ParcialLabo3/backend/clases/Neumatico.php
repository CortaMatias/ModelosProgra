<?php

namespace CortaMatias;

class Neumatico {
    protected string $marca;
    protected string $medidas;
    protected float $precio;

    public function __construct($marca, $medidas, $precio) {
      $this->marca = $marca;
      $this->medidas = $medidas;
      $this->precio = $precio;
  }

  public function Marca():string{
    return $this->marca;
}
public function Medidas():string{
    return $this->medidas;
}
public function Precio():float{
    return $this->precio;
}
  public function toJSON() {
      $data = array(
          'marca' => $this->marca,
          'medidas' => $this->medidas,
          'precio' => $this->precio
      );

      return json_encode($data);
  }

  public function guardarJSON($path){
    if(file_exists($path)){
      $ar = fopen($path, "a");
      $cant = fwrite($ar, $this->toJSON() . "\r\n");
    } 
    if ($cant > 0) $retorno = array("exito" => true, "mensaje" => "agregado");
    else $retorno = array("exito" => false, "mensaje" => "ERROR"); 
    fclose($ar);
  
    return $retorno;
  }

  public static function traerJSON($path){
    $retorno = [];
    $ar = fopen($path, "r");
    while (!feof($ar)) {
      $linea = fgets($ar);
      $neumatico_leido = json_decode($linea);
      if (isset($neumatico_leido)) {
          $neumatico = new Neumatico($neumatico_leido->marca,$neumatico_leido->medidas,$neumatico_leido->precio);
          array_push($retorno, $neumatico);
      }
  }
  return $retorno;
  }
  
  public static function verificarNeumaticoJSON(Neumatico $neumaticoParam){
    $neumaticos = self::traerJSON("./archivos/neumaticos.json");
    $retorno = false;
    $cant = 0;
    foreach($neumaticos as $neumatico){
      if($neumatico->marca == $neumaticoParam->marca && $neumatico->medidas == $neumaticoParam->medidas){
         $retorno = true;
         $cant = $cant + $neumatico->precio;
      }
    }
    if($retorno == true ){
      $retorno = array("exito" => true, "mensaje" => "Neumatico encontrado, la sumatoria de los precios es de $" .$cant);
    }else $retorno = array("exito" => false, "mensaje" => "Neumatico no encontrado");
    return $retorno;
  }
  



}

?>







