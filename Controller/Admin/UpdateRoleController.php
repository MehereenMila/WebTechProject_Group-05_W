<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Check if user is Admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

    // Admin nijei nijer role jeno galti te change na kore fele
    if ($user_id == $_SESSION['user_id']) {
        echo "<script>alert('You cannot change your own role!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php';</script>";
        exit();
    }

    $sql = "UPDATE users SET role = '$new_role' WHERE id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User role updated successfully!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php';</script>";
    } else {
        echo "<script>alert('Error updating role!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php';</script>";
    }
}
$conn->close();
?>
