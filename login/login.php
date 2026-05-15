<?php
session_start();
include '../conexion/conexion.php';

$conn->query("ALTER TABLE usuarios MODIFY `contraseña` VARCHAR(255) NOT NULL");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Por favor ingrese el correo y la contraseña.';
    } else {
        $stmt = $conn->prepare('SELECT id, nombre, apellido, `contraseña` FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nombre, $apellido, $hash);
            $stmt->fetch();
            $stmt->close();

            if (password_verify($password, $hash) || $password === $hash) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $nombre;
                header('Location: ../index.php');
                exit;
            }

            $error = 'Correo o contraseña incorrecta.';
        } else {
            $stmt->close();
            $error = 'Correo o contraseña incorrecta.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="login.css?v=3">
  <title>Inicia Sesión</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.php">
        <img src="../imagenes/logo.jpg" alt="Alestylo" class="login-logo" width="150" height="56">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="../index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../Carrito/carrito.php">Carrito de compras</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
    <h1>Inicia Sesión</h1>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" class="controls" name="email" required>
      </div>
      <div class="form-group">
        <label for="contraseña">Contraseña</label>
        <input type="password" id="contraseña" class="controls" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
    <div class="login-options">
      <label>
        <input type="checkbox"> ¿Recordar contraseña?
      </label>
    </div>
    <div class="register-link">
      <a href="../registro/registro.php">Registrarse</a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>
</html>
