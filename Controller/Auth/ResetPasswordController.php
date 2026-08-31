<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $entered_otp = trim($_POST['otp_code']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($email)) {
        $_SESSION['flash_error'] = 'Session expired, please enter your email again!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php");
        exit();
    }

    $sql = "SELECT id, reset_otp FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['flash_error'] = 'No account found with this email!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php");
        exit();
    }

    if ($user['reset_otp'] !== $entered_otp) {
        $_SESSION['flash_error'] = 'Invalid OTP code!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php?email=" . urlencode($email));
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['flash_error'] = 'Passwords do not match!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php?email=" . urlencode($email));
        exit();
    }

    if (strlen($new_password) < 8) {
        $_SESSION['flash_error'] = 'Password must be at least 8 characters!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php?email=" . urlencode($email));
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $updateSql = "UPDATE users SET password = '$hashed_password', reset_otp = NULL WHERE email = '$email'";

    if ($conn->query($updateSql) === TRUE) {
        $_SESSION['flash_success'] = 'Password reset successful! Please sign in with your new password.';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
        exit();
    } else {
        $_SESSION['flash_error'] = 'Database Error: ' . $conn->error;
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php?email=" . urlencode($email));
        exit();
    }
}
$conn->close();
?>