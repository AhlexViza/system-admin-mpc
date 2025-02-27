<?php
session_start();
require '../db.php'; // Conexión a la base de datos

// Verificar si el usuario ha iniciado sesión

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
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

<!-- frontend/index.html -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reservas de Instalaciones Deportivas</title>

    <link rel="shortcut icon" href="../img/logo-canchis.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .calendar-cell {
            height: 60px;
            border: 1px solid #dee2e6;
            cursor: pointer;
            position: relative;
            padding: 5px;
        }
        .calendar-cell:hover {
            background-color: #f8f9fa;
        }
        .calendar-cell.disabled {
            background-color: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }
        .reserved {
            background-color: #dc3545; /* Rojo para reservado */
            color: white;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.8rem;
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            text-align: center;
        }
        .time-column {
            width: 80px;
            font-weight: bold;
        }
        /* Estilos para el modal */
        #card-errors {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    
    <header>
        <div class="section-logo">
            <a class="logo" href="../index.php">
                <img class="logo-img" src="../img/logo-canchis.png" alt="logo_canchis">
                <h2 class="subtitle">municipalidad provincial <br> de canchis</h2>
            </a>
        </div>
        <nav class="bottom-menu">
            <div class="menu-item">
                <a href=""><button class="icon-button">
                        <img class="img-responsive" src="../img/candario.svg" alt="Calendario">
                    </button>
                    <label class="icon-title">Reservas</label></a>

            </div>
            </div>
            <div class="menu-item">
                <a href=""><button class="icon-button">
                        <img class="img-responsive" src="../img/reclamos.svg" alt="Notas">
                    </button>
                    <label class="icon-title">Reclamos</label></a>

            </div>
            </div>
            <div class="menu-item">
                <a href=""><button class="icon-button">
                        <img class="img-responsive" src="../img/telefono.svg" alt="Llamadas">
                    </button>
                    <label class="icon-title">Contactos</label></a>

            </div>
            <div class="menu-item">
                <a href=""><button class="icon-button active">
                        <img class="img-responsive" src="../img/home.svg" alt="Inicio">
                    </button>
                    <label class="icon-title">Principal</label></a>

            </div>
            </div>
        </nav>
        <div class="user">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="container ">
                    <!-- Menú de usuario -->
                    <div class="d-flex align-items-center position-relative icono-section">
                        <!-- Ícono del usuario -->
                        <div id="user-avatar">
                            <i class="fas <?php echo htmlspecialchars($avatar); ?>" style="font-size: 40px; cursor: pointer;"></i>
                            <!-- Nombre de usuario -->
                            <span class="ms-2 fw-bold text-dark"><?php echo htmlspecialchars($username); ?></span>
                            <!-- Flecha hacia abajo para indicar opciones -->
                            <i id="dropdown-arrow" class="fas fa-chevron-down" style="font-size: 20px; margin-left: 5px; cursor: pointer;"></i>
                        </div>


                        <!-- Menú desplegable -->
                        <div class="dropdown-menu-custom shadow-lg" id="dropdown-menu">
                            <p class="text-center"><strong>¡Hola, <?php echo htmlspecialchars($username); ?>!</strong></p>
                            <ul class="list-unstyled text-center">
                                <li><a href="profile.php" class="text-decoration-none text-dark p-2">Editar perfil</a></li>
                                <li><a href="historial.php" class="text-decoration-none text-dark p-2">Historial de actividades</a></li>
                                <li><a href="logout.php" class="text-decoration-none text-dark p-2">Cerrar sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a class="user_login text-decoration-none" href="login.php">
                    <button class="btn btn-primary">Iniciar sesión</button>
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







    <div class="container py-4">
        <h1 class="mb-4">Sistema de Reservas de Instalaciones Deportivas</h1>
        
        <!-- Selección de Instalación -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label">Seleccionar Instalación:</label>
                <select class="form-select" id="facilitySelect">
                    <option value="" selected disabled>-- Selecciona una instalación --</option>
                    <!-- Las opciones se cargarán dinámicamente desde el backend -->
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de Deporte:</label>
                <select class="form-select" id="sportType" disabled>
                    <option value="" selected disabled>-- Selecciona un deporte --</option>
                    <option value="futbol-11">Fútbol 11</option>
                    <option value="futbol-7">Fútbol 7</option>
                    <option value="futsal">Futsal</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cancha:</label>
                <select class="form-select" id="courtType" disabled>
                    <option value="" selected disabled>-- Selecciona una cancha --</option>
                    <!-- Las opciones se cargarán dinámicamente según la instalación seleccionada -->
                </select>
            </div>
        </div>

        <!-- Precio por Hora y Precio Total -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Precio por Hora:</label>
                <input type="text" class="form-control" id="pricePerHour" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Precio Total:</label>
                <input type="text" class="form-control" id="totalPrice" readonly>
            </div>
        </div>

        <!-- Navegación del Calendario -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-secondary" id="prevWeek">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h3 id="currentWeek">Cargando...</h3>
            <button class="btn btn-secondary" id="nextWeek">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Grid del Calendario -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="time-column">Hora</th>
                        <th>Dom <span id="date0"></span></th>
                        <th>Lun <span id="date1"></span></th>
                        <th>Mar <span id="date2"></span></th>
                        <th>Mié <span id="date3"></span></th>
                        <th>Jue <span id="date4"></span></th>
                        <th>Vie <span id="date5"></span></th>
                        <th>Sáb <span id="date6"></span></th>
                    </tr>
                </thead>
                <tbody id="calendarBody">
                </tbody>
            </table>
        </div>

        <!-- Modal de Reserva -->
        <div class="modal fade" id="reservationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Realizar una Reserva</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reservationForm">
                            <div class="mb-3">
                                <label class="form-label">Fecha:</label>
                                <input type="text" class="form-control" id="reservationDate" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hora de Inicio:</label>
                                <input type="time" class="form-control" id="reservationStartTime" step="3600" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hora de Fin:</label>
                                <input type="time" class="form-control" id="reservationEndTime" step="3600" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="customerName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="customerEmail" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono:</label>
                                <input type="tel" class="form-control" id="customerPhone" required>
                            </div>
                            <!-- Selección de Método de Pago -->
                            <div class="mb-3">
                                <label class="form-label">Método de Pago:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="payNow" value="now" required>
                                    <label class="form-check-label" for="payNow">
                                        Pagar Ahora
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="payAtCourt" value="at_court" required>
                                    <label class="form-check-label" for="payAtCourt">
                                        Pagar en el Momento de la Entrada a la Cancha
                                    </label>
                                </div>
                            </div>

                            <!-- Sub-Formulario de Pago -->
                            <div id="paymentOptions" style="display: none;">
                                <label class="form-label">Selecciona tu método de pago:</label>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary w-100 mb-2" id="payWithCard">
                                        Pagar con Tarjeta Visa
                                    </button>
                                    <button type="button" class="btn btn-success w-100" id="payWithYape">
                                        Pagar con Yape
                                    </button>
                                </div>
                            </div>

                            <!-- Formulario de Pago con Tarjeta -->
                            <div id="cardPaymentForm" style="display: none;">
                                <h5>Información de la Tarjeta</h5>
                                <div class="mb-3">
                                    <label for="card-element" class="form-label">Tarjeta de Crédito o Débito</label>
                                    <div id="card-element">
                                        <!-- Stripe Element será insertado aquí (sin funcionalidad por ahora) -->
                                        <input type="text" class="form-control" placeholder="Número de Tarjeta" disabled>
                                    </div>
                                    <div id="card-errors" role="alert"></div>
                                </div>
                            </div>

                            <!-- Formulario de Pago con Yape -->
                            <div id="yapePaymentInfo" style="display: none;">
                                <h5>Instrucciones para Pagar con Yape</h5>
                                <p>Escanea el siguiente código QR con tu aplicación Yape para completar el pago.</p>
                                <img src="assets/images/qr_yape_placeholder.png" alt="QR Yape" class="img-fluid" id="yapeQR">
                                <p>Luego, ingresa el número de referencia:</p>
                                <input type="text" class="form-control" id="yapeReference" placeholder="Número de Referencia">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="confirmReservation">Confirmar Reserva</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts al final del body -->
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <!-- Tu script personalizado -->
    <script src="script.js"></script>
</body>
</html>
