<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($old_password, $user['password'])) {
        $_SESSION['flash_error'] = 'Current password is incorrect!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['flash_error'] = 'New passwords do not match!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
        exit();
    }

    if (strlen($new_password) < 6) {
        $_SESSION['flash_error'] = 'New password must be at least 6 characters!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $updateSql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";

    if ($conn->query($updateSql) === TRUE) {
        $_SESSION['flash_success'] = 'Password updated successfully!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
        exit();
    } else {
        $_SESSION['flash_error'] = 'Database Error: ' . $conn->error;
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
        exit();
    }
}
$conn->close();
?>