<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security: Shudhu Donor ekhane feedback dite parbe
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'donor') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = $_SESSION['user_id'];
    $donation_id = mysqli_real_escape_string($conn, $_POST['donation_id']);
    $feedback = mysqli_real_escape_string($conn, trim($_POST['feedback']));

    // Security: donation_id ta ei donor er nijer kina, ar delivered kina check kora hocche
    $checkSql = "SELECT id FROM donations WHERE id = '$donation_id' AND donor_id = '$donor_id' AND status = 'Delivered'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows === 1 && $feedback !== '') {
        $sql = "UPDATE donations SET feedback = '$feedback' WHERE id = '$donation_id'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thank you for your feedback!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php';</script>";
        } else {
            echo "<script>alert('Error saving feedback!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php';</script>";
        } 
    } else {
        echo "<script>alert('Invalid request.'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php';</script>";
    }
}
$conn->close();
?>