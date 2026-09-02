<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security Check

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'donor') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Donor';
$userInitial = strtoupper(substr($userName, 0, 1));

// Fetch user details including profile picture from database 

$userQuery = "SELECT * FROM users WHERE id = '$donor_id'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

// 1. Total Donations Count
$totalQuery = "SELECT COUNT(*) as total FROM donations WHERE donor_id = '$donor_id'";
$totalResult = $conn->query($totalQuery);
$totalDonations = $totalResult->fetch_assoc()['total'];

// 2. Active Listings Count (Status Pending ba Assigned)
$activeQuery = "SELECT COUNT(*) as active FROM donations WHERE donor_id = '$donor_id' AND (status = 'Pending' OR status = 'Assigned')";
$activeResult = $conn->query($activeQuery);
$activeListings = $activeResult->fetch_assoc()['active'];

// 3. Completed Count

$completedQuery = "SELECT COUNT(*) as completed FROM donations WHERE donor_id = '$donor_id' AND status = 'Delivered'";
$completedResult = $conn->query($completedQuery);
$completedDonations = $completedResult->fetch_assoc()['completed'];

// 4. Recent Activity (Latest 4 donations by this donor)
$activityQuery = "SELECT * FROM donations WHERE donor_id = '$donor_id' ORDER BY id DESC LIMIT 4";
$activityResult = $conn->query($activityQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
</head>
<body class="dash-body">
    <div class="dash-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge" style="background: #206a37;">Donor</span>
            </div>

            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/dashboard.php" class="menu-item active"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/createListing.php" class="menu-item"><span class="icon">➕</span> Create Food Listing</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/myListings.php" class="menu-item"><span class="icon">📋</span> My Listings</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php" class="menu-item"><span class="icon">⏱️</span> Donation History</a>

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
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <h4><?php echo htmlspecialchars($userName); ?></h4>
                        <p>Food Donor</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Dashboard</h1>
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
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="dash-header">
                <div>
                    <h2>Donor Dashboard</h2>
                    <p>Overview of your food donations and activities</p>
                </div>
                <div class="date"><?php echo date('l, j F Y'); ?></div>
            </div>

            <!-- Dynamic Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <p>TOTAL DONATIONS</p>
                    <h3><?php echo $totalDonations; ?></h3>
                    <span style="font-size: 11px; color: #2e8f46; font-weight: bold;">All Time</span>
                    <span class="stat-icon">📦</span>
                </div>
                <div class="stat-card">
                    <p>ACTIVE LISTINGS</p>
                    <h3><?php echo $activeListings; ?></h3>
                    <span style="font-size: 11px; color: #2e8f46; font-weight: bold;">+Currently Live</span>
                    <span class="stat-icon">🟢</span>
                </div>
                <div class="stat-card">
                    <p>COMPLETED</p>
                    <h3><?php echo $completedDonations; ?></h3>
                    <span style="font-size: 11px; color: #888; font-weight: bold;">Successfully Delivered</span>
                    <span class="stat-icon">✅</span>
                </div>
                <div class="stat-card">
                    <p>PENDING PICKUP</p>
                    <h3><?php echo $activeListings; ?></h3>
                    <span style="font-size: 11px; color: #d4a017; font-weight: bold;">Awaiting Action</span>
                    <span class="stat-icon">⏳</span>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="dash-bottom">
                <!-- Recent Activity List -->
                <div class="activity-section">
                    <div class="section-header">
                        <h3>My Recent Activity</h3>
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php" class="link-green">View all</a>                    </div>
                    <div class="activity-list">
                        <?php if ($activityResult->num_rows > 0): ?>
                            <?php while($row = $activityResult->fetch_assoc()): ?>
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
                            <p style="padding: 20px; color: #777; text-align: center;">No donations made yet. Create a listing!</p>
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