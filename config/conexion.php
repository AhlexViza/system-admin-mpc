<?php

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "sports_reservation";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>