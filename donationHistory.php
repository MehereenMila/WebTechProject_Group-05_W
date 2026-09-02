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

// Shudhu delivered/completed donation gulo history te ashe, volunteer name soho
$sql = "SELECT d.*, v.name as volunteer_name
        FROM donations d
        LEFT JOIN users v ON d.volunteer_id = v.id
        WHERE d.donor_id = '$donor_id' AND d.status = 'Delivered'
        ORDER BY d.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History - FoodShare</title>
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
                <span class="role-badge" style="background: #206a37;">Donor</span>
            </div>

            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>

                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/createListing.php" class="menu-item"><span class="icon">➕</span> Create Food Listing</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/myListings.php" class="menu-item"><span class="icon">📋</span> My Listings</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/donationHistory.php" class="menu-item active"><span class="icon">⏱️</span> Donation History</a>

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
                <h1>Donation History</h1>
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
                <h2>Successfully Delivered Donations</h2>
                <p>A record of every donation that has reached someone.</p>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>Food Type</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Delivered By</th>
                            <th>Status</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['food_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo !empty($row['volunteer_name']) ? htmlspecialchars($row['volunteer_name']) : '—'; ?></td>
                                    <td><span class="status-badge">Delivered</span></td>
                                    <td>
                                        <?php if (!empty($row['feedback'])): ?>
                                            <span style="color:#2e8f46; font-style: italic;">"<?php echo htmlspecialchars($row['feedback']); ?>"</span>
                                        <?php else: ?>
                                            <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Donor/SubmitFeedbackController.php" method="POST" style="display:flex; gap:6px;">
                                                <input type="hidden" name="donation_id" value="<?php echo $row['id']; ?>">
                                                <input type="text" name="feedback" placeholder="Rate your volunteer..." required style="padding:6px; border-radius:4px; border:1px solid #ccc; width:160px;">
                                                <button type="submit" class="btn-update-status" style="background:#28a745; color:white; border:none; padding:6px 10px; border-radius:4px; cursor:pointer;">Send</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #777;">No completed donations yet.</td></tr>
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