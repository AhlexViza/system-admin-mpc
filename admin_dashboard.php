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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-shield"></i> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="list-group">
                    <button class="list-group-item list-group-item-action active" id="btn_dashboard" onclick="showSection('dashboard')">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </button>
                    <button class="list-group-item list-group-item-action" id="btn_users" onclick="showSection('users')">
                        <i class="fas fa-users"></i> Usuarios
                    </button>
                    <button class="list-group-item list-group-item-action" id="btn_courts" onclick="showSection('courts')">
                        <i class="fas fa-basketball-ball"></i> Áreas Deportivas
                    </button>
                    <button class="list-group-item list-group-item-action" id="btn_reservations" onclick="showSection('reservations')">
                        <i class="fas fa-calendar-check"></i> Reservas
                    </button>
                    <a href="admin_register.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-plus"></i> Registrar Administradores
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-cogs"></i> Configuración
                    </a>
                </div>
            </div>

            <div class="col-md-8">
                <div id="dashboard" class="section">
                    <h1 class="mb-4">Bienvenido al Panel de Administración</h1>
                    <p>Desde aquí puedes gestionar las configuraciones del sistema y registrar nuevos administradores.</p>
                </div>

                <div id="users" class="section" style="display:none;">
                    <h2>Usuarios Registrados</h2>
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
                    <h2>Áreas Deportivas</h2>
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
                    <h2>Reservas Realizadas</h2>
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
            var buttons = document.querySelectorAll('.list-group-item');
            buttons.forEach(function(button) {
                button.classList.remove('active');
            });

            document.getElementById('btn_' + sectionId).classList.add('active');
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
