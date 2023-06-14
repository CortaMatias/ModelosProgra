<?php

require_once("./clases/Empleado.php");

$tabla = isset($_GET["tabla"]) ? $_GET["tabla"] : "sin tabla";


if($tabla == "mostrar"){
  $empleados = Empleado::TraerTodos();
}else if($tabla == "mostrarArchivo") {
  $empleados = Empleado::ListarArchivo();
}
else {
  $empleados = null;
  echo ($tabla);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Listado de empleados</title>
</head>
<body>
  <style>
    table {
      border-collapse: collapse; 
      width: 80%; 
      padding: 10px;
      margin: 50px auto;
      text-align: center;
    }
    td, th {
      border: 1px solid black;
      padding: 8px; 
      text-align: center;
    }
  </style>
  <h1>Listado de empleados</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Clave</th>
        <th>Perfil</th>
        <th>Foto</th>
        <th>Sueldo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if(isset($empleados)) foreach($empleados as $empleado) { ?>
        <tr>
          <td><?php if($tabla == "mostrar")echo $empleado->id ?></td>
          <td><?php echo $empleado->nombre ?></td>
          <td><?php echo $empleado->correo ?></td>
          <td><?php echo $empleado->clave ?></td>
          <td><?php echo $empleado->id_perfil ?></td>
          <td><img src="<?php echo $empleado->foto ?>" width="50" height="50"></td>
          <td><?php echo $empleado->sueldo ?></td>
          <td>
            <button name="btnModificar" data-empleado='<?php echo json_encode($empleado);?>'>Modificar</button>
            <button name="btnEliminar"data-empleado='<?php echo $empleado->id;?>'>Eliminar</button>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</body>
</html>
