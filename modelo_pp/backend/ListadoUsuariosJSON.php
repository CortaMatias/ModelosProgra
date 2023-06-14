<?php
require_once("./clases/Usuario.php");

$usuarios =  Usuario::TraerTodosJSON();
echo json_encode($usuarios);
?>