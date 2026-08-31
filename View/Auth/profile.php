<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$userName = isset($user['name']) ? $user['name'] : 'User';
$userInitial = strtoupper(substr($userName, 0, 1));

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
$flash_error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
unset($_SESSION['flash_error']);
unset($_SESSION['flash_success']);

// Email/phone ke partially mask kore dekhanor jonno (security-e pura dekhano thik na)
function maskDestination($value, $type) {
    if (empty($value)) return $type === 'email' ? 'your email' : 'your phone number';
    if ($type === 'email') {
        $parts = explode('@', $value);
        if (count($parts) !== 2) return $value;
        $name = $parts[0];
        $visible = substr($name, 0, min(2, strlen($name)));
        return $visible . str_repeat('*', max(strlen($name) - 2, 3)) . '@' . $parts[1];
    } else {
        $len = strlen($value);
        if ($len < 5) return str_repeat('*', $len);
        return substr($value, 0, 3) . str_repeat('*', $len - 5) . substr($value, -2);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        .settings-container { max-width: 900px; margin: 50px auto; background: white; border-radius: 14px; box-shadow: 0 8px 25px rgba(0,0,0,0.06); display: flex; overflow: hidden; min-height: 520px; }
        
        .settings-sidebar { width: 260px; background: #fdfdfd; border-right: 1px solid #eaeaea; padding: 30px 20px; }
        .settings-sidebar h3 { font-size: 18px; color: #2c3e50; margin-bottom: 25px; padding-left: 10px; }
        .settings-menu-item { display: block; padding: 12px 15px; color: #555; text-decoration: none; font-weight: 500; font-size: 14px; border-radius: 8px; margin-bottom: 8px; transition: all 0.2s; }
        .settings-menu-item:hover { background: #f0f4f1; color: #2e8f46; }
        .settings-menu-item.active { background: #e8f5e9; color: #2e8f46; font-weight: 600; }

        .settings-content { flex-grow: 1; padding: 40px; }
        .content-header { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; border-bottom: 1px solid #eaeaea; padding-bottom: 15px; }
        
        .avatar-circle { width: 65px; height: 65px; background: #2e8f46; color: white; font-size: 24px; font-weight: bold; display: flex; align-items: center; justify-content: center; border-radius: 50%; overflow: hidden; object-fit: cover; }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

        .content-header h2 { margin: 0; font-size: 20px; color: #2c3e50; }
        .content-header p { margin: 3px 0 0 0; color: #888; font-size: 13px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 7px; font-weight: 600; color: #34495e; font-size: 13px; }
        .form-group input { width: 100%; padding: 11px 14px; border: 1px solid #dcdde1; border-radius: 8px; font-size: 14px; background: #fafafa; }
        .form-group input:focus { border-color: #2e8f46; outline: none; background: #fff; box-shadow: 0 0 0 3px rgba(46,143,70,0.1); }

        .btn-save { background: #2e8f46; color: white; border: none; padding: 11px 22px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: background 0.2s; }
        .btn-save:hover { background: #206a37; }
        
        .btn-otp { background: #f39c12; color: white; border: none; padding: 11px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; height: 43px; white-space: nowrap; }
        .btn-otp:hover { background: #d68910; }

        .otp-row { display: flex; gap: 10px; align-items: flex-end; }
        .otp-row .form-group { flex-grow: 1; margin-bottom: 0; }

        .info-box { background: #fff8e1; border-left: 4px solid #f39c12; padding: 12px 15px; border-radius: 0 8px 8px 0; margin-bottom: 20px; font-size: 13px; color: #7f6000; line-height: 1.4; }
        .info-box-blue { background: #eaf3ff; border-left: 4px solid #2a75d3; color: #1a4d8f; }

        /* --- Password & Security: Method Selector --- */
        .method-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background: #f4f6f5;
            padding: 6px;
            border-radius: 12px;
        }
        .method-pill {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 14px 10px;
            background: transparent;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #7a8a80;
            transition: all 0.2s ease;
        }
        .method-pill-icon { font-size: 20px; }
        .method-pill:hover { background: rgba(46,143,70,0.08); color: #2e8f46; }
        .method-pill.active {
            background: #ffffff;
            color: #2e8f46;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .method-panel {
            animation: fadeIn 0.25s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        @media (max-width: 640px) {
            .form-grid-2col { grid-template-columns: 1fr; }
        }

        .btn-danger { background: #d9534f; margin-top: 10px; }
        .btn-danger:hover { background: #c9302c; }
    </style>
    <script>
        // Live image preview script
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('profilePreview');
                output.innerHTML = '<img src="' + reader.result + '" alt="Preview" style="width:100%; height:100%; object-fit:cover;">';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>

    <div class="settings-container">
        <!-- Left Sub-Sidebar -->
        <div class="settings-sidebar">
            <h3>Account Settings</h3>
            <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=profile" class="settings-menu-item <?php echo ($tab == 'profile') ? 'active' : ''; ?>">👤 Edit Profile</a>
            <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php?tab=security" class="settings-menu-item <?php echo ($tab == 'security') ? 'active' : ''; ?>">🔐 Password & Security</a>
            <a href="javascript:history.back()" class="settings-menu-item">⬅ Go Back</a>
        </div>

        <!-- Right Content Area -->
        <div class="settings-content">

            <?php if (!empty($flash_error)): ?>
         <div class="flash-message flash-error" id="flashMsg"><span class="flash-icon">!</span><span><?php echo htmlspecialchars($flash_error); ?></span><button class="flash-close" onclick="dismissFlash()">×</button></div>
            <?php endif; ?>
             <?php if (!empty($flash_success)): ?>
             <div class="flash-message flash-success" id="flashMsg"><span class="flash-icon">✓</span><span><?php echo htmlspecialchars($flash_success); ?></span><button class="flash-close" onclick="dismissFlash()">×</button></div>
            <?php endif; ?>
            
            <?php if ($tab == 'profile'): ?>
                <!-- TAB 1: EDIT PROFILE WITH LIVE PREVIEW -->
                <div class="content-header">
                    <div class="avatar-circle" id="profilePreview">
                        <?php if (!empty($user['profile_pic'])): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
                        <?php else: ?>
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2>Edit Profile</h2>
                        <p>Update your personal details and profile picture</p>
                    </div>
                </div>

                <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/UpdateProfileController.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Profile Picture (Upload Image)</label>
                        <input type="file" name="profile_pic" accept="image/*" onchange="previewImage(event)" style="background: white; padding: 8px;">
                    </div>

                    <button type="submit" name="update_profile" class="btn-save" style="margin-top: 10px;">Save Changes</button>
                </form>

            <?php else: ?>
                <!-- TAB 2: PASSWORD & SECURITY (Redesigned - 3 methods) -->
                <div class="content-header">
                    <div class="avatar-circle" style="background: #d4a017; font-size: 20px;">🔐</div>
                    <div>
                        <h2>Password & Security</h2>
                        <p>Choose how you'd like to change your password</p>
                    </div>
                </div>

                <!-- Method selector pills -->
                <div class="method-selector">
                    <button type="button" class="method-pill active" data-method="current" onclick="switchMethod('current')">
                        <span class="method-pill-icon">🔑</span>
                        <span>Current Password</span>
                    </button>
                    <button type="button" class="method-pill" data-method="email" onclick="switchMethod('email')">
                        <span class="method-pill-icon">📧</span>
                        <span>Email OTP</span>
                    </button>
                </div>

                <!-- METHOD 1: Current Password -->
                <div class="method-panel" id="panel-current">
                    <div class="info-box info-box-blue">
                        <strong>Know your current password?</strong> Verify it below to set a new one instantly — no OTP needed.
                    </div>
                    <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/ChangePasswordController.php" method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="old_password" placeholder="Enter your current password" required>
                        </div>
                        <div class="form-grid-2col">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" placeholder="Enter new secure password" minlength="6" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" placeholder="Re-enter new password" minlength="6" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-save btn-danger">Update Password</button>
                    </form>
                </div>

                <!-- METHOD 2: Email OTP -->
                <div class="method-panel" id="panel-email" style="display:none;">
                    <div class="info-box">
                        <strong>High Security Active:</strong> A 6-digit verification code will be sent to
                        <strong><?php echo htmlspecialchars(maskDestination($user['email'] ?? '', 'email')); ?></strong>.
                    </div>
                    <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/VerifyOTPController.php" method="POST">
                        <div class="otp-row" style="margin-bottom: 20px;">
                            <div class="form-group">
                                <label>Verification Code (OTP)</label>
                                <input type="text" name="otp_code" placeholder="Enter 6-digit code" maxlength="6" required>
                            </div>
                            <div>
                                <button type="button" class="btn-otp" onclick="window.location.href='/Web_Technology%20Summer%2025-26/FoodShare/Controller/Auth/SendOTPController.php?method=email'">📧 Send OTP</button>
                            </div>
                        </div>
                        <div class="form-grid-2col">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" placeholder="Enter new secure password" minlength="6" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" placeholder="Re-enter new password" minlength="6" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-save btn-danger">Verify & Update Password</button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>

        <script>
        function switchMethod(method) {
            document.querySelectorAll('.method-pill').forEach(p => p.classList.remove('active'));
            document.querySelector('.method-pill[data-method="' + method + '"]').classList.add('active');

            document.querySelectorAll('.method-panel').forEach(p => p.style.display = 'none');
            document.getElementById('panel-' + method).style.display = 'block';
        }

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