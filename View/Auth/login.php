<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash_error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);
?>







<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
 </head>
  <body class="auth-body">
    <div class="auth-container">
        <!-- Left Panel -->
        <div class="auth-left">
            <ul class="auth-nav">
                <li><a href="#">About</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Help</a></li>
            </ul>
            <div class="auth-logo-box">
                <!-- Router er maddhome Home/Landing page e phire jawar link -->
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/index.php?page=home" class="logo">Food<span>Share</span></a>
                <div class="logo-underline"></div>
            </div>
            <div class="auth-switch">
                <p>Don't have any account?</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/index.php?page=register">Sign Up</a>
            </div>
            <div class="auth-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </div>
        </div>
        
        <!-- Right Panel (Form) -->
        <div class="auth-right">
            <div class="form-wrapper">
                <h2>Welcome Back!</h2>
                <p class="sub-heading">Sign in to your account</p>

             <?php if (!empty($flash_error)): ?>
             <div class="flash-message flash-error" id="flashMsg"><span class="flash-icon">!</span><span><?php echo htmlspecialchars($flash_error); ?></span><button class="flash-close" onclick="dismissFlash()">×</button></div>
             <?php endif; ?>
             <?php if (!empty($flash_success)): ?>
             <div class="flash-message flash-success" id="flashMsg"><span class="flash-icon">✓</span><span><?php echo htmlspecialchars($flash_success); ?></span><button class="flash-close" onclick="dismissFlash()">×</button></div>
              <?php endif; ?>
                
                <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/loginValidation.php" method="POST">
                    <div class="input-group">
                        <label>Email address:</label>
                        <input type="email" name="email" placeholder="name@example.com" required>
                    </div>
                    <div class="input-group">
                        <label>Password:</label>
                        <input type="password" name="password" placeholder="********" minlength="8" required>
                    </div>
                    <div class="forgot-password">
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/forgotPassword.php">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn-submit">SIGN IN</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function dismissFlash() {
            const el = document.getElementById('flashMsg');
            if (!el) return;
            el.classList.add('flash-hide');
            setTimeout(() => el.remove(), 300);
        }
        setTimeout(dismissFlash, 4000);
    </script>


  </body>
</html>