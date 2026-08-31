<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : '';

    if ($password !== $confirm_password) {
        $_SESSION['flash_error'] = 'Passwords do not match!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/registration.php");
        exit();
    }

    $check_email = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        $_SESSION['flash_error'] = 'Email already exists! Please Sign In instead.';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/index.php?page=login");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, phone, dob, gender, password, role) 
            VALUES ('$name', '$email', '$phone', '$dob', '$gender', '$hashed_password', '$role')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['flash_success'] = 'Registration successful! Please sign in.';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
        exit();
    } else {
        $_SESSION['flash_error'] = 'Database Error: ' . $conn->error;
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/registration.php");
        exit();
    }

    if ($conn->query($sql) === TRUE) 
        {
            $_SESSION['flash_success'] = 'Registration successful! Please sign in.';
            header("Location: /Web_Technology%20Summer%2025-26/FoodShare/index.php?page=login");
            exit();
        } 
    else {
        $_SESSION['flash_error'] = 'Database Error: ' . $conn->error;
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/index.php?page=register");
        exit();
    }
}
$conn->close();
?>