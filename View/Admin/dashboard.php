<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') 
{
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin User';
$userInitial = strtoupper(substr($userName, 0, 2));

$userQuery = "SELECT * FROM users WHERE id = '$admin_id'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

$usersQuery = "SELECT COUNT(*) as total_users FROM users";
$usersResult = $conn->query($usersQuery);
$totalUsers = $usersResult->fetch_assoc()['total_users'];

$activeDonationsQuery = "SELECT COUNT(*) as active_donations FROM donations WHERE status != 'Delivered'";
$activeDonationsResult = $conn->query($activeDonationsQuery);
$activeDonations = $activeDonationsResult->fetch_assoc()['active_donations'];

$completedQuery = "SELECT COUNT(*) as completed FROM donations WHERE status = 'Delivered'";
$completedResult = $conn->query($completedQuery);
$completedCount = $completedResult->fetch_assoc()['completed'];

$pendingQuery = "SELECT COUNT(*) as pending FROM donations WHERE status = 'Pending'";
$pendingResult = $conn->query($pendingQuery);
$pendingCount = $pendingResult->fetch_assoc()['pending'];

$activityQuery = "SELECT d.*, u.name as donor_name FROM donations d JOIN users u ON d.donor_id = u.id ORDER BY d.id DESC LIMIT 5";
$activityResult = $conn->query($activityQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
</head>
<body class="dash-body">
    <div class="dash-container">
     
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge">Admin</span>
            </div>

            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/dashboard.php" class="menu-item active"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php" class="menu-item"><span class="icon">👥</span> User Management</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php" class="menu-item"><span class="icon">🖥️</span> Food Monitor</a>                
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/deliveryTracking.php" class="menu-item"><span class="icon">📍</span> Delivery Tracking</a>
                <a href="#" class="menu-item" onclick="alert('🤝 NGO Collaboration — Coming Soon!'); return false;"><span class="icon">🤝</span> NGO Collaboration</a>

                <p class="menu-label">REPORTS</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/reportsAnalytics.php" class="menu-item"><span class="icon">📊</span> Reports & Analytics</a>
                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>            
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <h4><?php echo htmlspecialchars($userName); ?></h4>
                        <p>System Administrator</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h1>Dashboard</h1>
                <div class="topbar-right">
                    <div class="search-bar">
                        <span>🔍</span>
                        <input type="text" placeholder="Search...">
                    </div>
                    <button class="btn-notification">🔔</button>
                   
                    <div class="topbar-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="dash-header">
                <div>
                    <h2>Admin Dashboard</h2>
                    <p>Overview of system health and activity</p>
                </div>
                <div class="date"><?php echo date('l, j F Y'); ?></div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <p>TOTAL USERS</p>
                    <h3><?php echo $totalUsers; ?></h3>
                    <span class="stat-icon">👥</span>
                </div>
                <div class="stat-card">
                    <p>ACTIVE DONATIONS</p>
                    <h3><?php echo $activeDonations; ?></h3>
                    <span class="stat-icon">📦</span>
                </div>
                <div class="stat-card">
                    <p>COMPLETED</p>
                    <h3><?php echo $completedCount; ?></h3>
                    <span class="stat-icon">✅</span>
                </div>
                <div class="stat-card">
                    <p>PENDING VERIFY</p>
                    <h3><?php echo $pendingCount; ?></h3>
                    <span class="stat-icon">⏳</span>
                </div>
            </div>

            <div class="dash-bottom">
                <div class="chart-section">
                    <div class="section-header">
                        <h3><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#2ecc71;margin-right:6px;"></span>Live Delivery Tracking</h3>
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/deliveryTracking.php" class="link-green">View all</a>
                    </div>
                    <div style="max-height: 320px; overflow-y: auto;">
                        <table style="width:100%; border-collapse: collapse;" id="miniTrackingTable">
                            <thead>
                                <tr style="text-align:left; font-size:12px; color:#888;">
                                    <th style="padding:8px;">Food</th>
                                    <th style="padding:8px;">Volunteer</th>
                                    <th style="padding:8px;">Route</th>
                                    <th style="padding:8px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="miniTrackingBody">
                                <tr><td colspan="4" style="padding:15px; text-align:center; color:#999;">Loading live data...</td></tr>
                            </tbody>
                        </table>
                        <p style="font-size:11px; color:#aaa; margin-top:8px;" id="miniLastUpdated"></p>
                    </div>
                </div>

                <div class="activity-section">
                    <div class="section-header">
                        <h3>System Recent Activity</h3>
                        <a href="#" class="link-green">View all</a>
                    </div>
                    <div class="activity-list" style="max-height: 320px; overflow-y: auto;">
                        <?php if ($activityResult->num_rows > 0): ?>
                            <?php while($row = $activityResult->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">🍛</div>
                                    <div class="activity-info">
                                        <h4><?php echo htmlspecialchars($row['food_type']); ?></h4>
                                        <p><?php echo htmlspecialchars($row['donor_name']); ?> → <?php echo htmlspecialchars($row['location']); ?></p>
                                    </div>
                                    <span class="status badge-transit"><?php echo htmlspecialchars($row['status']); ?></span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="padding: 20px; color: #777; text-align: center;">No system activity recorded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>

    <script>
        function loadMiniTracking() {
            var xhttp = new XMLHttpRequest();
            
            xhttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    const body = document.getElementById('miniTrackingBody');
                    document.getElementById('miniLastUpdated').textContent = 'Last updated: ' + new Date().toLocaleTimeString();

                    if (!this.responseText.trim()) {
                        body.innerHTML = '<tr><td colspan="4" style="padding:15px; text-align:center; color:#999;">No active deliveries right now.</td></tr>';
                        return;
                    }
                    body.innerHTML = this.responseText;
                }
            };
            
            xhttp.open("GET", "/Web_Technology%20Summer%2025-26/FoodShare/Controller/Admin/GetLiveDeliveries.php?mode=mini", true);
            xhttp.send();
        }
        loadMiniTracking();
        setInterval(loadMiniTracking, 6000);
    </script>
</body>
</html>
