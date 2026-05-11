<?php
include "../conexion/conexion.php";

$id_pedido = $_GET['id'] ?? null;

if (!$id_pedido) {
    die("ID de pedido no proporcionado.");
}

// Consultar los datos del pago/cliente
$sql_pago = "SELECT * FROM pagos WHERE id = ?";
$stmt = $conn->prepare($sql_pago);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$result_pago = $stmt->get_result();
$pago = $result_pago->fetch_assoc();
$stmt->close();

if (!$pago) {
    die("Pedido no encontrado.");
}

// Consultar los productos del carrito
$sql_detalles = "SELECT * FROM detalles_pago WHERE id_pago = ?";
$stmt_detalles = $conn->prepare($sql_detalles);
$stmt_detalles->bind_param("i", $id_pedido);
$stmt_detalles->execute();
$result_detalles = $stmt_detalles->get_result();
$productos = [];
$total = 0;
while ($row = $result_detalles->fetch_assoc()) {
    $productos[] = $row;
    $total += $row['precio'] * $row['cantidad'];
}
$stmt_detalles->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Compra #<?php echo htmlspecialchars($id_pedido); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .invoice-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
        }

        .table th {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="container invoice-container">
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="invoice-title">FACTURA</h2>
                <p class="mb-0">Pedido #<?php echo htmlspecialchars($id_pedido); ?></p>
                <p class="text-muted"><?php echo htmlspecialchars($pago['fecha']); ?></p>
            </div>
            <div class="text-end">
                <h4>Alestylo</h4>
                <p class="mb-0">Tu estilo al alcance de un clic</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <h5 class="mb-3">Facturado a:</h5>
                <div><strong><?php echo htmlspecialchars($pago['nombre'] . ' ' . $pago['apellidos']); ?></strong></div>
                <div><?php echo htmlspecialchars($pago['email']); ?></div>
                <div><?php echo htmlspecialchars($pago['telefono']); ?></div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h5 class="mb-3">Datos de Envío:</h5>
                <div><?php echo htmlspecialchars($pago['direccion']); ?></div>
                <div><?php echo htmlspecialchars($pago['ciudad'] . ', ' . $pago['departamento']); ?></div>
                <div>Método de pago: <strong><?php echo htmlspecialchars($pago['metodo_pago']); ?></strong></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Color</th>
                        <th>Talla</th>
                        <th class="text-center">Cant</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                            <td><?php echo htmlspecialchars($item['color']); ?></td>
                            <td><?php echo htmlspecialchars($item['talla']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td class="text-end">$<?php echo number_format($item['precio'], 2); ?></td>
                            <td class="text-end">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total a Pagar</th>
                        <th class="text-end fs-5 text-success">$<?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-center mt-5">
            <p>¡Gracias por tu compra!</p>
            <a href="../index.html" class="btn btn-primary">Volver a la tienda</a>
            <button onclick="window.print()" class="btn btn-outline-secondary">Imprimir Factura</button>
        </div>
    </div>
</body>

</html>