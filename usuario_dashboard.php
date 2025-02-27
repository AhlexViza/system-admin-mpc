<?php
session_start();

// Verificar si el usuario está autenticado y es usuario normal
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.php");
    exit();
}

echo "<h1>Bienvenido, Usuario {$_SESSION['nombre']}</h1>";
echo "<a href='logout.php'>Cerrar sesión</a>";
?>