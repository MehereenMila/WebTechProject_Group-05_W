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

$donation_id = isset($_GET['donation_id']) ? mysqli_real_escape_string($conn, $_GET['donation_id']) : '';

if ($donation_id === '') {
    echo json_encode(['error' => 'Missing donation_id']);
    exit();
}

// Donation row theke volunteer_id + feedback ana hocche, tarpor users table theke volunteer details
$sql = "SELECT u.id, u.name, u.phone, u.profile_pic, u.dob, u.gender, d.feedback, d.status, d.food_type
        FROM donations d
        LEFT JOIN users u ON d.volunteer_id = u.id
        WHERE d.id = '$donation_id'";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(['error' => 'Not found']);
    exit();
}

$row = $result->fetch_assoc();

if (empty($row['id'])) {
    echo json_encode(['error' => 'No volunteer assigned to this donation yet']);
    exit();
}

// Age calculate kora hocche dob theke
$age = null;
if (!empty($row['dob'])) {
    $dobDate = new DateTime($row['dob']);
    $today = new DateTime('today');
    $age = $today->diff($dobDate)->y;
}

echo json_encode([
    'id' => $row['id'],
    'name' => $row['name'],
    'phone' => $row['phone'],
    'profile_pic' => $row['profile_pic'],
    'age' => $age,
    'gender' => $row['gender'],
    'feedback' => !empty($row['feedback']) ? $row['feedback'] : null,
    'food_type' => $row['food_type'],
    'status' => $row['status'],
]);
$conn->close();
?>