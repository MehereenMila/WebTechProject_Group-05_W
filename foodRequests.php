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

// Shob donation - donor + assigned volunteer (thakle) shoho
$sql = "SELECT d.*, u1.name as donor_name, u2.name as volunteer_name
        FROM donations d
        JOIN users u1 ON d.donor_id = u1.id
        LEFT JOIN users u2 ON d.volunteer_id = u2.id
        ORDER BY d.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Requests - Manager</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .table-container { margin: 20px; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #fff3e0; color: #e67e22; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; color: white; }
        .status-pending { background: #f0ad4e; }
        .status-assigned { background: #d4a017; }
        .status-transit, .status-intransit { background: #5bc0de; }
        .status-delivered { background: #5cb85c; }
        .filter-bar { margin: 0 0 15px; }
        .filter-bar input { padding: 10px 14px; width: 320px; max-width: 100%; border-radius: 6px; border: 1px solid #ccc; }
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/foodRequests.php" class="menu-item active"><span class="icon">📦</span> Food Requests</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php" class="menu-item"><span class="icon">🚴</span> Assign Volunteers</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/reports.php" class="menu-item"><span class="icon">📊</span> Reports</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="//Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h1>Food Requests Overview</h1>
            </header>

            <div class="table-container">
                <h2>All Donation Requests</h2>
                <p>Full picture of every donation across all statuses — pending, assigned, in transit, and delivered.</p>
                <br>
                <div class="filter-bar">
                    <input type="text" id="filterInput" placeholder="🔍 Filter by donor name or food type...">
                </div>
                <table id="requestsTable">
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Assigned Volunteer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()):
                                $statusClass = 'status-' . strtolower(str_replace(' ', '', $row['status']));
                            ?>
                                <tr data-search="<?php echo strtolower(htmlspecialchars($row['donor_name'] . ' ' . $row['food_type'])); ?>">
                                    <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo $row['volunteer_name'] ? htmlspecialchars($row['volunteer_name']) : '<span style="color:#999;">Not Assigned</span>'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">No food requests found in the system.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('filterInput').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#requestsTable tbody tr').forEach(function (tr) {
                const haystack = tr.getAttribute('data-search') || '';
                tr.style.display = haystack.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>
</html>