<?php
$host = 'localhost';
$user = 'root'; // Cambia según tu configuración
$password = ''; // Cambia según tu configuración
$database = 'sports_reservation';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
