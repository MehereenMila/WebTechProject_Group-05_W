<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security Check: Admin only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Shudhu active (Assigned / In Transit) delivery gulo live tracking e dekhano hocche
$sql = "SELECT d.id, d.food_type, d.location, d.delivery_location, d.status,
               u1.name as donor_name,
               u2.name as volunteer_name, u2.phone as volunteer_phone
        FROM donations d
        JOIN users u1 ON d.donor_id = u1.id
        LEFT JOIN users u2 ON d.volunteer_id = u2.id
        WHERE d.status IN ('Assigned', 'In Transit')
        ORDER BY d.id DESC";

$result = $conn->query($sql);
$deliveries = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $deliveries[] = [
            'id' => $row['id'],
            'food_type' => $row['food_type'],
            'donor_name' => $row['donor_name'],
            'volunteer_name' => $row['volunteer_name'] ? $row['volunteer_name'] : 'Not Assigned',
            'volunteer_phone' => $row['volunteer_phone'] ? $row['volunteer_phone'] : '-',
            'pickup_location' => $row['location'],
            'delivery_location' => !empty($row['delivery_location']) ? $row['delivery_location'] : 'Not specified yet',
            'status' => $row['status'],
        ];
    }
}

echo json_encode(['deliveries' => $deliveries, 'server_time' => date('h:i:s A')]);
$conn->close();
?>