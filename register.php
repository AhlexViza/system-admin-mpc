<?php
require 'db.php';
session_start();

// Manejo del formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validación de campos vacíos
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Sanitización y validación de datos
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Validación de correo electrónico
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Por favor, ingresa un correo electrónico válido.";
        } elseif (strlen($password) < 6) {
            // Validación de contraseña (mínimo 6 caracteres)
            $error = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            // Hashear la contraseña
            $password = password_hash($password, PASSWORD_BCRYPT);
            $avatar = 'fa-user-circle';  // Avatar por defecto

            // Verificar si el correo ya existe en la base de datos
            $query = "SELECT email FROM usuarios WHERE email = ?";
            if ($stmt = mysqli_prepare($conn, $query)) {
                mysqli_stmt_bind_param($stmt, 's', $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    // Si el correo ya está registrado, mostramos un mensaje de error
                    $error = "El correo electrónico ya está registrado. Por favor, usa otro.";
                } else {
                    // Insertar el nuevo usuario en la base de datos
                    $query = "INSERT INTO usuarios (username, email, password, avatar) VALUES (?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($conn, $query)) {
                        mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $password, $avatar);

                        // Ejecutar la consulta y verificar si tiene éxito
                        if (mysqli_stmt_execute($stmt)) {
                            // Redirigir a la página de login con un mensaje de éxito
                            $_SESSION['success_message'] = "Registro exitoso. Ahora puedes iniciar sesión.";
                            header("Location: login.php");
                            exit();
                        } else {
                            $error = "Error al registrar el usuario: " . mysqli_error($conn);
                        }
                    } else {
                        $error = "Error al preparar la consulta de inserción: " . mysqli_error($conn);
                    }
                }
                // Cerrar la consulta de verificación de correo
                mysqli_stmt_close($stmt);
            } else {
                $error = "Error al preparar la consulta de verificación: " . mysqli_error($conn);
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
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Establecer la imagen de fondo */
        .fondo-personalizado {
            margin: 0;
            height: 100vh;
            background-size: cover;
            background-position: center;
            position: relative;
            /* Necesario para la superposición de elementos */
            transition: background-image 1s ease-in-out, opacity 1s ease-in-out;
            /* Transición para el fondo y la opacidad */
            opacity: 1;
            /* Comienza visible */
            /*             filter: brightness(80%); */
        }
    </style>
</head>

<body>
    <div class="fondo-personalizado d-flex align-items-center row justify-content-around">

        <div class="container col-xl-4 col-sm-12">

            <!-- Mostrar mensajes de error -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensaje de éxito si está presente -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; ?>
                </div>
                <?php unset($_SESSION['success_message']); // Limpiar el mensaje de éxito después de mostrarlo 
                ?>
            <?php endif; ?>

            <!-- Formulario de registro -->
            <form method="POST" class="shadow p-4 rounded border bg-white">
                <h1 class="mb-4">Crear una cuenta de usuario</h1>
                <div class="mb-3">
                    <label for="username" class="form-label">Nombre de Usuario</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                <p class="mt-3">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </form>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/fondo.js"></script>
</body>

</html>