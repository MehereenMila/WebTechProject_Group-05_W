<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'manager') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donation_id = mysqli_real_escape_string($conn, $_POST['donation_id']);
    $volunteer_id = mysqli_real_escape_string($conn, $_POST['volunteer_id']);

    // Status change kore 'Assigned' kore deya hocche ar volunteer_id update kora hocche
    $sql = "UPDATE donations SET volunteer_id = '$volunteer_id', status = 'Assigned' WHERE id = '$donation_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Volunteer assigned successfully!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php';</script>";
    } else {
        echo "<script>alert('Error assigning volunteer!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php';</script>";
    }
}
$conn->close();
?>