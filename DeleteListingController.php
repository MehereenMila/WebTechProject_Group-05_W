<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'donor') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $food_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Nijer listing chara delete kora jabe na, r shudhu Pending status thakle delete hobe
    $checkQuery = "SELECT * FROM donations WHERE id = '$food_id' AND donor_id = '$donor_id'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult && $checkResult->num_rows > 0) {
        $listing = $checkResult->fetch_assoc();

        if ($listing['status'] === 'Pending') {
            $deleteQuery = "DELETE FROM donations WHERE id = '$food_id'";
            if ($conn->query($deleteQuery) === TRUE) {
                $_SESSION['flash_success'] = 'Listing deleted successfully.';
            } else {
                $_SESSION['flash_error'] = 'Something went wrong while deleting the listing.';
            }
        } else {
            $_SESSION['flash_error'] = 'Only Pending listings can be deleted.';
        }
    } else {
        $_SESSION['flash_error'] = 'Listing not found or you do not have permission to delete it.';
    }
} else {
    $_SESSION['flash_error'] = 'No listing specified.';
}

$conn->close();
header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Donor/myListings.php");
exit();
?>