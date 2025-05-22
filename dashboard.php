<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.html");
    exit;
}
require 'php/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - NordLion International</title>
    <link rel="icon" type="image/x-icon" href="img/logo-2.png">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Lato:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Header and Navigation -->
    <header id="header">
        <div class="header-container">
            <a href="index.html" class="logo">
                <img src="img/logo-2.png" alt="NordLion Logo">
                <span class="logo-text">NordLion International</span>
            </a>
            <nav>
                <ul id="nav-menu">
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="inquiries.php">Inquiries</a></li>
                    <li><a href="onmarket.html">Cars</a></li>
                    <li><a href="jets.html">Jets</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
                <button class="mobile-menu-btn" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero" style="background-image: url('img/hp-img.jpg');">
            <div class="hero-content">
                <h1 class="hero-title">Welcome to Your Dashboard, <?php 
                    if(isset($_SESSION['user_name'])) {
                        $nameParts = explode(' ', $_SESSION['user_name']);
                        echo $nameParts[0]; 
                    } else {
                        echo 'User';
                    }
                ?>!</h1>
            </div>
        </section>

        <section class="dashboard section-padding">
            <div class="container">
                <h1 class="section-heading">Your Tools</h1>
                <div class="dashboard-grid">
                    <div class="card">
                        <h2 class="card-title">Your Profile</h2>
                        <p class="card-description">Manage your personal information and settings.</p>
                        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                    <div class="card">
                        <h2 class="card-title">Recent Inquiries</h2>
                        <p class="card-description">View your recent vehicle inquiries and status.</p>
                        <a href="inquiries.php" class="btn btn-primary">View Inquiries</a>
                    </div>
                    <div class="card">
                        <h2 class="card-title">Add Cars to On-Market</h2>
                        <p class="card-description">Add new cars to the on-market sector.</p>
                        <a href="add_car_onmarket.php" class="btn btn-primary">Add Car</a>
                    </div>
                    <div class="card">
                        <h2 class="card-title">Add Cars to Off-Market</h2>
                        <p class="card-description">Add new cars to the off-market sector.</p>
                        <a href="add_car_offmarket.php" class="btn btn-primary">Add Car</a>
                    </div>
                    <div class="card">
                        <h2 class="card-title">Add Jets</h2>
                        <p class="card-description">Add new jets to the inventory.</p>
                        <a href="add_jet.php" class="btn btn-primary">Add Jet</a>
                    </div>

                    <div class="card">
                        <h2 class="card-title">Current Cars</h2>
                        <p class="card-description">Current cars in the inventory.</p>
                        <a href="current_cars.php" class="btn btn-primary">Current Cars</a>
                    </div>

                    <div class="card">
                        <h2 class="card-title">Current Deals</h2>
                        <p class="card-description">Current deals occuring.</p>
                        <a href="current_deals.php" class="btn btn-primary">Current Deals</a>
                    </div>
                </div>
            </div>
        </section>
        <section class="pending section-padding" style="margin-top: 60px;">
            <div class="container">
                <h2 class="section-heading">Pending Car Submissions</h2>
                <?php
                $stmt = $pdo->query("SELECT * FROM cars WHERE status = 'pending'");
                $cars = $stmt->fetchAll();

                if (count($cars) === 0) {
                    echo "<p style='text-align: center;'>No pending submissions.</p>";
                } else {
                    echo '<div style="max-width: 800px; margin: 0 auto;">';
                    foreach ($cars as $car) {
                        echo '<div class="car-card">';
                        echo "<strong>" . htmlspecialchars($car['name']) . " " . htmlspecialchars($car['model']) . "</strong><br>";
                        echo "Price: €" . htmlspecialchars($car['price']) . " | Year: " . $car['year'] . " | Mileage: " . $car['mileage'] . " km<br>";
                        echo '<form method="POST" action="php/approve_car.php" style="margin-top: 10px; display: flex; justify-content: center; gap: 10px;">';
                        echo '<input type="hidden" name="car_id" value="' . $car['id'] . '">';
                        echo '<button type="submit" name="action" value="approve" class="btn btn-primary" style="background-color: #28a745;">Approve</button> ';
                        echo '<button type="submit" name="action" value="reject" class="btn btn-primary" style="background-color: #dc3545;">Reject</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <div class="social-icons">
                            <a href="https://www.instagram.com/the_nordlion_international/" target="_blank"><img src="img/insta.png" alt="Instagram"></a>
                            <a href="https://www.linkedin.com/company/nordlion-international/" target="_blank"><img src="img/linkedin.png" alt="LinkedIn"></a>
                        </div>
                        <img src="img/logo-2.png" alt="NordLion Logo">
                        <span class="footer-logo-text">NordLion International</span>
                    </div>
                    <p class="footer-text">Excellence in luxury vehicle brokerage</p>
                </div>
                
                <div class="footer-links">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="onmarket.html">Cars</a></li>
                        <li><a href="jets.html">Jets</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4 class="footer-heading">Services</h4>
                    <ul>
                        <li><a href="#">Vehicle Acquisition</a></li>
                        <li><a href="#">Jet Brokerage</a></li>
                        <li><a href="#">Off-Market Access</a></li>
                        <li><a href="#">Investment Consulting</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4 class="footer-heading">Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 NordLion International. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>