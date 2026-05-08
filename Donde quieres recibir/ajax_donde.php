<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos los datos del formulario (gracias al atributo 'name')
    $nombre = $_POST['nombre'] ?? 'Usuario';
    
    // Aquí puedes añadir la lógica para guardar en tu base de datos
    
    echo json_encode([
        "status" => "success",
        "message" => "¡Excelente $nombre! Tu dirección ha sido guardada."
    ]);
    exit;
}
?>