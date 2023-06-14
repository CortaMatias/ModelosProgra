<?php

$directorio = "./neumaticosModificados/";

// Obtener la lista de archivos en la carpeta
$archivos = scandir($directorio);
$newPath = str_replace("./neumaticosModificados/", "./backend/neumaticosModificados/", $directorio);

// Crear la tabla HTML con estilos
echo '<table style="border-collapse: collapse; width: 80%; padding: 10px; margin: 50px auto; text-align: center;">';
echo '<tr><th style="border: 1px solid black; padding: 8px;">Nombre</th><th style="border: 1px solid black; padding: 8px;">Imagen</th></tr>';

// Iterar sobre los archivos y mostrarlos en la tabla
foreach ($archivos as $archivo) {
    // Excluir los directorios "." y ".."
    if ($archivo != "." && $archivo != "..") {
        echo '<tr>';
        echo '<td style="border: 1px solid black; padding: 8px;">' . $archivo . '</td>';
        echo '<td style="border: 1px solid black; padding: 8px;"><img src="'  . $newPath . '/' . $archivo . '" style="width: 80px; height: 80px;"></td>';
        echo '</tr>';
    }
}

echo '</table>';