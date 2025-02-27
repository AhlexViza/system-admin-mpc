<?php
// backend/api/create_reservation.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos JSON de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar que se haya recibido JSON correctamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Datos JSON inválidos.']);
        exit;
    }

    // Validar los campos requeridos
    $required_fields = ['court_id', 'date', 'start_time', 'end_time', 'customer_name', 'customer_email', 'customer_phone'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            echo json_encode(['error' => "El campo '$field' es requerido."]);
            exit;
        }
    }

    // Escapar caracteres especiales para evitar inyecciones SQL
    $court_id = $conn->real_escape_string($data['court_id']);
    $date = $conn->real_escape_string($data['date']);
    $start_time = $conn->real_escape_string($data['start_time']);
    $end_time = $conn->real_escape_string($data['end_time']);
    $customer_name = $conn->real_escape_string($data['customer_name']);
    $customer_email = $conn->real_escape_string($data['customer_email']);
    $customer_phone = $conn->real_escape_string($data['customer_phone']);

    // Verificar que start_time < end_time
    if (strtotime($start_time) >= strtotime($end_time)) {
        echo json_encode(['error' => 'La hora de inicio debe ser anterior a la hora de fin.']);
        exit;
    }

    // Verificar que la fecha no sea pasada
    $today = date('Y-m-d');
    if ($date < $today) {
        echo json_encode(['error' => 'No se pueden realizar reservas en fechas pasadas.']);
        exit;
    }

    // Obtener el precio por hora de la cancha
    $price_sql = "SELECT price_per_hour FROM courts WHERE id = ?";
    $price_stmt = $conn->prepare($price_sql);
    if ($price_stmt === false) {
        echo json_encode(['error' => 'Error en la preparación de la consulta de precio.']);
        exit;
    }

    $price_stmt->bind_param("i", $court_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result();

    if ($price_result->num_rows === 0) {
        echo json_encode(['error' => 'Cancha no encontrada.']);
        exit;
    }

    $price_row = $price_result->fetch_assoc();
    $price_per_hour = floatval($price_row['price_per_hour']);

    // Calcular la duración de la reserva en horas
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $interval = $start->diff($end);
    $hours = $interval->h + ($interval->i > 0 ? 1 : 0); // Redondear hacia arriba si hay minutos
    $total_price = $price_per_hour * $hours;

    // Verificar si el slot está disponible
    $sql = "SELECT * FROM reservations WHERE court_id = ? AND date = ? AND (
                (start_time < ? AND end_time > ?) OR
                (start_time >= ? AND start_time < ?) OR
                (end_time > ? AND end_time <= ?)
            )";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['error' => 'Error en la preparación de la consulta SQL.']);
        exit;
    }

    $stmt->bind_param("ssssssss", $court_id, $date, $end_time, $start_time, $start_time, $end_time, $start_time, $end_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'El horario ya está reservado.']);
        exit;
    }

    // Insertar la reserva con el precio total
    $insert_sql = "INSERT INTO reservations (court_id, date, start_time, end_time, customer_name, customer_email, customer_phone, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    if ($insert_stmt === false) {
        echo json_encode(['error' => 'Error en la preparación de la inserción SQL.']);
        exit;
    }
    $insert_stmt->bind_param("issssssi", $court_id, $date, $start_time, $end_time, $customer_name, $customer_email, $customer_phone, $total_price);

    if ($insert_stmt->execute()) {
        // Obtener el ID de la reserva recién creada
        $reservation_id = $insert_stmt->insert_id;

        // (Opcional) Obtener detalles completos de la reserva para devolver al frontend
        $detail_sql = "SELECT * FROM reservations WHERE id = ?";
        $detail_stmt = $conn->prepare($detail_sql);
        if ($detail_stmt === false) {
            echo json_encode(['error' => 'Error en la preparación de la consulta de detalles.']);
            exit;
        }
        $detail_stmt->bind_param("i", $reservation_id);
        $detail_stmt->execute();
        $detail_result = $detail_stmt->get_result();
        $reservation = $detail_result->fetch_assoc();

        echo json_encode(['success' => 'Reserva creada exitosamente.', 'total_price' => number_format($total_price, 2), 'reservation' => $reservation]);
    } else {
        echo json_encode(['error' => 'Error al crear la reserva.']);
    }
} else {
    echo json_encode(['error' => 'Método no permitido.']);
}
?>
