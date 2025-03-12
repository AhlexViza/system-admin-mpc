<?php
session_start();

// Verificar si el usuario tiene rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirigir si no es administrador
    exit();
}

include('db.php'); // Conexión a la base de datos

// Obtener usuarios
$sql_users = "SELECT * FROM usuarios";
$result_users = $conn->query($sql_users);

// Obtener áreas deportivas
$sql_courts = "SELECT * FROM courts";
$result_courts = $conn->query($sql_courts);

// Obtener reservas
$sql_reservations = "SELECT * FROM reservations";
$result_reservations = $conn->query($sql_reservations);
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
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminstyle.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    
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

            <div class="col-md-9 main-content">
                <div id="dashboard" class="section">
                    <h1 class="mb-4">Bienvenido al Panel de Administración</h1>
                    <?php
                        $total_reservas = $conn->query("SELECT COUNT(*) AS total FROM reservations")->fetch_assoc()['total'];
                        $total_usuarios = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
                        $canchas_disponibles = $conn->query("SELECT COUNT(*) AS total FROM courts")->fetch_assoc()['total'];

                        // Obtener reservas para el calendario
                        $reservas_query = $conn->query("SELECT id ,court_id date, start_time, end_time FROM reservations");
                        $reservas = [];
                        if ($reservas_query) {
                            while ($row = $reservas_query->fetch_assoc()) {
                                $reservas[] = $row;
                            }
                        }
                        $conn->close();
                    ?>
                    <div class="container mt-4">
                        <div class="row">
                            <!-- Tarjetas de estadísticas -->
                            <div class="col-md-3">
                                <div class="card text-black mb-3 shadow animate">
                                    <div class="card-custom text-center">
                                        <i class="fas fa-calendar-check text-danger"></i>
                                        <h5 class="card-title mt-2">Total de Reservas</h5>
                                        <p class="card-text fs-3 fw-bold count" data-count="<?php echo $total_reservas; ?>">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-black mb-3 shadow animate">
                                    <div class="card-custom text-center">
                                    <i class="fas fa-users text-primary"></i>
                                        <h5 class="card-title mt-2">Usuarios Registrados</h5>
                                        <p class="card-text fs-3 fw-bold count" data-count="<?php echo $total_usuarios; ?>">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-black mb-3 shadow animate">
                                    <div class="card-custom text-center">
                                        <i class="fas fa-futbol text-success"></i>
                                        <h5 class="card-title mt-2">Canchas Disponibles</h5>
                                        <p class="card-text fs-3 fw-bold count" data-count="<?php echo $canchas_disponibles; ?>">0</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-black  mb-3 shadow animate">
                                    <div class="card-custom text-center">
                                        <i class="fa fa-eye text-primary"></i>
                                        <h5 class="card-title mt-2">Visitas</h5>
                                        <p class="card-text fs-3 fw-bold count" id="contadorVisitas"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de visitas -->
                        <div class="card p-4 mt-4">
                            <h5 class="text-center">Estadísticas de Visitas</h5>
                            <canvas id="graficoVisitas"></canvas>
                        </div>

                        <!-- Barra de búsqueda -->
                        <div class="mt-4">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar...">
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Animación de conteo
                            document.querySelectorAll('.count').forEach(counter => {
                                let target = +counter.getAttribute('data-count');
                                let count = 0;
                                let increment = target / 100;
                                let updateCount = () => {
                                    count += increment;
                                    counter.textContent = count < target ? Math.floor(count) : target;
                                    if (count < target) requestAnimationFrame(updateCount);
                                };
                                updateCount();
                            });

                            // Contador de visitas con CountAPI
                            fetch("https://api.countapi.xyz/hit/reservaareas.ainnovarsystems.com/contador")
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById("contadorVisitas").textContent = data.value;
                                });

                            // Gráfico de visitas
                            const ctx = document.getElementById("graficoVisitas").getContext("2d");
                            new Chart(ctx, {
                                type: "line",
                                data: {
                                    labels: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
                                    datasets: [{
                                        label: "Visitas",
                                        data: [12, 19, 3, 5, 2, 3, 10],
                                        borderColor: "#007bff",
                                        backgroundColor: "rgba(0,123,255,0.2)",
                                        fill: true
                                    }]
                                }
                            });

                            // Búsqueda en tiempo real
                            document.getElementById("searchInput").addEventListener("input", function () {
                                let value = this.value.toLowerCase();
                                document.querySelectorAll(".card").forEach(card => {
                                    card.style.display = card.textContent.toLowerCase().includes(value) ? "block" : "none";
                                });
                            });

                            // Notificación en tiempo real
                            setTimeout(() => {
                                let notificacion = document.getElementById("notificacion");
                                notificacion.textContent = "Nuevo usuario registrado!";
                                notificacion.style.display = "block";
                                setTimeout(() => notificacion.style.display = "none", 3000);
                            }, 5000);

                            // Modo Oscuro
                            document.getElementById("toggleDarkMode").addEventListener("click", function () {
                                document.body.classList.toggle("bg-dark");
                                document.body.classList.toggle("text-white");
                            });
                        });
                    </script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                </div>

                <div id="users" class="section" style="display:none;">
                    <h2 class="text-center">Usuarios Registrados</h2>
                    <form action="">
                        <input type="search" class="form-control mb-3" placeholder="buscar usuario..">
                    </form>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre de Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_users->num_rows > 0) {
                                while ($row = $result_users->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['username']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['rol']}</td>
                                        <td>
                                            <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editUserModal' data-id='{$row['id']}' data-username='{$row['username']}' data-email='{$row['email']}'>Editar</button>
                                            <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteUserModal' data-id='{$row['id']}'>Eliminar</button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No hay usuarios registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div id="courts" class="section" style="display:none;">
                    <h2 class="text-center">Áreas Deportivas</h2>
                    <form action="">
                        <input type="search" class="form-control mb-3" placeholder="buscar areas deportivas..">
                    </form>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Capacidad</th>
                                <th>Precio por hora</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_courts->num_rows > 0) {
                                while ($row = $result_courts->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['name']}</td>
                                        <td>{$row['location']}</td>
                                        <td>{$row['capacity']}</td>
                                        <td>\${$row['price_per_hour']}</td>
                                        <td>
                                            <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editCourtModal' data-id='{$row['id']}' data-name='{$row['name']}' data-location='{$row['location']}' data-capacity='{$row['capacity']}' data-price='{$row['price_per_hour']}'>Editar</button>
                                            <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteCourtModal' data-id='{$row['id']}'>Eliminar</button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay áreas deportivas registradas.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div id="reservations" class="section" style="display:none;">
                    <h2 class="text-center">Reservas Realizadas</h2>
                    <form action="">
                        <input type="search" class="form-control mb-3" placeholder="buscar reservas..">
                    </form>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Deporte</th>
                                <th>Fecha</th>
                                <th>Hora de Inicio</th>
                                <th>Hora de Fin</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_reservations->num_rows > 0) {
                                while ($row = $result_reservations->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['sport']}</td>
                                        <td>{$row['date']}</td>
                                        <td>{$row['start_time']}</td>
                                        <td>{$row['end_time']}</td>
                                        <td>{$row['customer_name']}</td>
                                        <td>
                                            <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteReservationModal' data-id='{$row['id']}'>Eliminar</button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No hay reservas realizadas.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales de Confirmación -->

    <!-- Modal de Edición de Usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario de Edición de Usuario -->
                    <form action="editar_usuario.php" method="POST">
                        <input type="hidden" id="editUserId" name="id">
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación de Usuario -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Eliminar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este usuario?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="deleteUserLink" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación de Área Deportiva -->
    <div class="modal fade" id="deleteCourtModal" tabindex="-1" aria-labelledby="deleteCourtModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCourtModalLabel">Eliminar Área Deportiva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta área deportiva?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="deleteCourtLink" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Eliminación de Reserva -->
    <div class="modal fade" id="deleteReservationModal" tabindex="-1" aria-labelledby="deleteReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteReservationModalLabel">Eliminar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta reserva?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="deleteReservationLink" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function showSection(sectionId) {
        // Ocultar todas las secciones
        var sections = document.querySelectorAll('.section');
        sections.forEach(function(section) {
            section.style.display = 'none';
        });

        // Mostrar la sección seleccionada
        document.getElementById(sectionId).style.display = 'block';

        // Cambiar el estado activo de los botones
        var buttons = document.querySelectorAll('.sidebar .bot, .sidebar a');
        buttons.forEach(function(button) {
            button.classList.remove('active');
        });

        // Establecer el estado activo en el botón o enlace clicado
        var selectedElement = document.getElementById('btn_' + sectionId) || document.querySelector('a[href="#' + sectionId + '"]');
        selectedElement.classList.add('active');
    }

        // Set data for the modals dynamically
        document.addEventListener('DOMContentLoaded', function() {
            var editUserModal = document.getElementById('editUserModal');
            editUserModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var userId = button.getAttribute('data-id');
                var username = button.getAttribute('data-username');
                var email = button.getAttribute('data-email');
                var modal = editUserModal.querySelector('form');
                modal.querySelector('#editUserId').value = userId;
                modal.querySelector('#editUsername').value = username;
                modal.querySelector('#editEmail').value = email;
            });

            var deleteUserModal = document.getElementById('deleteUserModal');
            deleteUserModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var userId = button.getAttribute('data-id');
                var deleteLink = deleteUserModal.querySelector('#deleteUserLink');
                deleteLink.href = 'eliminar_usuario.php?id=' + userId;
            });

            var deleteCourtModal = document.getElementById('deleteCourtModal');
            deleteCourtModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var courtId = button.getAttribute('data-id');
                var deleteLink = deleteCourtModal.querySelector('#deleteCourtLink');
                deleteLink.href = 'eliminar_area.php?id=' + courtId;
            });

            var deleteReservationModal = document.getElementById('deleteReservationModal');
            deleteReservationModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var reservationId = button.getAttribute('data-id');
                var deleteLink = deleteReservationModal.querySelector('#deleteReservationLink');
                deleteLink.href = 'eliminar_reserva.php?id=' + reservationId;
            });
        });
    </script>
</body>
</html>
