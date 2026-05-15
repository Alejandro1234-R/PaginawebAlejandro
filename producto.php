<?php
session_start();
include 'conexion/conexion.php';
include 'productos_seed.php';

asegurarProductosIniciales($conn);

$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id, nombre, precio, descripcion, imagen FROM productos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    header('Location: index.php');
    exit;
}

$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Usuario');
$isLoggedIn = isset($_SESSION['user_id']);
$nombre = htmlspecialchars($producto['nombre']);
$descripcion = htmlspecialchars($producto['descripcion']);
$imagen = htmlspecialchars(str_replace('\\', '/', $producto['imagen']));
$precioTexto = '$' . number_format((float) $producto['precio'], 0, ',', '.');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $nombre ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
      <img src="imagenes/img/Logo tienda online.png" width="40%" height="70px" style="display: flex; margin: auto;">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <?php if ($isLoggedIn): ?>
            <li class="nav-item"><span class="nav-link"><i class="fa-solid fa-user"></i> <?= $userName ?></span></li>
            <li class="nav-item"><a class="nav-link" href="login/logout.php">Cerrar sesion</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login/login.php">Iniciar sesion</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Volver a la tienda</a></li>
          <li class="nav-item"><a class="nav-link" href="Carrito/carrito.php">Carrito de compras</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div id="mensaje-confirmacion" class="alert alert-success" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 1000;">
    Producto agregado al carrito.
  </div>

  <main class="contenido-todo">
    <div class="container-img">
      <img src="<?= $imagen ?>" alt="<?= $nombre ?>">
    </div>
    <div class="container-info-product">
      <div class="container-price">
        <span><?= $precioTexto ?></span>
      </div>
      <div class="container-details-product">
        <div class="form-group">
          <label for="colour">Color</label>
          <select name="colour" id="colour">
            <option disabled selected value="">Escoge una opcion</option>
            <option value="negro">Negro</option>
            <option value="blanco">Blanco</option>
          </select>
        </div>
      </div>
      <div class="container-details-product">
        <div class="form-group">
          <label for="size">Talla</label>
          <select name="size" id="size">
            <option disabled selected value="">Escoge una opcion</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
          </select>
        </div>
      </div>
      <div class="container-add-cart">
        <div class="container-quantity">
          <label for="quantity">Cantidad:</label>
          <input type="number" id="quantity" placeholder="1" value="1" min="1" class="input-quantity">
        </div>
        <button class="btn-add-to-cart" data-nombre="<?= $nombre ?>" data-precio="<?= $precioTexto ?>" data-imagen="<?= $imagen ?>">
          Anadir al carrito
        </button>
      </div>
      <div class="container-description">
        <div class="title-description">
          <h6>Descripcion</h6>
        </div>
        <div class="text-description">
          <p><?= $descripcion ?></p>
        </div>
      </div>
    </div>
  </main>

  <script src="Carrito/carrito.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
