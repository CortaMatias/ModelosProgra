<?php

require_once("./clases/Usuario.php");
require_once("./clases/accesoDatos.php");
require_once("./clases/ICRUD.php");
use ejercicio_bd\AccesoDatos;

class Empleado extends Usuario implements ICRUD{

  public string $foto;
  public int $sueldo;


  public function toJSON()
    {
        $empleado = array(
            "nombre" => $this->nombre,
            "correo" => $this->correo,
            "clave" => $this->clave,
            "id_perfil" => $this->id_perfil,
            "sueldo" => $this->sueldo,
            "foto" => $this->foto
        );
        return json_encode($empleado);
    }

// ------------ARCHIVOS-----------------------

public function AgregarArchivo(){
  if(file_exists("./archivos/empleados.json")){
    $ar = fopen("./archivos/empleados.json", "a");
    $cant = fwrite($ar, $this->toJSON() . "\r\n");
  } 
  if ($cant > 0) $retorno = array("exito" => true, "mensaje" => "agregado");
  else $retorno = array("exito" => false, "mensaje" => "ERROR"); 
  fclose($ar);

  return $retorno;
}


public static function ListarArchivo(){
  $retorno = [];
  $ar = fopen("./archivos/empleados.json", "r");
  while (!feof($ar)) {
    $linea = fgets($ar);
    $empleado_leido = json_decode($linea);
    if (isset($empleado_leido)) {
        $empleado = new Empleado();
        $empleado->nombre = $empleado_leido->nombre;
        $empleado->correo = $empleado_leido->correo;
        $empleado->clave = $empleado_leido->clave;
        $empleado->id_perfil = $empleado_leido->id_perfil;
        $empleado->foto = $empleado_leido->foto;
        $empleado->sueldo = $empleado_leido->sueldo;
        array_push($retorno, $empleado);
    }
}
return $retorno;
}

//AL NO TENER ID USAMOS COMO PRIMARY KEY EL MAIL Y LA CONTRASEÑA
public function TraerEmpleadoArchivo(string $empleado_json){
  $retorno = [];
  $ar = fopen("./archivos/empleados.json", "r");
  $empleado_buscado = json_decode($empleado_json);
  while (!feof($ar)) {
    $linea = fgets($ar);
    $empleado_leido = json_decode($linea);
    if(isset($empleado_leido)){
     if($empleado_leido->correo == $empleado_buscado->correo && $empleado_leido->clave == $empleado_buscado->clave) {
      $retorno = $empleado_leido;
     }
    }
  }
  return $retorno;
}

public static function EliminarArchivo($correo, $clave){
   // Leer el contenido del archivo empleados.json y almacenarlo en una variable
   $ar = fopen("./archivos/empleados.json", "r");
   $empleados = [];
   while (!feof($ar)) {
    $linea = fgets($ar);
    $empleado_leido = json_decode($linea);
    if (isset($empleado_leido)) {
        $empleado = new Empleado();
        $empleado->nombre = $empleado_leido->nombre;
        $empleado->correo = $empleado_leido->correo;
        $empleado->clave = $empleado_leido->clave;
        $empleado->id_perfil = $empleado_leido->id_perfil;
        $empleado->foto = $empleado_leido->foto;
        $empleado->sueldo = $empleado_leido->sueldo;
        array_push($empleados, $empleado);
    }
} 
   // Buscar el empleado con el correo y clave proporcionados
   $empleadoEncontrado = null;
   foreach ($empleados as $empleado) {
     if ($empleado->correo == $correo && $empleado->clave == $clave) {
       $empleadoEncontrado = $empleado;
       break;
     }
   }
 
   // Si se encontró el empleado, eliminarlo del array
   if ($empleadoEncontrado != null) {
     $indice = array_search($empleadoEncontrado, $empleados);
     unset($empleados[$indice]);
   }
 
   $ar = fopen("./archivos/empleados.json", "w");
   foreach($empleados as $empleado){
    $cant = fwrite($ar, $empleado->toJSON() . "\r\n");
   }  
   fclose($ar);
   
   if ($cant > 0) return array("exito" => true, "mensaje" => "Empleado eliminado");
   else return array("exito" => false, "mensaje" => "No se pudo eliminar el empleado");
 }


//{"id":21, "correo":"javisarrr@mail.com", "clave":123, "nombre":"javier", "id_perfil": 1, "sueldo":33500, "path_foto": "fake.jpg"}
//{"id":25,"nombre":"Alfredito-Cambiado","correo":"Alfredito@mail.com","clave":"2930a","id_perfil":2,"sueldo":25099,"foto":".\/empleados\/fotos\/Alfredito.201714.png"}

public function ModificarArchivo(){
  $ar = fopen("./archivos/empleados.json", "r");
   $empleados = [];
   while (!feof($ar)) {
    $linea = fgets($ar);
    $empleado_leido = json_decode($linea);
    if (isset($empleado_leido)) {
        $empleado = new Empleado();
        $empleado->nombre = $empleado_leido->nombre;
        $empleado->correo = $empleado_leido->correo;
        $empleado->clave = $empleado_leido->clave;
        $empleado->id_perfil = $empleado_leido->id_perfil;
        $empleado->foto = $empleado_leido->foto;
        $empleado->sueldo = $empleado_leido->sueldo;
        array_push($empleados, $empleado);
    }
}

  $empleadoEncontrado = null;
  if(isset($empleados)){
    foreach ($empleados as $empleado) {
      if ($empleado->correo == $this->correo && $empleado->clave == $this->clave) {
        $empleadoEncontrado = $empleado;
        break;
      }
    }
  }
  
  if ($empleadoEncontrado != null) {
    // Modificar los datos del empleado
    $nuevosDatos = array("nombre" => $this->nombre, "id_perfil" => $this->id_perfil, "sueldo" => $this->sueldo, "foto" => $this->foto);
    foreach ($nuevosDatos as $clave => $valor){
      $empleadoEncontrado->$clave = $valor;
    }

      // Convertir el array de nuevo a un string JSON y guardar el contenido en el archivo
      $ar = fopen("./archivos/empleados.json", "w");
      foreach($empleados as $empleado){
       $cant = fwrite($ar, $empleado->toJSON() . "\r\n");
      }  
      fclose($ar);
  
      // Devolver un mensaje indicando si el empleado fue modificado o no
      if ($cant > 0) return array("exito" => true, "mensaje" => "Empleado modificado");
      else return array("exito" => false, "mensaje" => "No se pudo modificar el empleado");
    } else {
      return array("exito" => false, "mensaje" => "No se encontró el empleado");
    }
}
  
  public static function TraerTodos()
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM empleados");

    $consulta->execute();

    $consulta->setFetchMode(PDO::FETCH_CLASS, 'Empleado');

    return $consulta->fetchAll();
}
// ------------ARCHIVOS-----------------------


//---------------BASE DE DATOS----------------
public function Agregar()
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("INSERT INTO empleados (nombre, correo, clave, id_perfil, foto, sueldo)"
        . "VALUES(:nombre, :correo, :clave, :id_perfil, :foto, :sueldo)");

    $consulta->bindValue(':nombre', $this->nombre,  PDO::PARAM_STR);
    $consulta->bindValue(':correo', $this->correo,  PDO::PARAM_STR);
    $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
    $consulta->bindValue(':id_perfil', intval($this->id_perfil),  PDO::PARAM_INT);
    $consulta->bindValue(':foto', $this->foto,  PDO::PARAM_STR);
    $consulta->bindValue(':sueldo', intval($this->sueldo),  PDO::PARAM_INT);

    return $consulta->execute();
}

public function Modificar() : bool
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("UPDATE empleados SET foto = :foto, correo = :correo, id_perfil = :id_perfil, nombre = :nombre, clave = :clave WHERE id = :id");

    $consulta->bindValue(':correo', $this->correo, PDO::PARAM_STR);
    $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
    $consulta->bindValue(':clave', strval($this->clave), PDO::PARAM_STR);
    $consulta->bindValue(':id_perfil', $this->id_perfil, PDO::PARAM_INT);
    $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
    $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);

    $empleado = Empleado::ObtenerEmpleado($this->id);
    if($empleado){
      if (file_exists($empleado->foto)) {
        unlink($empleado->foto);   
      }
    }    
    $modificado = $consulta->execute();   
    if ($modificado && $consulta->rowCount() > 0) return true;
    else return false;    
}


public static function Eliminar($id)
{
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

    $consulta = $objetoAccesoDato->retornarConsulta("DELETE FROM empleados WHERE id = :id");

    $consulta->bindValue(':id', intval($id), PDO::PARAM_INT);

    $empleado = Empleado::ObtenerEmpleado($id);
    if($empleado){
      if (file_exists($empleado->foto)) {
        unlink($empleado->foto);
      }
    }    
    
    $eliminado = $consulta->execute();   
    if ($eliminado && $consulta->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

public static function ObtenerEmpleado($id)
{    
    $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
    
    $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM empleados WHERE id = :id");        
    
    $consulta->bindValue(':id', intval($id), PDO::PARAM_INT);

    $consulta->execute();    
   
    $consulta->setFetchMode(PDO::FETCH_INTO, new Empleado);    

    $empleado = $consulta->fetch();

    return $empleado;
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
      $pathMover ="./empleados/fotos/".$nombre . "." .$hora.".".$file_ext;
      $path = "./backend/empleados/fotos/".$nombre . "." .$hora.".".$file_ext;
        move_uploaded_file($file_tmp, $pathMover);     
        return $path; //VA A LA BASE DE DATOS 
    } else echo $errors;
  } else echo 'Error: No se ha seleccionado un archivo para cargar.';
}
//---------------BASE DE DATOS----------------

} 

?>