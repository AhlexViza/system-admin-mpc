<?php
// Iniciar sesión
session_start();

// Incluir la conexión a la base de datos
require_once 'config/conexion.php';

// Manejo del formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contraseña = $conn->real_escape_string($_POST['contraseña']);
    $rol = $conn->real_escape_string($_POST['rol']);

    // Verificar si el correo ya está registrado
    $sql_verificar = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultado_verificar = $conn->query($sql_verificar);

    if ($resultado_verificar->num_rows > 0) {
        $error = "El correo ya está registrado. Usa otro.";
    } else {
        // Hashear la contraseña de manera segura
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        // Insertar el nuevo usuario
        $sql_insertar = "INSERT INTO usuarios (nombre, correo, contraseña, rol) 
                         VALUES ('$nombre', '$correo', '$contraseña_hash', '$rol')";

        if ($conn->query($sql_insertar)) {
            $success = "Usuario registrado exitosamente.";
        } else {
            $error = "Error al registrar el usuario: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Registrar Nuevo Usuario</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" id="nombre" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo:</label>
                <input type="email" name="correo" class="form-control" id="correo" required>
            </div>
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" name="contraseña" class="form-control" id="contraseña" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="usuario">Usuario</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary w-100">Registrar</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php">Volver al Login</a>
        </div>
    </div>
</body>
</html>