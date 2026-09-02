<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $profile_pic_sql = "";
    $file_name = ""; // Initialize file name variable

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $target_dir = __DIR__ . '/../../uploads/';
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic_sql = ", profile_pic = '$file_name'";
        } else {
            $_SESSION['flash_error'] = 'Upload Error: Permission denied on uploads folder!';
            header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=profile");
            exit();
        }
    }

    $updateSql = "UPDATE users SET name = '$name', email = '$email' $profile_pic_sql WHERE id = '$user_id'";
    
    if ($conn->query($updateSql) === TRUE) {
        $_SESSION['name'] = $name;
        
        // Jodi notun picture upload kora hoy, tahole session-eo update kore dibo
        if (!empty($file_name)) {
            $_SESSION['profile_pic'] = $file_name;
        }

        $_SESSION['flash_success'] = 'Profile updated successfully!';
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=profile");
        exit();
    } else {
        $_SESSION['flash_error'] = 'Database Error: ' . $conn->error;
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=profile");
        exit();
    }
}
$conn->close();
?>