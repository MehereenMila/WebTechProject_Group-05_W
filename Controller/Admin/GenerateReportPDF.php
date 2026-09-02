<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$database = new DatabaseConnection();
$conn = $database->openConnection();

$sql = "SELECT d.food_type, d.quantity, d.status, d.created_at, d.delivered_at,
               d.location, d.delivery_location,
               u1.name as donor_name, u2.name as volunteer_name
        FROM donations d
        JOIN users u1 ON d.donor_id = u1.id
        LEFT JOIN users u2 ON d.volunteer_id = u2.id
        WHERE d.created_at BETWEEN '$startDate' AND '$endDate'
        ORDER BY d.created_at ASC";
$result = $conn->query($sql);

$rows = [];
$totalCount = 0;
$deliveredCount = 0;
$pendingCount = 0;
$activeCount = 0;

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
        $totalCount++;
        if ($row['status'] === 'Delivered') $deliveredCount++;
        elseif ($row['status'] === 'Pending') $pendingCount++;
        else $activeCount++;
    }
}

$conn->close();
?>
