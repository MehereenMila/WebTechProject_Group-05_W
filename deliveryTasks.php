<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// Security: Shudhu Volunteer ekhane dhukte parbe
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'volunteer') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$volunteer_id = $_SESSION['user_id'];
$volunteerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Volunteer';

// Ei volunteer ke je task gulo assign kora hoyeche shegulo ana hocche
$sql = "SELECT d.*, u.name as donor_name FROM donations d JOIN users u ON d.donor_id = u.id WHERE d.volunteer_id = '$volunteer_id' ORDER BY d.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Tasks - Volunteer</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #e8f5e9; color: #2e8f46; }
        select { padding: 8px; border-radius: 4px; border: 1px solid #ccc; width: 140px; }
        .btn-update-status { background: #28a745; color: white; border: none; padding: 8px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-update-status:hover { background: #218838; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; color: white; }
        .status-assigned { background: #f0ad4e; }
        .status-transit { background: #5bc0de; }
        .status-delivered { background: #5cb85c; }
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/deliveryTasks.php" class="menu-item active"><span class="icon">🚚</span> Delivery Tasks</a>
            </div>
            <div class="sidebar-footer">
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>My Assigned Deliveries</h1>
            </header>

            <div class="table-container">
                <h2>Active Pickup & Drop Tasks</h2>
                <p>Update your delivery status as you progress.</p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Current Status</th>
                            <th>Change Status</th>
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
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $row['status'])); ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                  </td>
                                   <td>
    <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Volunteer/UpdateStatusController.php" method="POST" style="display:flex; flex-direction:column; gap:6px;">
        <input type="hidden" name="donation_id" value="<?php echo $row['id']; ?>">
        <select name="new_status">
            <option value="Assigned" <?php if($row['status']=='Assigned') echo 'selected'; ?>>Assigned</option>
            <option value="In Transit" <?php if($row['status']=='In Transit') echo 'selected'; ?>>In Transit</option>
            <option value="Delivered" <?php if($row['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
        </select>
        <input type="text" name="delivery_location" placeholder="Delivery location (e.g. NGO/shelter address)" value="<?php echo htmlspecialchars($row['delivery_location'] ?? ''); ?>" style="padding:6px; border-radius:4px; border:1px solid #ccc; width:200px;">
</td>
<td>
        <button type="submit" class="btn-update-status">Update</button>
    </form>
</td>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center; color: #777;">No delivery tasks assigned to you yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>