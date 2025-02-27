<?php
// backend/api/db_connect.php

// Configurar la zona horaria a Perú
date_default_timezone_set('America/Lima');

// Detalles de conexión a la base de datos
$servername = "localhost";
$username = "root";        // Reemplaza con tu usuario de MySQL
$password = "";     // Reemplaza con tu contraseña de MySQL
$dbname = "sports_reservation";  // Nombre de la base de datos creada

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['error' => "Conexión fallida: " . $conn->connect_error]));
}
?>
