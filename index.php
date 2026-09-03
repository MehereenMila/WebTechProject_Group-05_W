<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    session_start();

 $page = isset($_GET['page']) ? $_GET['page'] : 'home';

    switch($page) {
        case 'home':
            include 'View/Landing/landing.php';
            break;
        case 'login':
            include 'View/Auth/login.php';
            break;
        case 'register':
            include 'View/Auth/registration.php';
            break;
        case 'dashboard':
            include 'View/Admin/dashboard.php';
            break;
        default:
            echo "404 Page Not Found";
            break; 
    }
 ?>