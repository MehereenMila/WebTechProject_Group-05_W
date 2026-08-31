<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $otp = rand(100000, 999999);

    // Email diye user khoja hocche
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        $_SESSION['flash_error'] = 'No account found with this email!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php");
        exit();
    }

    $updateSql = "UPDATE users SET reset_otp = '$otp' WHERE email = '$email'";
    if ($conn->query($updateSql) === TRUE) {
        // Localhost simulation (Live system-e eta PHPMailer diye email-e pathano hobe)
        $_SESSION['flash_success'] = "OTP sent to $email! (Simulation) Your code is: $otp";
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php?email=" . urlencode($email));
        exit();
    } else {
        $_SESSION['flash_error'] = 'Error generating OTP!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php");
        exit();
    }
}
$conn->close();
?>