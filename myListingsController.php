<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'volunteer') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$volunteerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Volunteer';
$volunteerInitial = strtoupper(substr($volunteerName, 0, 1));

$db = new DatabaseConnection();
$conn = $db->openConnection();

$userQuery = "SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
$userResult = $conn->query($userQuery);
$userData = $userResult ? $userResult->fetch_assoc() : [];
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

$sql = "SELECT id, food_name, food_type, quantity, pickup_address, expiry_time, status FROM food_donations ORDER BY id DESC";
$result = $conn->query($sql);

$listings = [];
$currentTime = date("Y-m-d H:i:s");

if ($result) {
    while($row = $result->fetch_assoc()) 
    {

        if (strtotime($row["expiry_time"]) < strtotime($currentTime)) {
            $row["status"] = "Expired";
        }
        $listings[] = $row;
    }
}

require_once __DIR__ . '/../../View/Volunteer/myListings.php';
?>