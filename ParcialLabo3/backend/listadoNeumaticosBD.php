<?php
require_once "./clases/neumaticoBD.php";

use CortaMatias\NeumaticoBd;

$mostar = isset($_GET["tabla"]) ? $_GET["tabla"] : "sin tabla";
$retorno = NeumaticoBd::traer();

if ($mostar == "mostrar") {
  echo "<style>
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
  </style>";
  echo "
  <table >
      <thead>
          <tr>
              <th>ID</th>
              <th>MARCA</th>
              <th>MEDIDAS</th>
              <th>PRECIO</th>
              <th>PATH</th>
              <th>Foto</th>
              <th>Acciones</th>
          </tr>
      </thead>";
  foreach ($retorno as $neumatico) {
    echo "<tr>";
    echo "<td>" . $neumatico->Id() . "</td>";
    echo "<td>" . $neumatico->Marca() . "</td>";
    echo "<td>" . $neumatico->Medidas() . "</td>";
    echo "<td>" . $neumatico->Precio() . "</td>";
    echo "<td>" . $neumatico->Pathfoto() . "</td>";
    echo "<td>";
    if ($neumatico->Pathfoto() != "sin foto") {
      $pathFoto = $neumatico->PathFoto();
      $newPathFoto = str_replace("./neumaticos", "./backend/neumaticos", $pathFoto);
      echo '<img src=' . $newPathFoto . ' alt=' . $neumatico->Pathfoto() . ' height="100px" width="100px">';
    } else {
      echo "Sin datos //";
    }
    echo "</td>";
    echo '<td>
          <button name="btnModificar" data-json=\'' . $neumatico->toJSON() . '\'>Modificar</button>
          <button name="btnEliminar" data-json=\'' . $neumatico->toJSON() . '\'>Eliminar</button>
              </td>';

    echo "</tr>";
  }
  echo "</table>";
} else {
  foreach ($retorno as $neumatico) {
    echo $neumatico->toJSON();
  }
}
