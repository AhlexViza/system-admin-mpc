<?php
require 'db.php';
session_start();

// Verificar si el usuario tiene rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirigir si no es administrador
    exit();
}

// Manejo del formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO usuarios (username, email, password, rol) VALUES (?, ?, ?, 'administrador')";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $passwordHash);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Administrador registrado exitosamente.";
        } else {
            $error = "Error al registrar el administrador.";
        }
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}
?>
<?php
    // Verificar si el usuario ha iniciado sesión

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    $username = $_SESSION['username'];

    // Consulta para obtener el avatar del usuario
    $query = "SELECT avatar FROM usuarios WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verifica si el avatar está configurado; si no, asigna el ícono predeterminado
    $avatar = $user['avatar'] ?: 'fa-user-circle'; // Ícono por defecto

    // Guarda el avatar en la sesión para usarlo dinámicamente
    $_SESSION['avatar'] = $avatar;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminstyle.css">
</head>
<body>
<div class="admin-header">
                <a class="admin-header-text" href="#">
                <img class="admin-img" src="./img/logo-canchis.png" alt="logo_canchis">
                    Municipalidad Provincial de Canchis
                </a>
    </div>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="sidebar">
            <div class="user">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="container mt-5">
                    <!-- Menú de usuario -->
                    <div class="d-flex align-items-center position-relative icono-section">
                        <!-- Nombre de usuario -->
                        <span class="ms-2 fw-bold text-light fs-5 mb-3">Hola, <?php echo htmlspecialchars($username); ?></span>
                            <!-- Flecha hacia abajo para indicar opciones -->
                    </div>
                </div>
            <?php else: ?>
                <a class="user_login text-decoration-none" href="login.php">
                    <button class="btn btn-primary">Iniciar sesión</button>
                </a>
            <?php endif; ?>
        </div>
                <div class="list-group">
                    <button class="bot active" id="btn_dashboard" onclick="showSection('dashboard')">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </button>
                    <button class="bot" id="btn_users" onclick="showSection('users')">
                        <i class="fas fa-users"></i> Usuarios
                    </button>
                    <button class="bot" id="btn_courts" onclick="showSection('courts')">
                        <i class="fas fa-basketball-ball"></i> Áreas Deportivas
                    </button>
                    <button class="bot" id="btn_reservations" onclick="showSection('reservations')">
                        <i class="fas fa-calendar-check"></i> Reservas
                    </button>
                    <a href="admin_register.php" class="">
                        <i class="fas fa-user-plus"></i> Registrar Administradores
                    </a>
                    <a href="#" class=" ">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                        <a class="" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
    <div class="container mt-5 main-content">
        <h1><i class="fas fa-user-plus"></i> Registrar Nuevo Administrador</h1>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar</button>
        </form>

        <a href="admin_dashboard.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
