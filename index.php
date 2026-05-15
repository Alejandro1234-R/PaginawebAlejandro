<?php
session_start();
include 'conexion/conexion.php';
include 'productos_seed.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login/login.php');
    exit;
}

asegurarProductosIniciales($conn);

$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Usuario');
$html = file_get_contents(__DIR__ . '/index.html');
$productosHtml = '';
$productos = $conn->query("SELECT id, nombre, precio, imagen FROM productos ORDER BY id DESC LIMIT 8");

while ($producto = $productos->fetch_assoc()) {
    $id = (int) $producto['id'];
    $nombre = htmlspecialchars($producto['nombre']);
    $precio = '$' . number_format((float) $producto['precio'], 0, ',', '.');
    $imagen = htmlspecialchars(str_replace('\\', '/', $producto['imagen']));

    $productosHtml .= '
      <a href="producto.php?id=' . $id . '">
        <div class="card" style="background-image: url(\'' . $imagen . '\');">
          <p class="subtitle">' . $nombre . '</p>
          <h2>' . $precio . '</h2>
        </div>
      </a>';
}

$html = str_replace(
    '<li class="nav-item">
            <a class="nav-link" href="login/login.html">Iniciar sesión</a>
          </li>',
    '<li class="nav-item">
            <span class="nav-link"><i class="fa-solid fa-user"></i> ' . $userName . '</span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login/logout.php">Cerrar sesión</a>
          </li>',
    $html
);

$html = preg_replace(
    '/<section class="container-related-products">.*?<\/section><br><br>/s',
    '<section class="container-related-products">
    <h2>Lo mas reciente</h2>
    <div class="card-container">' . $productosHtml . '
    </div>
  </section><br><br>',
    $html
);

echo $html;
?>
