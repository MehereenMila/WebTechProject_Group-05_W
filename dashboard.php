<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security Check: Volunteer role check
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'volunteer') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$volunteer_id = $_SESSION['user_id'];
$volunteerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Volunteer';
$volunteerInitial = strtoupper(substr($volunteerName, 0, 1));

// Fetch Volunteer details including profile picture from database
$userQuery = "SELECT * FROM users WHERE id = '$volunteer_id'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

// 1. Assigned Tasks Count (Delivered bad diye)
$assignedQuery = "SELECT COUNT(*) as assigned FROM donations WHERE volunteer_id = '$volunteer_id' AND status = 'Assigned'";
$assignedResult = $conn->query($assignedQuery);
$assignedCount = $assignedResult->fetch_assoc()['assigned'];

 // 2. In Transit Count
$transitQuery = "SELECT COUNT(*) as transit FROM donations WHERE volunteer_id = '$volunteer_id' AND status = 'In Transit'";
$transitResult = $conn->query($transitQuery);
$transitCount = $transitResult->fetch_assoc()['transit'];

// 3. Completed Deliveries Count
$completedQuery = "SELECT COUNT(*) as completed FROM donations WHERE volunteer_id = '$volunteer_id' AND status = 'Delivered'";
$completedResult = $conn->query($completedQuery);
$completedCount = $completedResult->fetch_assoc()['completed'];

// 4. Active Tasks List (Only non-delivered tasks for dashboard view)
$tasksQuery = "SELECT * FROM donations WHERE volunteer_id = '$volunteer_id' AND status != 'Delivered' ORDER BY id DESC LIMIT 5";
$tasksResult = $conn->query($tasksQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
</head>
<body class="dash-body">
    <div class="dash-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge" style="background: #28a745;">Volunteer</span>
            </div>

            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/dashboard.php" class="menu-item active"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/deliveryTasks.php" class="menu-item"><span class="icon">🚚</span> Delivery Tasks</a>                
                <!-- History Link Connected -->
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/history.php" class="menu-item"><span class="icon">⏱️</span> History</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>            
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <!-- Sidebar Avatar with Profile Picture Support -->
                    <div class="avatar" style="background: #3eb55c; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $volunteerInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <h4><?php echo htmlspecialchars($volunteerName); ?></h4>
                        <p>Volunteer</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Volunteer Dashboard</h1>
                <div class="topbar-right">
                    <div class="search-bar">
                        <span>🔍</span>
                        <input type="text" placeholder="Search...">
                    </div>
                    <button class="btn-notification">🔔</button>
                    <!-- Topbar Avatar with Profile Picture Support -->
                    <div class="topbar-avatar" style="background: #3eb55c; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $volunteerInitial; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="dash-header">
                <div>
                    <h2>Delivery Operations</h2>
                    <p>Track your assigned tasks and delivery status</p>
                </div>
                <div class="date"><?php echo date('l, j F Y'); ?></div>
            </div>

            <!-- Dynamic Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <p>ASSIGNED TASKS</p>
                    <h3><?php echo $assignedCount; ?></h3>
                    <span style="font-size: 11px; color: #2e8f46; font-weight: bold;">+Currently Active</span>
                    <span class="stat-icon">📋</span>
                </div>
                <div class="stat-card">
                    <p>IN TRANSIT</p>
                    <h3><?php echo $transitCount; ?></h3>
                    <span style="font-size: 11px; color: #d4a017; font-weight: bold;">On the way</span>
                    <span class="stat-icon">🚚</span>
                </div>
                <div class="stat-card">
                    <p>COMPLETED</p>
                    <h3><?php echo $completedCount; ?></h3>
                    <span style="font-size: 11px; color: #888; font-weight: bold;">All Time</span>
                    <span class="stat-icon">✅</span>
                </div>
                <div class="stat-card">
                    <p>RATING SCORE</p>
                    <h3>5.0</h3>
                    <span style="font-size: 11px; color: #d4a017; font-weight: bold;">Top Rated</span>
                    <span class="stat-icon">⭐</span>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="dash-bottom">
                <div class="chart-section">
                    <div class="section-header">
                        <h3>Delivery Performance</h3>
                        <span class="link-green">This year</span>
                    </div>
                    <div class="chart-placeholder" style="display: flex; align-items: center; justify-content: center; flex-direction: column; color: #666;">
                        <p>Ready for pickups and distributions.</p>
                        <p style="font-size: 13px; margin-top: 5px;">Check assigned tasks from your delivery panel.</p>
                    </div>
                </div>

                <!-- Live Tasks List (Delivered bad diye) -->
                <div class="activity-section">
                    <div class="section-header">
                        <h3>Available / Assigned Tasks</h3>
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/deliveryTasks.php" class="link-green">View all</a>
                    </div>
                    <div class="activity-list" style="max-height: 320px; overflow-y: auto;">
                        <?php if ($tasksResult->num_rows > 0): ?>
                            <?php while($row = $tasksResult->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">📦</div>
                                    <div class="activity-info">
                                        <h4><?php echo htmlspecialchars($row['food_type']); ?> (<?php echo htmlspecialchars($row['quantity']); ?>)</h4>
                                        <p>Pickup: <?php echo htmlspecialchars($row['location']); ?></p>
                                    </div>
                                    <span class="status badge-transit"><?php echo htmlspecialchars($row['status']); ?></span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="padding: 20px; color: #777; text-align: center;">No active delivery tasks available right now.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>
</body>
</html>