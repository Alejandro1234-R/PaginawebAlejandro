<?php

include "../conexion/conexion.php";

$id_departamento = $_POST['id_departamento'] ?? '';

$stmt = $conn->prepare("SELECT id, nombre_ciudad FROM ciudades WHERE id_departamento = ?");
$stmt->bind_param("i", $id_departamento);
$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
    echo '<option value="'.$row['id'].'">'.$row['nombre_ciudad'].'</option>';
}

$stmt->close();

?>
