<?php
require_once __DIR__ . '/../../Model/DatabaseConnection.php';
$database = new DatabaseConnection();
$conn = $database->openConnection();

// --- Live stats for the landing page ---
$mealsSharedQuery = "SELECT COUNT(*) as total FROM donations WHERE status = 'Delivered'";
$mealsSharedResult = $conn->query($mealsSharedQuery);
$mealsShared = $mealsSharedResult ? $mealsSharedResult->fetch_assoc()['total'] : 0;

$activeDonorsQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'donor'";
$activeDonorsResult = $conn->query($activeDonorsQuery);
$activeDonors = $activeDonorsResult ? $activeDonorsResult->fetch_assoc()['total'] : 0;

$volunteersQuery = "SELECT COUNT(*) as total FROM users WHERE role = 'volunteer'";
$volunteersResult = $conn->query($volunteersQuery);
$volunteers = $volunteersResult ? $volunteersResult->fetch_assoc()['total'] : 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare</title>
    <link rel="stylesheet" href="/Web_Technology%20Summer%2025-26/FoodShare/assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Food<span>Share</span></div>
        <ul class="nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <li><a href="#help">Help</a></li>
        </ul>
        <div class="nav-buttons">
          <a href="index.php?page=login"><button class="btn-signin" type="button">Sign In</button></a>
          <a href="index.php?page=register"><button class="btn-signup" type="button">Sign Up</button></a>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="hero-section">
        <div class="hero-content">
            <h1><span class="highlight">Welcome</span> To Our<br>Community</h1>
            <p class="tagline">Let's bridge the gap between surplus and shortage by turning neighborhood waste into community wellness</p>
        </div>

        <div class="hero-visual">
            <div class="give-badge">
                <!--<span class="hand-emoji">&#129330;</span>-->
                <span>Someone just shared a meal</span>
            </div>
        </div>
    </main>

    <!-- Stats Bar (live from database) -->
    <div class="stats-bar">
        <div>
            <h3><?php echo $mealsShared; ?>+</h3>
            <p>Meals Shared</p>
        </div>
        <div>
            <h3><?php echo $activeDonors; ?>+</h3>
            <p>Active Donors</p>
        </div>
        <div>
            <h3><?php echo $volunteers; ?>+</h3>
            <p>Volunteers</p>
        </div>
    </div>

    <!-- Food Showcase Section -->
    <section class="section">
        <h2>Food That Found A Home</h2>
        <p class="sub">A glimpse of what's being shared right now</p>
        <div class="food-grid">
            <div class="food-card">
                <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=400&auto=format&fit=crop" alt="Fresh bread">
                <div class="body">
                    <h4>Fresh Bread</h4>
                    <p>12 loaves &middot; Dhanmondi Bakery</p>
                </div>
            </div>
            <div class="food-card">
                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=400&auto=format&fit=crop" alt="Mixed vegetables">
                <div class="body">
                    <h4>Mixed Vegetables</h4>
                    <p>8 kg &middot; Green Grocer</p>
                </div>
            </div>
            <div class="food-card">
                <img src="https://images.unsplash.com/photo-1547592166-23ac45744acd?q=80&w=400&auto=format&fit=crop" alt="Cooked meals">
                <div class="body">
                    <h4>Cooked Meals</h4>
                    <p>30 packs &middot; Community Kitchen</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="section" id="about">
        <h2>How FoodShare Works</h2>
        <p class="sub">Three simple steps from surplus to someone's plate</p>
        <div class="steps">
            <div class="step">
                <div class="num">01</div>
                <h4>Donor Posts Food</h4>
                <p>Restaurants and homes list surplus food with pickup time.</p>
                <span class="arrow-flow">&rarr;</span>
            </div>
            <div class="step">
                <div class="num">02</div>
                <h4>Manager Assigns</h4>
                <p>A coordinator matches it to the nearest volunteer.</p>
                <span class="arrow-flow">&rarr;</span>
            </div>
            <div class="step">
                <div class="num">03</div>
                <h4>Volunteer Delivers</h4>
                <p>Food reaches shelters and families before it's wasted.</p>
            </div>
        </div>
    </section>

    <!-- Why Choose FoodShare -->
    <section class="section">
        <h2>Why Choose FoodShare</h2>
        <p class="sub">A simple way to turn extra food into someone's next meal</p>
        <div class="why-grid">
            <div class="why-card">
                <span class="why-icon">&#127807;</span>
                <h4>Cut Food Waste</h4>
                <p>Restaurants, homes, and events redirect surplus food instead of throwing it away.</p>
            </div>
            <div class="why-card">
                <span class="why-icon">&#129309;</span>
                <h4>Help Your Neighbors</h4>
                <p>Every donation goes straight to shelters and families who need it most.</p>
            </div>
            <div class="why-card">
                <span class="why-icon">&#9201;&#65039;</span>
                <h4>Fast & Simple</h4>
                <p>Post a donation in under a minute — pickup time, location, and details.</p>
            </div>
            <div class="why-card">
                <span class="why-icon">&#128737;&#65039;</span>
                <h4>Verified Volunteers</h4>
                <p>Every pickup and delivery is handled by a checked, trusted volunteer.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section">
        <h2>From Our Community</h2>
        <p class="sub">Real stories from donors and volunteers</p>
        <div class="testimonials">
            <div class="t-card">
                <img src="/Web_Technology%20Summer%2025-26/FoodShare/assets/images/avatar-donor.svg" alt="Mereen Khan Mila">
                <div>
                    <p class="quote">"The trays of food we used to throw away every night, now it feeds a shelter across town."</p>
                    <b>Mehreen Khan Mila</b>
                    <span>Restaurant Donor</span>
                </div>
            </div>
            <div class="t-card">
                <img src="/Web_Technology%20Summer%2025-26/FoodShare/assets/images/avatar-volunteer.svg" alt="Shuvo Ghosh">
                <div>
                    <p class="quote">"Picking up and delivering at least a few portion of meals and it means a family get something to eat tonight."</p>
                    <b>Shuvo Ghosh</b>
                    <span>Volunteer</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner -->
    <div class="cta-banner">
        <h2>Ready to make a difference?</h2>
        <p>Join <?php echo ($activeDonors + $volunteers); ?>+ donors and volunteers already reducing food waste in your city</p>
        <a href="/Web_Technology%20Summer%2025-26/FoodShare/index.php?page=register"><button type="button">Get Started</button></a>
    </div>

    <!-- Footer -->
    <footer id="contact">
        <p>@ 2026 FoodShare - Food Waste Reduction & Redistribution System</p>
    </footer>
</body>
</html>