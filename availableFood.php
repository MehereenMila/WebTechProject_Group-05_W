<?php
session_start();
// Security Check: Shudhu Manager jate ei page dekhte pare
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

// Database connect kora holo
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Database theke shudhu Pending khabar gulo ana hocche
$sql = "SELECT * FROM donations WHERE status = 'Pending' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Food Lists - Manager</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        /* Table er jonno chotto kichu CSS */
        .food-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #1a5629; border-radius: 10px; overflow: hidden; }
        .food-table th, .food-table td { padding: 15px; text-align: left; color: white; border-bottom: 1px solid #2e8f46; }
        .food-table th { background: #0f3b1b; color: #a9dfb6; }
        .btn-assign { background: #d4a017; color: #111; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-assign:hover { background: #b8860b; }
        .empty-msg { text-align: center; color: #88c798; padding: 20px; font-size: 18px; }
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/availableFood.php" class="menu-item active"><span class="icon">📋</span> Available Food Lists</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignTask.php" class="menu-item"><span class="icon">➕</span> Assign Tasks</a>                    <a href="#" class="menu-item"><span class="icon">⏱️</span> Donation History</a>
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
                <h1>Available Food Lists</h1>
                <div class="topbar-right">
                    <div class="search-bar">
                        <span>🔍</span>
                        <input type="text" placeholder="Search food...">
                    </div>
                    <button class="btn-notification">🔔</button>
                    <div class="topbar-avatar" style="background: #3eb55c;">SG</div>
                </div>
            </header>

            <!-- Table Section -->
            <div class="dash-header" style="margin-top: 20px;">
                <div>
                    <h2>Pending Donations</h2>
                    <p>List of surplus food waiting to be assigned to a volunteer</p>
                </div>
            </div>

            <table class="food-table">
                <thead>
                    <tr>
                        <th>Food Type</th>
                        <th>Quantity</th>
                        <th>Pickup Date & Time</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['food_type']); ?></strong><br>
                                    <span style="font-size:12px; color:#a9dfb6;"><?php echo htmlspecialchars($row['food_category']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td>
                                    📅 <?php echo htmlspecialchars($row['pickup_day']); ?><br>
                                    ⏰ <?php echo htmlspecialchars($row['expiry_time']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td>
                                    <button class="btn-assign">Assign Task</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-msg">No pending donations available right now.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>
</body>
</html>