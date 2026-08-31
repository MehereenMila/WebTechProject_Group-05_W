<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash_error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - FoodShare</title>
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/index.php?page=home" class="logo">Food<span>Share</span></a>
                <div class="logo-underline"></div>
            </div>
            <div class="auth-switch">
                <p>You have an account?</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/index.php?page=login">Sign In</a> <!-- Sign in e jawar link -->
            </div>
            <div class="auth-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </div>
        </div>
        
        <!-- Right Panel (Form) -->
        <div class="auth-right">
            <div class="form-wrapper">
                <h2>Create New Account !</h2>
                <p class="sub-heading">Join the FoodShare community</p>

                <?php if (!empty($flash_error)): ?>
                    <div class="flash-message flash-error">⚠️ <?php echo htmlspecialchars($flash_error); ?></div>
                <?php endif; ?>
                
               <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/RegistrationValidation.php" method="POST">
                   <div class="input-group row-input">
                        <label>Name :</label>
                        <input type="text" name="name" placeholder="Enter your name"  required>
                    </div>
                    <div class="input-group row-input">
                        <label>Email :</label>
                        <input type="email" name="email" placeholder="abc@gmail.com" required>
                    </div>
                    <div class="input-group row-input">
                        <label>Phone :</label>
                        <input type="text" name="phone" placeholder="11 digits" pattern="[0-9]{11}" required>
                    </div>
                    <div class="input-group row-input">
                        <label>Date Of Birth :</label>
                        <input type="date" name="dob" required>
                    </div>
                    <div class="input-group row-input">
                        <label>Gender :</label>
                        <input type="text" name="gender" required>
                    </div>
                    <div class="input-group row-input">
                        <label>New Password :</label>
                        <input type="password" name="password" placeholder="********" minlength="8" required>
                    </div>
                    <div class="input-group row-input">
                        <label>Confirm Password :</label>
                        <input type="password" name="confirm_password" placeholder="********"  minlength="8" required>
                    </div>
                    
                    <div class="role-group">
                        <label>Select your role :</label>
                        <div class="role-options">
                            <label class="role-btn"><input type="radio" name="role" value="donor" required> Donor</label>
                            <label class="role-btn"><input type="radio" name="role" value="volunteer"> Volunteer</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit">SIGN UP</button>
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
