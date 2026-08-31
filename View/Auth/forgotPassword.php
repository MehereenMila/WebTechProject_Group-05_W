<?php
session_start();
$prefill_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
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
    <title>Forgot Password - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .fp-body { background: #f0f4eb; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .fp-card { background: white; max-width: 480px; width: 100%; margin: 40px 20px; padding: 45px; border-radius: 16px; box-shadow: 0 10px 35px rgba(0,0,0,0.08); }
        .fp-logo { text-align: center; font-family: 'Times New Roman', Times, serif; font-size: 32px; color: #1a4314; margin-bottom: 5px; }
        .fp-logo span { color: #2e8f46; }
        .fp-title { text-align: center; font-size: 22px; font-weight: bold; color: #2c3e50; margin: 15px 0 5px; }
        .fp-sub { text-align: center; color: #888; font-size: 13px; margin-bottom: 30px; }

        .fp-group { margin-bottom: 18px; }
        .fp-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #34495e; font-size: 13px; }
        .fp-group input { width: 100%; padding: 11px 14px; border: 1px solid #dcdde1; border-radius: 8px; font-size: 14px; background: #fafafa; }
        .fp-group input:focus { border-color: #2e8f46; outline: none; background: #fff; box-shadow: 0 0 0 3px rgba(46,143,70,0.1); }

        .fp-otp-row { display: flex; gap: 10px; align-items: flex-end; }
        .fp-otp-row .fp-group { flex-grow: 1; margin-bottom: 0; }
        .btn-otp { background: #f39c12; color: white; border: none; padding: 11px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; height: 43px; white-space: nowrap; }
        .btn-otp:hover { background: #d68910; }

        .fp-submit { width: 100%; background: #2e8f46; color: white; border: none; padding: 13px; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .fp-submit:hover { background: #206a37; }

        .fp-back { text-align: center; margin-top: 22px; font-size: 13px; }
        .fp-back a { color: #2e8f46; text-decoration: none; font-weight: 600; }

        .fp-step2 { border-top: 1px solid #eee; margin-top: 22px; padding-top: 22px; }
        .fp-info { background: #eaf3ff; border-left: 4px solid #2a75d3; color: #1a4d8f; padding: 10px 14px; border-radius: 0 8px 8px 0; font-size: 12px; margin-bottom: 18px; }
    </style>
</head>
<body class="fp-body">

    <div class="fp-card">
        <div class="fp-logo">Food<span>Share</span></div>
        <div class="fp-title">Forgot Password</div>
        <div class="fp-sub">Enter your email to reset your password via OTP</div>

                <?php if (!empty($flash_error)): ?>
            <div class="flash-message flash-error" id="flashMsg"><span class="flash-icon">!</span><span><?php echo htmlspecialchars($flash_error); ?></span><button class="flash-close" onclick="dismissFlash()">×</button></div>
        <?php endif; ?>
        <?php if (!empty($flash_success)): ?>
            <div class="flash-message flash-success" id="flashMsg"><span class="flash-icon">✓</span><span><?php echo htmlspecialchars($flash_success); ?></span><button class="flash-close" onclick="dismissFlash()">×</button></div>
        <?php endif; ?>

        <!-- STEP 1: Send OTP to email -->
        <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/SendForgotOTPController.php" method="POST">
            <div class="fp-otp-row">
                <div class="fp-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="abc@gmail.com" value="<?php echo $prefill_email; ?>" required>
                </div>
                <div>
                    <button type="submit" class="btn-otp">📧 Send OTP</button>
                </div>
            </div>
        </form>

        <!-- STEP 2: Verify OTP + set new password -->
        <div class="fp-step2">
            <?php if (!empty($prefill_email) && empty($flash_error)): ?>
                <div class="fp-info">OTP sent above — enter the code and your new password below.</div>
            <?php endif; ?>
            <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/ResetPasswordController.php" method="POST">
                <input type="hidden" name="email" value="<?php echo $prefill_email; ?>">
                <div class="fp-group">
                    <label>Verification Code (OTP)</label>
                    <input type="text" name="otp_code" placeholder="Enter 6-digit code" maxlength="6" required>
                </div>
                <div class="fp-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="minimum of 8 characters" minlength="8" required>
                </div>
                <div class="fp-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Re-enter new password" minlength="8" required>
                </div>
                <button type="submit" class="fp-submit">Reset Password</button>
            </form>
        </div>

        <div class="fp-back">
            <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php">← Back to Sign In</a>
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
