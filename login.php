<?php
require 'db.php';
session_start();

// Manejo del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitización de datos (aunque ya usamos consultas preparadas, esto asegura que no haya caracteres peligrosos)
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Verificar que el correo electrónico esté presente
    if (empty($email) || empty($password)) {
        $error = "Por favor ingresa tu correo y contraseña.";
    } else {
        // Consulta para obtener el usuario por correo electrónico
        $query = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Verificar si el usuario existe y validar la contraseña
        if ($user = mysqli_fetch_assoc($result)) {
            // Si el correo existe y la contraseña es correcta
            if (password_verify($password, $user['password'])) {
                // Guardar la información del usuario en la sesión
                $_SESSION['id_usuario'] = $user['id'];  // Guardamos el ID del usuario
                $_SESSION['username'] = $user['username'];  // Guardamos el nombre de usuario
                $_SESSION['email'] = $user['email'];  // Guardamos el correo
                $_SESSION['avatar'] = $user['avatar'];  // Guardamos el avatar (si deseas usarlo)
                $_SESSION['rol'] = $user['rol'];  // Guardamos el rol (usuario o administrador)

                // Redirigir al usuario a la página principal o al panel de administrador según el rol
                if ($user['rol'] === 'administrador') {
                    header("Location: admin_dashboard.php"); // Redirigir al panel de administración
                } else {
                    header("Location: index.php"); // Redirigir al inicio para usuarios comunes
                }
                exit();
            } else {
                // Contraseña incorrecta
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            // Correo no encontrado en la base de datos
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style-l.css">
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
        <div class="col-xl-2">
            <a class="text-decoration-none text-center" href="index.php">
                <img class="w-100" src="./img/logo-canchis.png" alt="logo_canchis">
                <h2 class="text-white fw-bold">Municipalidad provincial <br> de Canchis</h2>
            </a>
        </div>
        <div class=" col-xl-4 col-sm-12 contenedor-l bg-white p-5">
            <h1>Inicio de Sesión</h1>

            <!-- Mostrar mensajes de error -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de inicio de sesión -->
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>

            <p class="mt-3">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/fondo.js"></script>
</body>

</html>