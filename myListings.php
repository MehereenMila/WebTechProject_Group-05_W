<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'donor') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Donor';
$userInitial = strtoupper(substr($userName, 0, 1));

$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
$flash_error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_success']);
unset($_SESSION['flash_error']);

// Shob nijer listing, sob status soho (Pending, Assigned, Delivered)
    $sql = "SELECT * FROM donations WHERE donor_id = '$donor_id' ORDER BY id DESC";
    $result = $conn->query($sql);
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #e8f5e9; color: #2e8f46; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; color: white; }
        .status-pending  { background: #d4a017; }
        .status-assigned { background: #2a75d3; }
        .status-delivered{ background: #5cb85c; }
        .row-actions a {
            display: inline-block;
            margin-right: 8px;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }
        .action-edit { background: #e4f0d6; color: #2e8f46; }
        .action-delete { background: #fdecea; color: #c0392b; }
    </style>
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/createListing.php" class="menu-item"><span class="icon">➕</span> Create Food Listing</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/myListings.php" class="menu-item active"><span class="icon">📋</span> My Listings</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php" class="menu-item"><span class="icon">⏱️</span> Donation History</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="background: #3eb55c;"><?php echo $userInitial; ?></div>
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
                <h1>My Listings</h1>
                <div class="topbar-right">
                    <div class="search-bar">
                        <span>🔍</span>
                        <input type="text" placeholder="Search...">
                    </div>
                    <button class="btn-notification">🔔</button>
                    <div class="topbar-avatar" style="background: #3eb55c;"><?php echo $userInitial; ?></div>
                </div>
            </header>

            <div class="table-container">
                <h2>All Your Food Listings</h2>
                <p>Every donation you've posted, and its current status.</p>
                <br>

                <?php if (!empty($flash_success)): ?>
                    <div class="flash-message flash-success">✅ <?php echo htmlspecialchars($flash_success); ?></div>
                <?php endif; ?>
                <?php if (!empty($flash_error)): ?>
                    <div class="flash-message flash-error">⚠️ <?php echo htmlspecialchars($flash_error); ?></div>
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Pickup Day</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php
                                    $statusClass = 'status-pending';
                                    if ($row['status'] === 'Assigned') $statusClass = 'status-assigned';
                                    if ($row['status'] === 'Delivered') $statusClass = 'status-delivered';
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pickup_day']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    <td class="row-actions">
                                        <?php if ($row['status'] === 'Pending'): ?>
                                            <a href="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Donor/DeleteListingController.php?id=<?php echo $row['id']; ?>"
                                               class="action-delete"
                                               onclick="return confirm('Delete this listing?');">Delete</a>
                                        <?php else: ?>
                                            <span style="color: #aaa; font-size: 12px;">Locked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">You haven't created any listings yet. <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/createListing.php" class="link-green">Create one</a>.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <footer class="dash-footer">
                <p style="text-align: center; padding: 20px; color: #999;">@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>
</body>
</html>