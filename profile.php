<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

// Obtener datos del usuario
$query = "SELECT * FROM usuarios WHERE username = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Procesar actualizaciones del perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $user['password'];
        $new_avatar = $_POST['avatar']; // Obtener el avatar seleccionado

        // Verificar si el correo ya está en uso (sin considerar el correo del usuario actual)
        if (!$error) {
            $check_email_query = "SELECT * FROM usuarios WHERE email = ? AND id != ?";
            $stmt = mysqli_prepare($conn, $check_email_query);
            mysqli_stmt_bind_param($stmt, 'si', $new_email, $user['id']);
            mysqli_stmt_execute($stmt);
            $email_result = mysqli_stmt_get_result($stmt);

            // Si se encuentra otro usuario con el mismo correo, se lanza un error
            if (mysqli_num_rows($email_result) > 0) {
                $error = "El correo electrónico ya está en uso por otro usuario.";
            }
        }

        // Si no hay errores, actualizar el perfil
        if (!$error) {
            $query = "UPDATE usuarios SET username = ?, email = ?, password = ?, avatar = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssssi', $new_username, $new_email, $new_password, $new_avatar, $user['id']);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Perfil actualizado exitosamente.";
                $_SESSION['username'] = $new_username;
                $_SESSION['avatar'] = $new_avatar; // Guardar el avatar actualizado en la sesión
                header("Location: index.php"); // Redirigir al index
                exit(); // Asegurarse de que no se siga ejecutando el script
            } else {
                $error = "Error al actualizar el perfil: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Incluir Font Awesome -->
    <title>Mi Cuenta</title>
</head>
<body>
    <div class="container mt-5">
        <h1>Mi Cuenta</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Selecciona un Icono de Perfil</label>
                <div class="row">
                    <div class="col-4">
                        <input type="radio" name="avatar" id="avatar1" value="fa-user-circle" <?php echo $user['avatar'] == 'fa-user-circle' ? 'checked' : ''; ?>>
                        <label for="avatar1"><i class="fas fa-user-circle" style="font-size: 40px;"></i></label>
                    </div>
                    <div class="col-4">
                        <input type="radio" name="avatar" id="avatar2" value="fa-user-alt" <?php echo $user['avatar'] == 'fa-user-alt' ? 'checked' : ''; ?>>
                        <label for="avatar2"><i class="fas fa-user-alt" style="font-size: 40px;"></i></label>
                    </div>
                    <div class="col-4">
                        <input type="radio" name="avatar" id="avatar3" value="fa-user-tie" <?php echo $user['avatar'] == 'fa-user-tie' ? 'checked' : ''; ?>>
                        <label for="avatar3"><i class="fas fa-user-tie" style="font-size: 40px;"></i></label>
                    </div>
                    <!-- Puedes agregar más íconos aquí -->
                </div>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Actualizar Perfil</button>
        </form>
    </div>
</body>
</html>
