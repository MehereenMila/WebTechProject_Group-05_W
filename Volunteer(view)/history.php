<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'volunteer') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$volunteer_id = $_SESSION['user_id'];
$volunteerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Volunteer';
$volunteerInitial = strtoupper(substr($volunteerName, 0, 1));

//Shudhu delivered task gulo history te ashe
$sql = "SELECT d.*, u.name as donor_name FROM donations d JOIN users u ON d.donor_id = u.id WHERE d.volunteer_id = '$volunteer_id' AND d.status = 'Delivered' ORDER BY d.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery History - Volunteer</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #e8f5e9; color: #2e8f46; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; color: white; background: #5cb85c; }
    </style>
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/deliveryTasks.php" class="menu-item"><span class="icon">🚚</span> Delivery Tasks</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/history.php" class="menu-item active"><span class="icon">⏱️</span> History</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="#" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="background: #3eb55c;"><?php echo $volunteerInitial; ?></div>
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
                <h1>Completed Delivery History</h1>
                <div class="topbar-right">
                    <div class="search-bar">
                        <span>🔍</span>
                        <input type="text" placeholder="Search...">
                    </div>
                    <button class="btn-notification">🔔</button>
                    <div class="topbar-avatar" style="background: #3eb55c;"><?php echo $volunteerInitial; ?></div>
                </div>
            </header>

            <div class="table-container">
                <h2>Successfully Delivered Tasks</h2>
                <p>List of all food distributions completed by you.</p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Status</th>
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
                                    <td><span class="status-badge">Delivered</span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align: center; color: #777;">No completed deliveries in history yet.</td></tr>
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