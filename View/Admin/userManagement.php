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

// Database theke shob user der data ana hocche
$sql = "SELECT id, name, email, role FROM users ORDER BY role ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f1f8e9; color: #2e8f46; }
        .role-badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; color: white;}
        .role-admin { background: #d9534f; }
        .role-manager { background: #f0ad4e; }
        .role-donor { background: #5bc0de; }
        .role-volunteer { background: #5cb85c; }
        select { padding: 5px; border-radius: 4px; }
        .btn-update { background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body class="dash-body">
    <div class="dash-container">
        <!-- Sidebar (Ager motoi, shudhu link change) -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge role-admin">Admin</span>
            </div>
            <div class="sidebar-menu">
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php" class="menu-item active"><span class="icon">👥</span> User Management</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php" class="menu-item"><span class="icon">🖥️</span> Food Monitor</a>
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
                <h1>User Management</h1>
            </header>

            <div class="table-container">
                <h2>Manage User Roles</h2>
                <p>Change user roles (Admin, Manager, Volunteer, Donor) from here.</p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Change Role To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo strtolower($row['role']); ?>">
                                            <?php echo ucfirst($row['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Admin/UpdateRoleController.php" method="POST" style="display:flex; gap:10px;">
                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                            <select name="new_role">
                                                <option value="donor" <?php if($row['role']=='donor') echo 'selected'; ?>>Donor</option>
                                                <option value="volunteer" <?php if($row['role']=='volunteer') echo 'selected'; ?>>Volunteer</option>
                                                <option value="manager" <?php if($row['role']=='manager') echo 'selected'; ?>>Manager</option>
                                                <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
                                            </select>
                                    </td>
                                    <td>
                                            <button type="submit" class="btn-update">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>