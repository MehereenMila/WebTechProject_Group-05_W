<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'volunteer') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donation_id = mysqli_real_escape_string($conn, $_POST['donation_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $delivery_location = isset($_POST['delivery_location']) ? mysqli_real_escape_string($conn, trim($_POST['delivery_location'])) : '';

    // Jodi status 'Delivered' e change hoy, delivered_at time-o save kora hocche
    if ($new_status === 'Delivered') {
        $sql = "UPDATE donations SET status = '$new_status', delivered_at = NOW(), delivery_location = '$delivery_location' WHERE id = '$donation_id'";
    } else {
        $sql = "UPDATE donations SET status = '$new_status', delivery_location = '$delivery_location' WHERE id = '$donation_id'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Delivery status updated successfully!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/deliveryTasks.php';</script>";
    } else {
        echo "<script>alert('Error updating status!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/deliveryTasks.php';</script>";
    }
}
$conn->close();
?>