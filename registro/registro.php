<?php
include '../conexion/conexion.php';

$conn->query("ALTER TABLE usuarios MODIFY `contraseña` VARCHAR(255) NOT NULL");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    if ($nombre === '' || $apellido === '' || $email === '' || $password === '') {
        $error = 'Por favor complete todos los campos.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'El correo ya está registrado.';
            $stmt->close();
        } else {
            $stmt->close();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO usuarios (nombre, apellido, email, `contraseña`) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $nombre, $apellido, $email, $passwordHash);

            if ($stmt->execute()) {
                $success = 'Registro exitoso. Ya puedes iniciar sesión.';
            } else {
                $error = 'Error al guardar en la base de datos: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="registro.css">
    <title>Registro de Usuario</title>
</head>
<body>
    <div class="container">
        <h1>Registrarse</h1>
        <?php if (!empty($error)): ?>
            <div class="mensaje error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="mensaje success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form id="registroForm" method="POST" action="registro.php">
            <div class="input-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="input-group">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required>
            </div>
            <div class="input-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm-password">Confirmar Contraseña</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <button type="submit">Registrarse</button>
            <p class="mensaje">¿Ya tienes una cuenta? <a href="../login/login.html">Iniciar sesión</a></p>
        </form>
    </div>

    <script>
        document.getElementById('registroForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
