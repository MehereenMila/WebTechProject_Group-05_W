<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';

// Security Check: Volunteer role check
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'volunteer') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$volunteer_id = $_SESSION['user_id'];
$volunteerName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Volunteer';
$volunteerInitial = strtoupper(substr($volunteerName, 0, 1));

$db = new DatabaseConnection();
$conn = $db->openConnection();

// Fetch Profile Pic
$userQuery = "SELECT * FROM users WHERE id = '$volunteer_id'";
$userResult = $conn->query($userQuery);
$userData = $userResult ? $userResult->fetch_assoc() : [];
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';

// Fetch Listings
$sql = "SELECT id, food_type, quantity, location, delivery_location, status, created_at FROM donations ORDER BY id DESC";
$result = $conn->query($sql);

$listings = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - Volunteer</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
</head>
<body class="dash-body">
    <div class="dash-container">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <h2>Food<span>Share</span></h2>
                <span class="role-badge" style="background: #28a745;">Volunteer</span>
            </div>
            <div class="sidebar-menu">
                <p class="menu-label">OVERVIEW</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/dashboard.php" class="menu-item"><span class="icon">📄</span> Dashboard</a>
                
                <p class="menu-label">MANAGEMENT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/myListings.php" class="menu-item active"><span class="icon">📦</span> My Listings</a>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Volunteer/history.php" class="menu-item"><span class="icon">⏱️</span> History</a>
                
                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>
            </div>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="background: #3eb55c; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo htmlspecialchars($volunteerInitial); ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <h4><?php echo htmlspecialchars($volunteerName); ?></h4>
                        <p>Volunteer</p>
                    </div>
                </div>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php" style="text-decoration: none;"><button class="btn-signout">Sign out</button></a>
            </div>
        </aside>

        <main class="main-content">
            <div class="management-header">
                <h1>My Listings</h1>
                <input class="search-box" id="searchBox" placeholder="Search food type or location...">
                <div class="header-profile" style="background: #3eb55c; overflow: hidden; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                    <?php if (!empty($profilePic)): ?>
                        <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo htmlspecialchars($volunteerInitial); ?>
                    <?php endif; ?>
                </div>
            </div>

            <p>View available food donations and their current status</p>

            <div class="listing-container" id="listingContainer">
                <?php if (isset($listings) && count($listings) > 0): ?>
                    <?php foreach ($listings as $listing): ?>
                        <div class="listing-card" data-search="<?php echo strtolower(htmlspecialchars($listing['food_type'] . ' ' . $listing['location'])); ?>">
                            <div class="food-icon">🍱</div>
                            <div class="listing-info">
                                <h3><?php echo htmlspecialchars($listing["food_type"]); ?></h3>
                                <p>Quantity: <?php echo htmlspecialchars($listing["quantity"]); ?></p>
                                <p>Pickup Location: <?php echo htmlspecialchars($listing["location"]); ?></p>
                                <p>Delivery Location: <?php echo htmlspecialchars($listing["delivery_location"]); ?></p>
                                <p>Donated On: <?php echo date("d M Y, h:i A", strtotime($listing["created_at"])); ?></p>
                            </div>
                            <span class="listing-status"><?php echo htmlspecialchars($listing["status"]); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="listing-card">
                        <p>No food listings available</p>
                    </div>
                <?php endif; ?>
            </div>

            <footer class="dash-footer">
                <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>

    <script>
        document.getElementById('searchBox').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.listing-card').forEach(function (card) {
                const haystack = card.getAttribute('data-search') || '';
                card.style.display = haystack.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>
</html>