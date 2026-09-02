<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $food_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $sql = "DELETE FROM donations WHERE id = '$food_id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Food listing deleted successfully by Admin.'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php';</script>";
    } else {
        echo "<script>alert('Error deleting listing!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php';</script>";
    }
} else {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php");
}
$conn->close();
?>