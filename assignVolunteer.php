<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security: Shudhu Manager ekhane dhukte parbe
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'manager') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$managerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Manager';

// Database theke Pending/Active donations ar Volunteer-der list ana hocche
$donationsQuery = "SELECT d.*, u.name as donor_name FROM donations d JOIN users u ON d.donor_id = u.id WHERE d.status = 'Pending' ORDER BY d.id DESC";
$donationsResult = $conn->query($donationsQuery);

$volunteersQuery = "SELECT id, name FROM users WHERE role = 'volunteer'";
$volunteersResult = $conn->query($volunteersQuery);
$volunteers = [];
while($vRow = $volunteersResult->fetch_assoc()) {
    $volunteers[] = $vRow;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Volunteers - Manager</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #fff3e0; color: #e67e22; }
        select { padding: 8px; border-radius: 4px; border: 1px solid #ccc; width: 150px; }
        .btn-assign { background: #e67e22; color: white; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-assign:hover { background: #d35400; }
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/foodRequests.php" class="menu-item"><span class="icon">📦</span> Food Requests</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php" class="menu-item active"><span class="icon">🚴</span> Assign Volunteers</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/reports.php" class="menu-item"><span class="icon">📊</span> Reports</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Assign Volunteers for Pickup</h1>
            </header>

            <div class="table-container">
                <h2>Pending Food Requests</h2>
                <p>Select a volunteer to assign to incoming food donations.</p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Select Volunteer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($donationsResult->num_rows > 0): ?>
                            <?php while($row = $donationsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td>
                                        <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Manager/AssignController.php" method="POST" style="display:flex; gap:10px;">
                                            <input type="hidden" name="donation_id" value="<?php echo $row['id']; ?>">
                                            <select name="volunteer_id" required>
                                                <option value="">Choose Volunteer</option>
                                                <?php foreach($volunteers as $vol): ?>
                                                    <option value="<?php echo $vol['id']; ?>"><?php echo htmlspecialchars($vol['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                    </td>
                                    <td>
                                            <button type="submit" class="btn-assign">Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">No pending food requests to assign.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>