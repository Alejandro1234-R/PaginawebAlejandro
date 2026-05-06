<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alestylo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}
else {
    echo "conexion exitosa";
}
$conn->close();

?>