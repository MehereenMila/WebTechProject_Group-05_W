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

$managerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Manager';
$managerInitial = strtoupper(substr($managerName, 0, 1));

$userQuery = "SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

// Full report data
$sql = "SELECT d.id, d.food_type, d.quantity, d.location, d.delivery_location,
               d.status, d.created_at, d.delivered_at,
               u1.name as donor_name, u2.name as volunteer_name
        FROM donations d
        JOIN users u1 ON d.donor_id = u1.id
        LEFT JOIN users u2 ON d.volunteer_id = u2.id
        ORDER BY d.id DESC";
$result = $conn->query($sql);

// Summary counts
$totalQuery = "SELECT COUNT(*) as total FROM donations";
$total = $conn->query($totalQuery)->fetch_assoc()['total'];

$deliveredQuery = "SELECT COUNT(*) as c FROM donations WHERE status = 'Delivered'";
$delivered = $conn->query($deliveredQuery)->fetch_assoc()['c'];

$pendingQuery = "SELECT COUNT(*) as c FROM donations WHERE status = 'Pending'";
$pending = $conn->query($pendingQuery)->fetch_assoc()['c'];

$activeQuery = "SELECT COUNT(*) as c FROM donations WHERE status NOT IN ('Delivered','Pending')";
$active = $conn->query($activeQuery)->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Manager</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .mreport-summary { display: flex; gap: 16px; margin: 20px 0 24px; flex-wrap: wrap; }
        .mreport-card { flex: 1; min-width: 160px; background: white; border-radius: 14px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 3px 10px rgba(230,126,34,0.08); border-left: 4px solid #e67e22; }
        .mreport-card .icon-circle { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #fff3e0; flex-shrink: 0; }
        .mreport-card h3 { font-size: 22px; margin: 0; color: #2b2b2b; }
        .mreport-card p { font-size: 12px; color: #999; margin: 2px 0 0; text-transform: uppercase; letter-spacing: 0.4px; }

        .mreport-panel { background: white; border-radius: 14px; padding: 22px; box-shadow: 0 3px 10px rgba(0,0,0,0.06); }
        .mreport-panel-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
        .mreport-panel-head h2 { margin: 0; color: #d35400; }
        .mreport-panel-head p { margin: 4px 0 0; color: #888; font-size: 13.5px; }

        .btn-pdf { display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 13.5px; }
        .btn-pdf.weekly { background: #fdebd0; color: #b9770e; border: 1px solid #f5cba7; }
        .btn-pdf.monthly { background: #e67e22; color: white; }

        .filter-bar input { padding: 10px 14px; width: 100%; max-width: 320px; border-radius: 8px; border: 1px solid #eee; background: #fafafa; margin-bottom: 14px; }

        .mreport-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .mreport-table th { text-align: left; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.4px; color: #aaa; padding: 10px 12px; border-bottom: 2px solid #f5f5f5; }
        .mreport-table td { padding: 12px; font-size: 13.5px; border-bottom: 1px solid #f5f5f5; }
        .mreport-table tr:hover td { background: #fffaf3; }

        .m-status { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; color: white; display: inline-block; }
        .m-pending { background: #f0ad4e; }
        .m-assigned { background: #d4a017; }
        .m-transit, .m-intransit { background: #5bc0de; }
        .m-delivered { background: #27ae60; }

        .empty-msg { text-align: center; color: #999; padding: 30px; }
    </style>
</head>
<body class="dash-body">
    <div class="dash-container">
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/assignVolunteer.php" class="menu-item"><span class="icon">🚴</span> Assign Volunteers</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Manager/reports.php" class="menu-item active"><span class="icon">📊</span> Reports</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
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

        <main class="main-content">
            <header class="topbar">
                <h1>Reports</h1>
            </header>

            <div class="mreport-summary">
                <div class="mreport-card">
                    <div class="icon-circle">📦</div>
                    <div><h3><?php echo $total; ?></h3><p>Total Donations</p></div>
                </div>
                <div class="mreport-card">
                    <div class="icon-circle">✅</div>
                    <div><h3><?php echo $delivered; ?></h3><p>Delivered</p></div>
                </div>
                <div class="mreport-card">
                    <div class="icon-circle">🚴</div>
                    <div><h3><?php echo $active; ?></h3><p>In Progress</p></div>
                </div>
                <div class="mreport-card">
                    <div class="icon-circle">⏳</div>
                    <div><h3><?php echo $pending; ?></h3><p>Pending</p></div>
                </div>
            </div>

            <div class="mreport-panel">
                <div class="mreport-panel-head">
                    <div>
                        <h2>Full Donation Report</h2>
                        <p>Every donation this manager can see — donor, food, volunteer and status.</p>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Manager/GenerateReportPDF.php?period=weekly" class="btn-pdf weekly">📄 Weekly PDF</a>
                        <a href="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Manager/GenerateReportPDF.php?period=monthly" class="btn-pdf monthly">📄 Monthly PDF</a>
                    </div>
                </div>

                <div class="filter-bar">
                    <input type="text" id="filterInput" placeholder="🔍 Filter by donor name or food type...">
                </div>

                <div style="overflow-x:auto;">
                <table class="mreport-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Food Type</th>
                            <th>Qty</th>
                            <th>Donated On</th>
                            <th>Volunteer</th>
                            <th>Delivered At</th>
                            <th>Route</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()):
                                $statusClass = 'm-' . strtolower(str_replace(' ', '', $row['status']));
                                $route = $row['location'] . (!empty($row['delivery_location']) ? ' → ' . $row['delivery_location'] : '');
                            ?>
                            <tr data-search="<?php echo strtolower(htmlspecialchars($row['donor_name'] . ' ' . $row['food_type'])); ?>">
                                <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo $row['created_at'] ? date('d M Y, h:i A', strtotime($row['created_at'])) : '-'; ?></td>
                                <td><?php echo $row['volunteer_name'] ? htmlspecialchars($row['volunteer_name']) : '<span style="color:#999;">Not Assigned</span>'; ?></td>
                                <td><?php echo $row['delivered_at'] ? date('d M Y, h:i A', strtotime($row['delivered_at'])) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($route); ?></td>
                                <td><span class="m-status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="empty-msg">No donation records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>

    <script>
        document.getElementById('filterInput').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#reportTable tbody tr').forEach(function (tr) {
                const haystack = tr.getAttribute('data-search') || '';
                tr.style.display = haystack.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>
</html>