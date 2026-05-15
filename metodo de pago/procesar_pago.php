<?php
// Ajustamos la ruta para que encuentre conexion.php asumiendo que está un nivel arriba
include "../conexion/conexion.php";

function buscarValorPorId($conn, $tabla, $columnaNombre, $id)
{
    if ($id === '' || !ctype_digit((string) $id)) {
        return $id;
    }

    $sql = "SELECT $columnaNombre FROM $tabla WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return $id;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre);
    $stmt->fetch();
    $stmt->close();

    return $nombre ?: $id;
}

// Verificamos que sea una petición POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtenemos los datos y eliminamos espacios en blanco
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $departamento = trim($_POST['departamento'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $metodo_pago = trim($_POST['metodo_pago'] ?? '');

    $departamento = buscarValorPorId($conn, 'departamento', 'nombre_departamento', $departamento);
    $ciudad = buscarValorPorId($conn, 'ciudades', 'nombre_ciudad', $ciudad);

    // Recibir los datos del carrito (en formato JSON)
    $carrito_datos = $_POST['carrito_datos'] ?? '[]';
    $carrito = json_decode($carrito_datos, true);

    // Validamos que los campos requeridos no estén vacíos
    if (empty($nombre) || empty($email) || empty($direccion) || empty($metodo_pago) || empty($carrito)) {
        echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios o el carrito está vacío"]);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Preparamos la consulta con parámetros (?) para evitar inyecciones SQL
        $sql = "INSERT INTO pagos (nombre, apellidos, email, telefono, departamento, ciudad, direccion, metodo_pago) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $telefono, $departamento, $ciudad, $direccion, $metodo_pago);
            $stmt->execute();

            // Obtenemos el ID del pedido generado
            $id_pedido = $conn->insert_id;
            $stmt->close();

            // Insertar en detalles_pago
            $stmt_detalles = $conn->prepare("INSERT INTO detalles_pago (id_pago, nombre_producto, cantidad, color, talla, precio, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");

            foreach ($carrito as $producto) {
                $nombre_producto = $producto['nombre'] ?? '';
                $cantidad = $producto['cantidad'] ?? 1;
                $color = $producto['color'] ?? '';
                $talla = $producto['size'] ?? '';

                $precio_str = $producto['precio'] ?? '0';
                $precio = (float) preg_replace('/[^0-9.]/', '', $precio_str);
                $imagen = $producto['imagen'] ?? '';

                $stmt_detalles->bind_param("isissss", $id_pedido, $nombre_producto, $cantidad, $color, $talla, $precio, $imagen);
                $stmt_detalles->execute();
            }
            $stmt_detalles->close();

            $conn->commit();

            // Retornamos éxito y el ID
            echo json_encode([
                "status" => "success",
                "message" => "Pedido creado exitosamente",
                "id_pedido" => $id_pedido
            ]);
        } else {
            throw new Exception("Error al preparar la consulta de pagos: " . $conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}

$conn->close();
