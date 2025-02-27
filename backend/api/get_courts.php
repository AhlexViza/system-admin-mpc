<?php
// backend/api/get_courts.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM courts";
    $result = $conn->query($sql);

    $courts = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courts[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'location' => $row['location'],
                'capacity' => $row['capacity'],
                'phone' => $row['phone'],
                'image' => $row['image'],
                'price_per_hour' => floatval($row['price_per_hour']) // Asegura que sea un número
            ];
        }
    }

    echo json_encode($courts);
} else {
    echo json_encode(['error' => 'Método no permitido.']);
}
?>
