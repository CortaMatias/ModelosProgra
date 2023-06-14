<?php
namespace CortaMatias;
require_once("./clases/IParte1.php");
require_once("./clases/IParte2.php");
require_once("./clases/IParte3.php");
require_once("./clases/IParte4.php");
require_once("./clases/accesoDatos.php");
require_once("./clases/Neumatico.php");

use IParte1;
use ejercicio_bd\AccesoDatos;
use IParte2;
use IParte3;
use IParte4;
use PDO;

class NeumaticoBD extends Neumatico implements IParte1,IParte2,IParte3,IParte4{
    protected $id;
    protected $pathFoto;

    public function __construct($marca, $medidas, $precio, $id = null, $pathFoto = null) {
        parent::__construct($marca, $medidas, $precio);
        $this->id = $id;
        $this->pathFoto = $pathFoto;
    }

    public function Id():int{
      return $this->id;
  }
  public function Pathfoto():string{
      return $this->pathFoto;
  }

    public function toJSON() {
        $data = array(
            'id' => $this->Id(),
            'marca' => $this->Marca(),
            'medidas' => $this->Medidas(),
            'precio' => $this->Precio(),
            'pathFoto' => $this->PathFoto()
        );

        return json_encode($data);
    }

    public function agregar(){
      $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

      $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO neumaticos (marca,medidas,precio,foto)"
          . "VALUES(:marca, :medidas, :precio,:foto)");
  
      $consulta->bindValue(':marca', $this->marca,  PDO::PARAM_STR);
      $consulta->bindValue(':medidas', $this->medidas,  PDO::PARAM_STR);
      $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
      $consulta->bindValue(':foto', $this->pathFoto,  PDO::PARAM_STR);
  
      return $consulta->execute();    
    }

    public static function traer():array{
      $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();      
      $consulta =$objetoAccesoDato->retornarConsulta("SELECT * FROM  neumaticos");
      $consulta->execute();
      $neumaticos = array();
      while($fila = $consulta->fetch())
      {
          $id =  $fila[0];
          $marca = $fila[1];
          $medidas = $fila[2];
          $precio = $fila[3];
          $foto = $fila[4];
          if($foto != null){
              $item= new NeumaticoBD($marca,$medidas,$precio,$id,$foto); 
          }else{
              $item= new NeumaticoBD($marca,$medidas,$precio,$id,"sin foto");
          }
          array_push($neumaticos, $item);
      }
      return $neumaticos;
  }

  public static function eliminar($id){
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("DELETE FROM neumaticos WHERE id = :id");

    $consulta->bindValue(':id', intval($id), PDO::PARAM_INT);    
  
    $eliminado = $consulta->execute();   
    if ($eliminado && $consulta->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
  }

  public function modificar ():bool{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta =$objetoAccesoDato->retornarConsulta("UPDATE neumaticos SET marca = :marca, medidas = :medidas, 
                                                    precio = :precio,foto = :foto WHERE id = :id");

    $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
    $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
    $consulta->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
    $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
    $consulta->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

    return $consulta->execute();
}

public static function ObtenerNeumatico($id)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM neumaticos WHERE id = :id");

    $consulta->bindValue(':id', intval($id), PDO::PARAM_INT);

    $consulta->execute();

    $consulta->setFetchMode(PDO::FETCH_ASSOC);

    $neumatico = $consulta->fetch();

    return $neumatico;
}


public function Existe(array $neumaticos):bool{
  $retorno = false;
  if(count($neumaticos) > 0){
      foreach($neumaticos as $neumatico) {
          if(($this->marca == $neumatico->marca && $this->medidas == $neumatico->medidas )|| $this->id == $neumatico->id ){
              $retorno = true;
              break;
          }
      }
  }
  return $retorno; 
}

public function guardarEnArchivo(){
  $path = "./archivos/neumaticosbd_borrados.txt";
  $pathActual = $this->pathFoto;
  $this->pathFoto = "./neumaticosBorrados/".$this->id. "." . $this->marca . "." . "borrado". "." . "png";  
  if(file_exists($path)){
    $ar = fopen($path, "a");
    $cant = fwrite($ar, $this->toJSON() . "\r\n");
  } 
  if ($cant > 0) $retorno = array("exito" => true, "mensaje" => "agregado");
  else $retorno = array("exito" => false, "mensaje" => "ERROR");
  fclose($ar);
  
  $this->moverImagen($pathActual,"./neumaticosBorrados/".$this->id. "." . $this->marca . "." . "borrado". "." . "png");
  return $retorno;
}

public function moverImagen($origen, $destino) {
  if (!file_exists($origen)) {      
  }
  if (rename($origen, $destino)) {
  } 
}

public static function ListarArchivo(){
  $retorno = [];
  $ar = fopen("./archivos/neumaticosbd_borrados.txt", "r");
  while (!feof($ar)) {
    $linea = fgets($ar);
    $nuematico_leido = json_decode($linea);
    if (isset($nuematico_leido)) {
        $neumatico = new NeumaticoBD($nuematico_leido->marca, $nuematico_leido->medidas,$nuematico_leido->precio, $nuematico_leido->id,$nuematico_leido->pathFoto);
        array_push($retorno, $neumatico);
    }
}
return $retorno;
}

public static function mostrarBorradosJSON(){
  $retorno = [];
  $ar = fopen("./archivos/neumaticos_eliminados.json", "r");
  while (!feof($ar)) {
    $linea = fgets($ar);
    $nuematico_leido = json_decode($linea);
    if (isset($nuematico_leido)) {
        $neumatico = new Neumatico($nuematico_leido->marca, $nuematico_leido->medidas,$nuematico_leido->precio);
        array_push($retorno, $neumatico);
    }
}
return $retorno;
}


public static function validarFoto($nombre){
  if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
  
    // Obtener la información del archivo
    $file_name = $_FILES['foto']['name'];
    $file_size = $_FILES['foto']['size'];
    $file_tmp = $_FILES['foto']['tmp_name'];
    $file_type = $_FILES['foto']['type'];
    $errors = "";
    
    // Obtener la extensión del archivo
    $file_ext_arr = explode('.',$_FILES['foto']['name']);
    $file_ext = strtolower(end($file_ext_arr));
    
    // Definir los formatos de archivo permitidos y el tamaño máximo permitido en bytes
    $allowed_extensions = array('jpg', 'jpeg', 'png');
    $max_file_size = 50 * 1024 * 1024; // 50 MB
    
    // Validar la extensión del archivo
    if(in_array($file_ext, $allowed_extensions) === false){
        $errors .= 'Error: La extensión del archivo no está permitida. Solo se permiten archivos de tipo JPG o PNG.';
    }
    
    // Validar el tamaño del archivo
    if($file_size > $max_file_size){
      $errors .= 'Error: El tamaño del archivo es mayor al permitido. El tamaño máximo permitido es de 50 MB.';
    }
    
    // Si no hay errores, renombrar y mover el archivo a la carpeta de destino
    if(empty($errors) == true){
      $hora = str_replace(':', '', date('H:i:s'));
      $path = "./neumaticos/imagenes/".$nombre . "." .$hora.".".$file_ext;
        move_uploaded_file($file_tmp, $path);     
        return $path; //VA A LA BASE DE DATOS 
    } else echo $errors;
  } else echo 'Error: No se ha seleccionado un archivo para cargar.';
  }

}
