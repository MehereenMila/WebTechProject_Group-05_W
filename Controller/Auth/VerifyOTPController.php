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
    $entered_otp = trim($_POST['otp_code']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($entered_otp)) {
        $_SESSION['flash_error'] = 'Please enter the verification OTP code!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
        exit();
    }

    $sql = "SELECT reset_otp FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user['reset_otp'] === $entered_otp) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) < 6) {
                $_SESSION['flash_error'] = 'New password must be at least 6 characters!';
                header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
                exit();
            }
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET password = '$hashed_password', reset_otp = NULL WHERE id = '$user_id'";
            if ($conn->query($updateSql) === TRUE) {
                $_SESSION['flash_success'] = 'High-Security OTP Verified! Password updated successfully.';
            } else {
                $_SESSION['flash_error'] = 'Error updating password in database!';
            }
        } else {
            $_SESSION['flash_error'] = 'New passwords do not match!';
        }
    } else {
        $_SESSION['flash_error'] = 'Invalid OTP code! Security check failed.';
    }
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
    exit();
}
$conn->close();
?>