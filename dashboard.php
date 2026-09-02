<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security Check: Manager role check
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'manager') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$manager_id = $_SESSION['user_id'];
$managerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Manager';
$managerInitial = strtoupper(substr($managerName, 0, 1));

// Fetch Manager details including profile picture from database
$userQuery = "SELECT * FROM users WHERE id = '$manager_id'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

// 1. Total Donations in system
$totalQuery = "SELECT COUNT(*) as total FROM donations";
$totalResult = $conn->query($totalQuery);
$totalDonations = $totalResult->fetch_assoc()['total'];

// 2. Pending Donations (Awaiting Assignment)
$pendingQuery = "SELECT COUNT(*) as pending FROM donations WHERE status = 'Pending'";
$pendingResult = $conn->query($pendingQuery);
$pendingCount = $pendingResult->fetch_assoc()['pending'];

// 3. Completed Deliveries
$completedQuery = "SELECT COUNT(*) as completed FROM donations WHERE status = 'Delivered'";
$completedResult = $conn->query($completedQuery);
$completedCount = $completedResult->fetch_assoc()['completed'];

// 4. Pending Listings list for table/activities
$listingsQuery = "SELECT * FROM donations ORDER BY id DESC LIMIT 5";
$listingsResult = $conn->query($listingsQuery);

// 5. Available Volunteers - role='volunteer' AND currently no active (non-delivered) assignment
$availableVolunteersQuery = "SELECT id, name, phone FROM users 
    WHERE role = 'volunteer' 
    AND id NOT IN (
        SELECT volunteer_id FROM donations 
        WHERE volunteer_id IS NOT NULL AND status != 'Delivered'
    )
    ORDER BY name ASC";
$availableVolunteersResult = $conn->query($availableVolunteersQuery);
$availableCount = $availableVolunteersResult ? $availableVolunteersResult->num_rows : 0;

// Total volunteer count (for the stat card)
$totalVolunteersQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'volunteer'";
$totalVolunteersResult = $conn->query($totalVolunteersQuery);
$totalVolunteers = $totalVolunteersResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .volunteer-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; }
        .volunteer-row:last-child { border-bottom: none; }
        .volunteer-info { display: flex; align-items: center; gap: 10px; }
        .volunteer-avatar { width: 36px; height: 36px; border-radius: 50%; background: #fff3e0; color: #e67e22; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; }
        .volunteer-name { font-weight: 600; font-size: 14px; color: #333; }
        .volunteer-phone { font-size: 12px; color: #888; }
        .available-badge { background: #eafaf1; color: #2ecc71; font-size: 11px; font-weight: bold; padding: 4px 10px; border-radius: 12px; }
    </style>
</head>
<body class="dash-body">
    <div class="dash-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge" style="background: #e67e22;">Manager</span>
            </div>

            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/dashboard.php" class="menu-item active"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/foodRequests.php" class="menu-item"><span class="icon">📦</span> Food Requests</a>                
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php" class="menu-item"><span class="icon">🚴</span> Assign Volunteers</a>                
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/reports.php" class="menu-item"><span class="icon">📊</span> Reports</a>                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>            
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <!-- Sidebar Avatar with Profile Picture Support -->
                    <div class="avatar" style="background: #e67e22; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $managerInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <h4><?php echo htmlspecialchars($managerName); ?></h4>
                        <p>Operations Manager</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Manager Dashboard</h1>
                <div class="topbar-right">
                    <div class="search-bar">
                        <span>🔍</span>
                        <input type="text" placeholder="Search...">
                    </div>
                    <button class="btn-notification">🔔</button>
                    <!-- Topbar Avatar with Profile Picture Support -->
                    <div class="topbar-avatar" style="background: #e67e22; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $managerInitial; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="dash-header">
                <div>
                    <h2>Food Redistribution Overview</h2>
                    <p>Manage incoming donations and coordinate with volunteers</p>
                </div>
                <div class="date"><?php echo date('l, j F Y'); ?></div>
            </div>

            <!-- Dynamic Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <p>TOTAL DONATIONS</p>
                    <h3><?php echo $totalDonations; ?></h3>
                    <span style="font-size: 11px; color: #2e8f46; font-weight: bold;">System Wide</span>
                    <span class="stat-icon">📦</span>
                </div>
                <div class="stat-card">
                    <p>PENDING REQUESTS</p>
                    <h3><?php echo $pendingCount; ?></h3>
                    <span style="font-size: 11px; color: #d4a017; font-weight: bold;">Needs Action</span>
                    <span class="stat-icon">⏳</span>
                </div>
                <div class="stat-card">
                    <p>COMPLETED</p>
                    <h3><?php echo $completedCount; ?></h3>
                    <span style="font-size: 11px; color: #888; font-weight: bold;">Delivered</span>
                    <span class="stat-icon">✅</span>
                </div>
                <div class="stat-card">
                    <p>ACTIVE VOLUNTEERS</p>
                    <h3><?php echo $availableCount; ?> / <?php echo $totalVolunteers; ?></h3>
                    <span style="font-size: 11px; color: #2e8f46; font-weight: bold;">Ready</span>
                    <span class="stat-icon">🚴</span>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="dash-bottom">
                <div class="chart-section">
                    <div class="section-header">
                        <h3>Operations Flow</h3>
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php" class="link-green">View all</a>
                    </div>
                    <div style="max-height: 320px; overflow-y: auto;">
                        <?php if ($availableVolunteersResult && $availableVolunteersResult->num_rows > 0): ?>
                            <?php while($vol = $availableVolunteersResult->fetch_assoc()): ?>
                                <div class="volunteer-row">
                                    <div class="volunteer-info">
                                        <div class="volunteer-avatar"><?php echo strtoupper(substr($vol['name'], 0, 2)); ?></div>
                                        <div>
                                            <div class="volunteer-name"><?php echo htmlspecialchars($vol['name']); ?></div>
                                            <div class="volunteer-phone"><?php echo htmlspecialchars($vol['phone'] ?? '-'); ?></div>
                                        </div>
                                    </div>
                                    <span class="available-badge">Available</span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="padding: 20px; color: #777; text-align: center;">No volunteers currently available — all are on active deliveries.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Live Donation Requests List -->
                <div class="activity-section">
                    <div class="section-header">
                        <h3>Recent Food Listings</h3>
                        <a href="#" class="link-green">View all</a>
                    </div>
                    <div class="activity-list" style="max-height: 320px; overflow-y: auto;">
                        <?php if ($listingsResult->num_rows > 0): ?>
                            <?php while($row = $listingsResult->fetch_assoc()): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">🍱</div>
                                    <div class="activity-info">
                                        <h4><?php echo htmlspecialchars($row['food_type']); ?> (<?php echo htmlspecialchars($row['quantity']); ?>)</h4>
                                        <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                                    </div>
                                    <span class="status badge-transit"><?php echo htmlspecialchars($row['status']); ?></span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="padding: 20px; color: #777; text-align: center;">No food listings available.</p>
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