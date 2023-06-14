<?php 
require_once("./clases/Usuario.php");

$tabla = isset($_GET["tabla"]) ? $_GET["tabla"] : "sin tabla";


if($tabla == "mostrar"){
  $usuarios = Usuario::TraerTodos();
	echo json_encode($usuarios);
}else{
  $usuarios = null;
	echo $tabla;
}
/*
<!DOCTYPE html>
<html>
<head>
	<title>Listado de Usuarios</title>
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
	<h1>Listado de Usuarios</h1>
	<table>
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Correo</th>
				<th>Perfil</th>
			</tr>
		</thead>
		<tbody>
			<?php if(isset($usuarios))foreach ($usuarios as $usuario): ?>
				<tr>
					<td><?= $usuario->nombre ?></td>
					<td><?= $usuario->correo ?></td>
					<td><?= $usuario->id_perfil ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</body>
</html>*/
?>



