<?php
// backend/api/get_reservations.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener parámetros de la solicitud
    $location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';
    $date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

    // Validar parámetros
    if (empty($location) || empty($date)) {
        echo json_encode(['error' => 'Parámetros "location" y "date" son requeridos.']);
        exit;
    }

    // Preparar la consulta SQL
    $sql = "SELECT r.*, c.name AS court_name, c.price_per_hour FROM reservations r
            JOIN courts c ON r.court_id = c.id
            WHERE c.location = ? AND r.date = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['error' => 'Error en la consulta SQL.']);
        exit;
    }

    $stmt->bind_param("ss", $location, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $reservations = [];

    while ($row = $result->fetch_assoc()) {
        $reservations[] = [
            'id' => $row['id'],
            'court_id' => $row['court_id'],
            'court_name' => $row['court_name'],
            'price_per_hour' => $row['price_per_hour'],
            'date' => $row['date'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'customer_name' => $row['customer_name'],
            'customer_email' => $row['customer_email'],
            'customer_phone' => $row['customer_phone'],
            'total_price' => $row['total_price']
        ];
    }

    echo json_encode($reservations);
} else {
    echo json_encode(['error' => 'Método no permitido.']);
}
?>
