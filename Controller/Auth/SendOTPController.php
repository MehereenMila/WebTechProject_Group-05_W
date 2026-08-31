<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$method = isset($_GET['method']) ? $_GET['method'] : 'email'; // 'email' or 'phone'
$otp = rand(100000, 999999);

$userSql = "SELECT email, phone FROM users WHERE id = '$user_id'";
$userResult = $conn->query($userSql);
$userRow = $userResult->fetch_assoc();

if ($method === 'phone') {
    $destination = isset($userRow['phone']) ? $userRow['phone'] : 'your phone';
    $channelLabel = 'SMS';
} else {
    $destination = isset($userRow['email']) ? $userRow['email'] : 'your email';
    $channelLabel = 'Email';
}

$sql = "UPDATE users SET reset_otp = '$otp' WHERE id = '$user_id'";
if ($conn->query($sql) === TRUE) {
    // Localhost simulation (Live system-e eta PHPMailer / Twilio SMS API diye pathano hobe)
    $_SESSION['flash_success'] = "$channelLabel OTP sent to $destination! (Simulation) Your code is: $otp";
} else {
    $_SESSION['flash_error'] = 'Error generating OTP!';
}
header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security");
exit();
$conn->close();
?>