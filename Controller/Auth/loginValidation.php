<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 1. Password Verification kora hocche
        if (password_verify($password, $user['password'])) {
            
            // 2. Session-e data save kora hocche (Jate dashboard jate pare)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name']; 
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // 3. Role onujayi alada dashboard-e pathano
            $role = strtolower($user['role']); 

            if ($role === 'admin') {
                header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Admin/dashboard.php");
            } elseif ($role === 'donor') {
                header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Donor/dashboard.php");
            } elseif ($role === 'volunteer') {
                header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/dashboard.php");
            } elseif ($role === 'manager') {
                header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Manager/dashboard.php");
            } else {
                header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
            }
            exit();
            
        } else {
            // Password vul hole error login page e flash message shoho ferot pathano
            $_SESSION['flash_error'] = 'Invalid password! Please try again.';
            header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
            exit();
        }
    } else {
        // Email vul hole error
        $_SESSION['flash_error'] = 'No account found with this email!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
        exit();
    }
}
$conn->close();
?>