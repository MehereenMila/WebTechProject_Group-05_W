<?php
session_start();
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'donor') {
    header("Location: /Web_Technology%20Summer%2025-26/FoodShare/View/Auth/login.php");
    exit();
}

$userName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Donor';
$userInitial = strtoupper(substr($userName, 0, 1));

$donor_id = $_SESSION['user_id'];
$userQuery = "SELECT profile_pic FROM users WHERE id = '$donor_id'";
$userResult = $conn->query($userQuery);
$userData = $userResult->fetch_assoc();
$profilePic = isset($userData['profile_pic']) ? $userData['profile_pic'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Request - FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
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
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Donor/createListing.php" class="menu-item active"><span class="icon">➕</span> Create Food Listing</a>
                <a href="#" class="menu-item"><span class="icon">📋</span> My Listings</a>
                <a href="#" class="menu-item"><span class="icon">⏱️</span> Donation History</a>

                <p class="menu-label">ACCOUNT</p>
                <a href="/Web_Technology%20Summer%2025-26/FoodShare/View/Auth/profile.php" class="menu-item"><span class="icon">👤</span> Profile</a>            </div>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="avatar" style="background: #3eb55c; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
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
                <h1>Donation Request</h1>
                <div class="topbar-right">
                    <button class="btn-refresh">🔄 Refresh</button>
                    <button class="btn-notification">🔔</button>
                    <div class="topbar-avatar" style="background: #3eb55c; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($profilePic)): ?>
                            <img src="/Web_Technology%20Summer%2025-26/FoodShare/uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $userInitial; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Donation Form Area -->
            <div class="donation-form-wrapper">
                <!-- ENCTYPE added for image upload support -->
                <form action="/Web_Technology%20Summer%2025-26/FoodShare/Controller/Donor/AddFoodController.php" method="POST" enctype="multipart/form-data">
                    <div class="donate-input-box margin-bot">
                        <label>📦 Pickup Day</label>
                        <div class="pickup-options">
                            <label><input type="radio" name="pickup_day" value="Today" checked> Today</label>
                            <label><input type="radio" name="pickup_day" value="Tomorrow"> Tomorrow</label>
                            <label><input type="radio" name="pickup_day" value="Day After"> Day After</label>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="donate-input-box">
                            <label>🍱 Food Type</label>
                            <input type="text" name="food_type" placeholder="e.g. Veg Sahi Pulaw" required>
                        </div>
                        <div class="donate-input-box">
                            <label>📊 Food Quantity (In Person)</label>
                            <input type="text" name="quantity" placeholder="e.g. 50 Person" required>
                        </div>
                    </div>

                    <div class="donate-input-box margin-bot">
                        <label>📷 Add Photo (Upload Image)</label>
                        <input type="file" name="food_image" accept="image/*" style="padding: 10px; background: #0f3b1b; color: white; border: 1px solid #2e8f46; border-radius: 8px; width: 100%;">
                    </div>

                    <div class="form-grid-4">
                        <div class="donate-input-box">
                            <label>📅 Cooking Date</label>
                            <input type="date" name="cooking_date" required>
                        </div>
                        <div class="donate-input-box">
                            <label>⏰ Cooking Time</label>
                            <input type="time" name="cooking_time" required>
                        </div>
                        <div class="donate-input-box">
                            <label>📅 Expiry Date</label>
                            <input type="date" name="expiry_date" required>
                        </div>
                        <div class="donate-input-box">
                            <label>⏰ Expiry Time</label>
                            <input type="time" name="expiry_time" required>
                        </div>
                    </div>

                    <div class="donate-input-box margin-bot">
                        <label>📝 Food Details</label>
                        <input type="text" name="details" placeholder="Type here...">
                    </div>

                    <div class="donate-input-box margin-bot">
                        <label>📍 Pickup Location </label>
                        <input type="text" id="pickup_location" name="location" placeholder="e.g. Jannat Restaurant, Dhaka..." required>
                    </div>

                    <button type="submit" class="btn-donate-submit">Submit Request</button>
                </form>
            </div>
            
            <footer class="dash-footer">
                <p style="color: #999;">@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
            </footer>
        </main>
    </div>
</body>
</html>