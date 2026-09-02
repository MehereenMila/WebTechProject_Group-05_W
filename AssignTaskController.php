<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Shudhu manager ei action nite parbe
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donation_id = mysqli_real_escape_string($conn, $_POST['donation_id']);
    $volunteer_id = mysqli_real_escape_string($conn, $_POST['volunteer_id']);

    // Donation er status Pending theke Assigned kora hocche ar Volunteer ke jukto kora hocche
    $sql = "UPDATE donations SET status = 'Assigned', volunteer_id = '$volunteer_id' WHERE id = '$donation_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Task Assigned Successfully!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/availableFood.php';</script>";
    } else {
        echo "<script>alert('Error assigning task: " . $conn->error . "'); window.history.back();</script>";
    }
}
$conn->close();
?>