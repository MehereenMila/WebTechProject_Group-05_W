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

$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin User';
$userInitial = strtoupper(substr($userName, 0, 2));

$userQuery = "SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Tracking - Admin</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .tracking-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.08); }
        .tracking-table th, .tracking-table td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        .tracking-table th { background: #0f3b1b; color: #a9dfb6; }
        .route-cell { display: flex; align-items: center; gap: 6px; font-size: 13px; }
        .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; }
        .status-assigned { background: #f0ad4e; }
        .status-transit { background: #5bc0de; }
        .live-dot { display: inline-block; width: 9px; height: 9px; border-radius: 50%; background: #2ecc71; margin-right: 6px; animation: pulse 1.4s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(46,204,113,0.6); } 70% { box-shadow: 0 0 0 8px rgba(46,204,113,0); } 100% { box-shadow: 0 0 0 0 rgba(46,204,113,0); } }
        .last-updated { color: #888; font-size: 12px; }
        .empty-msg { text-align: center; color: #999; padding: 30px; }
    </style>
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php" class="menu-item"><span class="icon">👥</span> User Management</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php" class="menu-item"><span class="icon">🖥️</span> Food Monitor</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/deliveryTracking.php" class="menu-item active"><span class="icon">📍</span> Delivery Tracking</a>
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
                <h1>Delivery Tracking</h1>
            </header>

            <div class="dash-header" style="margin-top: 20px;">
                <div>
                    <h2><span class="live-dot"></span>Live Deliveries</h2>
                    <p>Auto-refreshes every few seconds — <span class="last-updated" id="lastUpdated">loading...</span></p>
                </div>
            </div>

            <table class="tracking-table">
                <thead>
                    <tr>
                        <th>Food</th>
                        <th>Donor</th>
                        <th>Volunteer</th>
                        <th>Pickup Location</th>
                        <th>Delivery Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="trackingBody">
                    <tr><td colspan="6" class="empty-msg">Loading live data...</td></tr>
                </tbody>
            </table>

            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>
   <script>
        function loadDeliveries() 
        {
            var xhttp = new XMLHttpRequest();
            
            xhttp.onreadystatechange = function()
            {
                if (this.readyState === 4 && this.status === 200) 
                {
                    const body = document.getElementById('trackingBody');
                    document.getElementById('lastUpdated').textContent = 'Last updated: ' + new Date().toLocaleTimeString();
                    body.innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "/Web_Technology%20Summer%2025-26/FoodShare/Controller/Admin/GetLiveDeliveries.php", true);
            xhttp.send();
        }

        loadDeliveries();
        setInterval(loadDeliveries, 6000);
    </script>
</body>
</html>
