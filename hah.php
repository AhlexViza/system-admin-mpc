<?php
$password = 'contraseña'; // Reemplaza por tu contraseña deseada
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "El hash generado es: " . $hash;
?>
