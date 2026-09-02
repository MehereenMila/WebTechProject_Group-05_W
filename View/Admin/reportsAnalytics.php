<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') 
{
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin User';
$userInitial = strtoupper(substr($userName, 0, 2));

$userQuery = "SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

$sql = "SELECT d.id, d.food_type, d.quantity, d.location, d.delivery_location,
               d.status, d.created_at, d.delivered_at,
               u1.name as donor_name,
               d.volunteer_id, u2.name as volunteer_name
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
    <title>Reports &amp; Analytics - Admin</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
    <style>
        .report-table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.08); }
        .report-table th, .report-table td { padding: 13px; text-align: left; border-bottom: 1px solid #eee; font-size: 13.5px; }
        .report-table th { background: #0f3b1b; color: #a9dfb6; position: sticky; top: 0; }
        .status-pill { padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: bold; color: white; }
        .status-pending { background: #f0ad4e; }
        .status-assigned { background: #d4a017; }
        .status-transit, .status-intransit { background: #5bc0de; }
        .status-delivered { background: #5cb85c; }
        .volunteer-link { background: none; border: none; color: #206a37; font-weight: bold; text-decoration: underline; cursor: pointer; font-size: 13.5px; padding: 0; }
        .volunteer-link:hover { color: #123a1c; }
        .filter-bar { margin: 15px 0; }
        .filter-bar input { padding: 10px 14px; width: 320px; max-width: 100%; border-radius: 6px; border: 1px solid #ccc; }
        .empty-msg { text-align: center; color: #999; padding: 30px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 999; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: white; border-radius: 12px; width: 380px; max-width: 90%; padding: 25px; position: relative; text-align: center; }
        .modal-close { position: absolute; top: 12px; right: 16px; cursor: pointer; font-size: 20px; color: #888; background: none; border: none; }
        .modal-photo { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; margin: 0 auto 12px; display: block; background: #1a5629; color: white; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: bold; }
        .modal-box h3 { margin: 5px 0 2px; }
        .modal-row { text-align: left; margin-top: 14px; font-size: 14px; }
        .modal-row b { color: #206a37; }
        .feedback-box { background: #f1f8e9; border-radius: 8px; padding: 10px; margin-top: 10px; font-style: italic; color: #444; }
    </style>
</head>
<body class="dash-body">
    <div class="dash-container">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge">Admin</span>
            </div>
            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/userManagement.php" class="menu-item"><span class="icon">👥</span> User Management</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/foodMonitor.php" class="menu-item"><span class="icon">🖥️</span> Food Monitor</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/deliveryTracking.php" class="menu-item"><span class="icon">📍</span> Delivery Tracking</a>
                <a href="#" class="menu-item" onclick="alert('🤝 NGO Collaboration — Coming Soon!'); return false;"><span class="icon">🤝</span> NGO Collaboration</a>

                <p class="menu-label">REPORTS</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Admin/reportsAnalytics.php" class="menu-item active"><span class="icon">📊</span> Reports & Analytics</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <h4><?php echo htmlspecialchars($userName); ?></h4>
                        <p>System Administrator</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h1>Reports &amp; Analytics</h1>
            </header>

            <div class="dash-header" style="margin-top: 20px;">
                <div>
                    <h2>Full Donation Report</h2>
                    <p>Donor, food, dates, assigned volunteer, route and status — all in one place</p>
                </div>
            </div>

            <div class="filter-bar">
                <input type="text" id="filterInput" placeholder="🔍 Filter by donor name or food type...">
            </div>

            <div style="overflow-x:auto;">
            <table class="report-table" id="reportTable">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Food Type</th>
                        <th>Qty</th>
                        <th>Donated On</th>
                        <th>Volunteer</th>
                        <th>Delivered At</th>
                        <th>Pickup Location</th>
                        <th>Delivery Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()):
                            $statusClass = 'status-' . strtolower(str_replace(' ', '', $row['status']));
                        ?>
                        <tr data-search="<?php echo strtolower(htmlspecialchars($row['donor_name'] . ' ' . $row['food_type'])); ?>">
                            <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo $row['created_at'] ? date('d M Y, h:i A', strtotime($row['created_at'])) : '-'; ?></td>
                            <td>
                                <?php if ($row['volunteer_id']): ?>
                                    <button class="volunteer-link" onclick="showVolunteer(<?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['volunteer_name']); ?></button>
                                <?php else: ?>
                                    <span style="color:#999;">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['delivered_at'] ? date('d M Y, h:i A', strtotime($row['delivered_at'])) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo !empty($row['delivery_location']) ? htmlspecialchars($row['delivery_location']) : '-'; ?></td>
                            <td><span class="status-pill <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="empty-msg">No donation records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>

    <div class="modal-overlay" id="volunteerModal">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div id="modalContent">Loading...</div>
        </div>
    </div>

    <script>
        document.getElementById('filterInput').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#reportTable tbody tr').forEach(function (tr) {
                const haystack = tr.getAttribute('data-search') || '';
                tr.style.display = haystack.includes(term) ? '' : 'none';
            });
        });

        function showVolunteer(donationId) 
        {
            const modal = document.getElementById('volunteerModal');
            const content = document.getElementById('modalContent');
            content.innerHTML = 'Loading...';
            modal.classList.add('active');

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() 
            {
                if (this.readyState === 4) {
                    if (this.status === 200) 
                    {
                        try 
                        {
                            const data = JSON.parse(this.responseText);
                            if (data.error) 
                            {
                                content.innerHTML = '<p style="color:#c0392b;">' + data.error + '</p>';
                                return;
                            }
                            const initial = data.name ? data.name.substring(0, 2).toUpperCase() : '?';
                            const photoHtml = data.profile_pic
                                ? '<img class="modal-photo" src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/' + data.profile_pic + '" alt="Photo">'
                                : '<div class="modal-photo">' + initial + '</div>';

                            content.innerHTML = `
                                ${photoHtml}
                                <h3>${data.name}</h3>
                                <p style="color:#888;">Volunteer</p>
                                <div class="modal-row"><b>ID:</b> ${data.id}</div>
                                <div class="modal-row"><b>Phone:</b> ${data.phone || '-'}</div>
                                <div class="modal-row"><b>Age:</b> ${data.age !== null ? data.age + ' years' : '-'}</div>
                                <div class="modal-row"><b>Delivery:</b> ${data.food_type} (${data.status})</div>
                                <div class="modal-row"><b>Feedback:</b>
                                    <div class="feedback-box">${data.feedback ? data.feedback : 'No feedback yet.'}</div>
                                </div>
                            `;
                        } 
                        catch (e) 
                        {
                            content.innerHTML = '<p style="color:#c0392b;">Invalid response format.</p>';
                        }
                    } 
                    else 
                    {
                        content.innerHTML = '<p style="color:#c0392b;">Could not load volunteer details.</p>';
                    }
                }
            };
            xhttp.open("GET", "/Web_Technology%20Summer%2025-26/FoodShare/Controller/Admin/GetVolunteerProfile.php?donation_id=" + donationId, true);
            xhttp.send();
        }

        function closeModal() 
        {
            document.getElementById('volunteerModal').classList.remove('active');
        }

        document.getElementById('volunteerModal').addEventListener('click', function (e) 
        {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
