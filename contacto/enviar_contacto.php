<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.html');
    exit;
}

// Configuracion SMTP. Para Gmail usa una contrasena de aplicacion.
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUser = 'alestylo139@gmail.com';
$smtpPass = 'hbwj tjsr xevm eubx';
$destinatario = 'alestylo139@gmail.com';

function limpiarHeader($valor)
{
    return str_replace(["\r", "\n"], '', trim($valor));
}

function leerRespuestaSmtp($socket)
{
    $respuesta = '';

    while ($linea = fgets($socket, 515)) {
        $respuesta .= $linea;
        if (isset($linea[3]) && $linea[3] === ' ') {
            break;
        }
    }

    return $respuesta;
}

function comandoSmtp($socket, $comando, $codigosEsperados)
{
    fwrite($socket, $comando . "\r\n");
    $respuesta = leerRespuestaSmtp($socket);
    $codigo = (int) substr($respuesta, 0, 3);

    if (!in_array($codigo, (array) $codigosEsperados, true)) {
        throw new Exception('Error SMTP: ' . trim($respuesta));
    }

    return $respuesta;
}

function enviarCorreoSmtp($host, $port, $usuario, $password, $para, $asunto, $mensaje, $replyTo)
{
    $password = preg_replace('/\s+/', '', $password);
    $socket = fsockopen($host, $port, $errno, $errstr, 20);

    if (!$socket) {
        throw new Exception("No se pudo conectar al servidor SMTP: $errstr ($errno)");
    }

    leerRespuestaSmtp($socket);
    comandoSmtp($socket, 'EHLO localhost', 250);
    comandoSmtp($socket, 'STARTTLS', 220);

    if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        throw new Exception('No se pudo activar TLS para SMTP.');
    }

    comandoSmtp($socket, 'EHLO localhost', 250);
    comandoSmtp($socket, 'AUTH LOGIN', 334);
    comandoSmtp($socket, base64_encode($usuario), 334);
    comandoSmtp($socket, base64_encode($password), 235);
    comandoSmtp($socket, "MAIL FROM:<$usuario>", 250);
    comandoSmtp($socket, "RCPT TO:<$para>", [250, 251]);
    comandoSmtp($socket, 'DATA', 354);

    $asuntoSeguro = limpiarHeader($asunto);
    $replyToSeguro = limpiarHeader($replyTo);
    $mensaje = str_replace(["\r\n", "\r"], "\n", $mensaje);
    $mensaje = str_replace("\n.", "\n..", $mensaje);

    $contenido = "From: Alestylo <$usuario>\r\n";
    $contenido .= "Reply-To: $replyToSeguro\r\n";
    $contenido .= "To: $para\r\n";
    $contenido .= "Subject: $asuntoSeguro\r\n";
    $contenido .= "MIME-Version: 1.0\r\n";
    $contenido .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $contenido .= str_replace("\n", "\r\n", $mensaje);

    fwrite($socket, $contenido . "\r\n.\r\n");
    $respuesta = leerRespuestaSmtp($socket);
    $codigo = (int) substr($respuesta, 0, 3);

    if ($codigo !== 250) {
        throw new Exception('Error SMTP: ' . trim($respuesta));
    }

    comandoSmtp($socket, 'QUIT', 221);
    fclose($socket);
}

function mostrarResultado($tipo, $titulo, $mensaje)
{
    $esExito = $tipo === 'success';
    $clase = $esExito ? 'success' : 'error';
    $simbolo = $esExito ? '✓' : '!';
    $tituloSeguro = htmlspecialchars($titulo);
    $mensajeSeguro = htmlspecialchars($mensaje);

    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$tituloSeguro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            font-family: "Poppins", Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.24), transparent 34%),
                linear-gradient(135deg, #f8fafc 0%, #eef2ff 42%, #fdf2f8 100%);
            color: #111827;
        }

        .result-card {
            width: min(92vw, 520px);
            padding: 42px 34px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 24px 70px rgba(31, 41, 55, 0.18);
            backdrop-filter: blur(14px);
        }

        .status-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 22px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            font-size: 46px;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 14px 28px rgba(79, 70, 229, 0.28);
        }

        .result-card.success .status-icon {
            background: linear-gradient(135deg, #22c55e, #4f46e5);
        }

        .result-card.error .status-icon {
            background: linear-gradient(135deg, #ef4444, #f97316);
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 5vw, 3.1rem);
            font-weight: 800;
            letter-spacing: 0;
        }

        p {
            margin: 0 auto 28px;
            max-width: 420px;
            color: #4b5563;
            font-size: 1.05rem;
            line-height: 1.65;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-primary,
        .btn-outline-secondary {
            min-width: 155px;
            border-radius: 999px;
            padding: 11px 18px;
            font-weight: 700;
        }

        .btn-primary {
            border: none;
            background: #4f46e5;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.28);
        }

        .btn-primary:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>
    <main class="result-card $clase">
        <div class="status-icon">$simbolo</div>
        <h1>$tituloSeguro</h1>
        <p>$mensajeSeguro</p>
        <div class="actions">
            <a href="contacto.html" class="btn btn-primary">Enviar otro</a>
            <a href="../index.php" class="btn btn-outline-secondary">Volver a la tienda</a>
        </div>
    </main>
</body>
</html>
HTML;
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$comentario = trim($_POST['comentario'] ?? '');

if ($nombre === '' || $correo === '' || $comentario === '') {
    mostrarResultado('error', 'Faltan datos', 'Por favor completa nombre, correo y comentario.');
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    mostrarResultado('error', 'Correo no valido', 'Revisa el correo electronico e intenta nuevamente.');
}

if ($smtpPass === 'PON_AQUI_TU_CONTRASENA_DE_APLICACION') {
    mostrarResultado('error', 'Falta configuracion', 'Falta configurar la contrasena de aplicacion de Gmail.');
}

$asunto = 'Nuevo mensaje de contacto - Alestylo';
$mensaje = "Nombre: $nombre\r\n";
$mensaje .= "Correo: $correo\r\n";
$mensaje .= "Telefono: $telefono\r\n\r\n";
$mensaje .= "Comentario:\r\n$comentario\r\n";

try {
    enviarCorreoSmtp($smtpHost, $smtpPort, $smtpUser, $smtpPass, $destinatario, $asunto, $mensaje, $correo);
    mostrarResultado('success', 'Mensaje enviado con exito', 'Gracias por escribirnos. Recibimos tu mensaje y te responderemos lo antes posible.');
} catch (Exception $e) {
    mostrarResultado('error', 'No se pudo enviar', 'Detalle: ' . $e->getMessage());
}
?>
