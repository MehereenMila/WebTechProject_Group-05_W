<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security: Shudhu Admin ekhane dhukte parbe
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';
$userInitial = strtoupper(substr($userName, 0, 2));

// Database theke shob donor-er food listings ana hocche
$sql = "SELECT d.*, u.name as donor_name FROM donations d JOIN users u ON d.donor_id = u.id ORDER BY d.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Monitor - Admin</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #f1f8e9; color: #2e8f46; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; color: white; }
        .status-pending { background: #f0ad4e; }
        .status-transit { background: #5bc0de; }
        .status-delivered { background: #5cb85c; }
        .btn-delete { background: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-delete:hover { background: #c9302c; }
    </style>
</head>
<body class="dash-body">
    <div class="dash-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge">Admin</span>
            </div>
            <div class="sidebar-menu">
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php" class="menu-item"><span class="icon">👥</span> User Management</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php" class="menu-item active"><span class="icon">🖥️</span> Food Monitor</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/deliveryTracking.php" class="menu-item"><span class="icon">📍</span> Delivery Tracking</a>
                <a href="#" class="menu-item" onclick="alert('🤝 NGO Collaboration — Coming Soon!'); return false;"><span class="icon">🤝</span> NGO Collaboration</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/reportsAnalytics.php" class="menu-item"><span class="icon">📊</span> Reports & Analytics</a>
            </div>
            <div class="sidebar-footer">
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Food Monitor (System Wide)</h1>
            </header>

            <div class="table-container">
                <h2>All Food Donation Listings</h2>
                <p>Monitor all active and completed donations across the platform.</p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Admin/DeleteFoodController.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this listing?');">
                                            <button class="btn-delete">Delete</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">No food listings found in the system.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>