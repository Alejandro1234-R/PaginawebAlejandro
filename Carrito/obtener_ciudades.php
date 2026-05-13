<?php

include "../conexion/conexion.php";

$id_departamento = $_POST['id_departamento'];

$sql = "SELECT * FROM ciudades WHERE id_departamento = '$id_departamento'";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){
    echo '<option value="'.$row['id'].'">'.$row['nombre_ciudad'].'</option>';
}

?>