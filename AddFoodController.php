<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'donor') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = $_SESSION['user_id'];
    
    $food_type = mysqli_real_escape_string($conn, $_POST['food_type']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $pickup_day = mysqli_real_escape_string($conn, $_POST['pickup_day']);
    $cooking_date = mysqli_real_escape_string($conn, $_POST['cooking_date']);
    $cooking_time = mysqli_real_escape_string($conn, $_POST['cooking_time']);
    $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
    $expiry_time = mysqli_real_escape_string($conn, $_POST['expiry_time']);
    $details = isset($_POST['details']) ? mysqli_real_escape_string($conn, $_POST['details']) : '';
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    // Image Upload Handling
    $imageName = "";
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] === 0) {
        $imgTmpPath = $_FILES['food_image']['tmp_name'];
        $imgOriginalName = $_FILES['food_image']['name'];
        $imageName = time() . "_" . $imgOriginalName; // Unique name to avoid duplicate issues
        
        // Ensure uploads directory exists inside assets or View
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        move_uploaded_file($imgTmpPath, $uploadDir . $imageName);
    }

    $food_category = 'General';
    $status = 'Pending'; 

    $sql = "INSERT INTO donations (donor_id, food_category, food_type, quantity, pickup_day, cooking_date, cooking_time, expiry_date, expiry_time, details, food_image, location, status) 
            VALUES ('$donor_id', '$food_category', '$food_type', '$quantity', '$pickup_day', '$cooking_date', '$cooking_time', '$expiry_date', '$expiry_time', '$details', '$imageName', '$location', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Food listing created successfully with location and image!'); window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/createListing.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
    }
}
$conn->close();
?>