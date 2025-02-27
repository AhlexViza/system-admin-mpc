<?php
session_start();
// No es necesario verificar si el usuario está logueado en la página principal
// Si necesitas validar si están logueados en otras páginas privadas, lo harás allí.

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Consulta para obtener el avatar del usuario solo si está logueado
if ($username) {
    require 'db.php'; // Conexión a la base de datos

    // Consulta para obtener el avatar del usuario
    $query = "SELECT avatar FROM usuarios WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verifica si el avatar está configurado; si no, asigna el ícono predeterminado
    $avatar = isset($user['avatar']) ? $user['avatar'] : 'fa-user-circle'; // Ícono por defecto

    // Guarda el avatar en la sesión para usarlo dinámicamente
    $_SESSION['avatar'] = $avatar;
} else {
    $avatar = 'fa-user-circle'; // Avatar predeterminado si no está logueado
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="img/logo-canchis.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <div class="section-logo">
            <a class="logo" href="index.php">
                <img class="logo-img" src="./img/logo-canchis.png" alt="logo_canchis">
                <h2 class="subtitle">municipalidad provincial <br> de canchis</h2>
            </a>
        </div>
        <nav class="bottom-menu">
            <div class="menu-item">
                <a href=""><button class="icon-button">
                        <img class="img-responsive" src="img/candario.svg" alt="Calendario">
                    </button>
                    <label class="icon-title">Reservas</label></a>
            </div>
            <div class="menu-item">
                <a href=""><button class="icon-button">
                        <img class="img-responsive" src="./img/reclamos.svg" alt="Notas">
                    </button>
                    <label class="icon-title">Reclamos</label></a>
            </div>
            <div class="menu-item">
                <a href=""><button class="icon-button">
                        <img class="img-responsive" src="./img/telefono.svg" alt="Llamadas">
                    </button>
                    <label class="icon-title">Contactos</label></a>
            </div>
            <div class="menu-item">
                <a href=""><button class="icon-button active">
                        <img class="img-responsive" src="img/home.svg" alt="Inicio">
                    </button>
                    <label class="icon-title">Principal</label></a>
            </div>
        </nav>
        <div class="user">
            <?php if (!empty($username)): ?>
                <div class="container mt-1">
                    <!-- Menú de usuario -->
                    <div class="d-flex align-items-center position-relative icono-section">
                        <!-- Ícono del usuario -->
                        <div id="user-avatar">
                            <!-- Usamos un valor predeterminado vacío si $avatar es null -->
                            <i class="fas <?php echo htmlspecialchars(strval($avatar)); ?>" style="font-size: 40px; cursor: pointer;"></i>
                            <!-- Nombre de usuario, usamos un valor predeterminado vacío si $username es null -->
                            <span class="ms-2 fw-bold text-dark"><?php echo htmlspecialchars(strval($username)); ?></span>
                            <!-- Flecha hacia abajo para indicar opciones -->
                            <i id="dropdown-arrow" class="fas fa-chevron-down" style="font-size: 20px; margin-left: 5px; cursor: pointer;"></i>
                        </div>

                        <!-- Menú desplegable -->
                        <div class="dropdown-menu-custom shadow-lg" id="dropdown-menu">
                            <p class="text-center"><strong>¡Hola, <?php echo htmlspecialchars(strval($username)); ?>!</strong></p>
                            <ul class="list-unstyled text-center">
                                <li><a href="profile.php" class="text-decoration-none text-dark p-2">Editar perfil</a></li>
                                <li><a href="historial.php" class="text-decoration-none text-dark p-2">Historial de actividades</a></li>
                                <li><a href="logout.php" class="text-decoration-none text-dark p-2">Cerrar sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a class="user_login" href="login.php">
                    <i class="fa-regular fa-user"></i>Inicie sesión
                </a>
                <a class="user_login" href="register.php">
                    registrate
                </a>
            <?php endif; ?>
        </div>
    </header>

    <div class="overlay" id="overlay"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
        // Referencias a los elementos
        const avatar = document.getElementById('user-avatar');
        const arrow = document.getElementById('dropdown-arrow');
        const dropdownMenu = document.getElementById('dropdown-menu');
        const overlay = document.getElementById('overlay');
        const greeting = document.getElementById('greeting');

        avatar.addEventListener('click', function(event) {
            event.stopPropagation(); // Evita que el clic en el avatar cierre el menú
            const isActive = dropdownMenu.classList.contains('active');

            if (isActive) {
                dropdownMenu.classList.remove('active');
                overlay.style.display = 'none';
                greeting.style.marginTop = '10px'; // Regresar el saludo a la posición original
            } else {
                dropdownMenu.classList.add('active');
                overlay.style.display = 'block';
                /* greeting.style.marginTop = '100px'; */ // Desplazar el saludo cuando el menú está abierto
            }
        });

        // Cerrar el menú si se hace clic fuera de él
        overlay.addEventListener('click', function() {
            dropdownMenu.classList.remove('active');
            overlay.style.display = 'none';
            /* greeting.style.marginTop = '10px'; */ // Regresar el saludo a la posición original
        });

        // Cerrar el menú si se hace clic fuera del avatar
        document.addEventListener('click', function(event) {
            const isClickInside = avatar.contains(event.target) || dropdownMenu.contains(event.target) || arrow.contains(event.target);
            if (!isClickInside) {
                dropdownMenu.classList.remove('active');
                overlay.style.display = 'none';
                /* greeting.style.marginTop = '10px'; */ // Regresar el saludo a la posición original
            }
        });
    });
    </script>
</body>

</html>
