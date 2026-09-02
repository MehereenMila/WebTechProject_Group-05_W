<?php
    session_start();
    // Security Check
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
        header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
        exit();
    }

    require_once __DIR__ . '/../../Model/DatabaseConnection.php';
    $database = new DatabaseConnection();
    $conn = $database->openConnection();

    // Database theke Pending donations gulo ana hocche
    $pending_sql = "SELECT id, food_type, quantity, location FROM donations WHERE status = 'Pending'";
    $pending_result = $conn->query($pending_sql);

    // Database theke Volunteer der list ana hocche
    $volunteer_sql = "SELECT id, name FROM users WHERE role = 'volunteer'";
    $volunteer_result = $conn->query($volunteer_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Task - Manager</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .assign-form-box { background: #1a5629; padding: 30px; border-radius: 10px; width: 60%; margin: 20px auto; color: white;}
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; color: #a9dfb6; margin-bottom: 8px; font-weight: bold;}
        .form-group select { width: 100%; padding: 12px; border-radius: 8px; background: #0f3b1b; border: 1px solid #2e8f46; color: white; font-size: 15px;}
        .btn-assign-submit { background: #d4a017; color: #111; padding: 15px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; margin-top: 10px;}
        .btn-assign-submit:hover { background: #b8860b; }
    </style>
</head>
<body class="dash-body">
    <div class="dash-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge" style="background: #1e7e34;">Manager</span>
            </div>
            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/availableFood.php" class="menu-item"><span class="icon">📋</span> Available Food Lists</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignTask.php" class="menu-item active"><span class="icon">➕</span> Assign Tasks</a>
                <a href="#" class="menu-item"><span class="icon">⏱️</span> Donation History</a>
                <a href="#" class="menu-item"><span class="icon">🚚</span> Track Deliveries</a>
                <a href="#" class="menu-item"><span class="icon">👥</span> Volunteer</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="#" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="background: #3eb55c;">SG</div>
                    <div class="details">
                        <h4>Shuvo</h4>
                        <p>Manager</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Assign Task to Volunteer</h1>
            </header>

            <div class="assign-form-box">
                <h3 style="margin-bottom: 20px; color: #d4a017; text-align:center;">📋 Select Food & Volunteer</h3>
                <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Manager/AssignTaskController.php" method="POST">
                    
                    <div class="form-group">
                        <label>Select Pending Food Donation</label>
                        <select name="donation_id" required>
                            <option value="">-- Choose a Food Listing --</option>
                            <?php if ($pending_result->num_rows > 0): ?>
                                <?php while($row = $pending_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo htmlspecialchars($row['food_type']) . " (" . htmlspecialchars($row['quantity']) . ") - at " . htmlspecialchars($row['location']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="" disabled>No pending food available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Available Volunteer</label>
                        <select name="volunteer_id" required>
                            <option value="">-- Choose a Volunteer --</option>
                            <?php if ($volunteer_result->num_rows > 0): ?>
                                <?php while($vol = $volunteer_result->fetch_assoc()): ?>
                                    <option value="<?php echo $vol['id']; ?>">
                                        <?php echo htmlspecialchars($vol['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="" disabled>No volunteers available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-assign-submit">Assign Task Now</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>