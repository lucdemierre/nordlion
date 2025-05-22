<?php
session_start();
require_once 'php/db.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';

// Get first name if user is logged in
$firstName = '';
if ($isLoggedIn && !empty($userName)) {
    $nameParts = explode(' ', $userName);
    $firstName = $nameParts[0];
}

// Check which section to display
$showRequestForm = false;
$showInvestmentForm = false;
if ($userRole === 'vc') { // Changed from $isVC to $userRole === 'vc'
    if (isset($_GET['section'])) {
        if ($_GET['section'] === 'request') {
            $showRequestForm = true;
        } elseif ($_GET['section'] === 'investment') {
            $showInvestmentForm = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - NordLion International</title>
    <link rel="icon" type="image/x-icon" href="img/logo-2.png">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Lato:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, html { margin: 0; padding: 0; overflow-x: hidden; }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <header id="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <img src="img/logo-2.png" alt="NordLion Logo">
                <span class="logo-text">NordLion International</span>
            </a>
            <nav>
                <ul id="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="onmarket.php">Cars</a></li>
                    <li><a href="jets.html">Jets</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="team.html">Our Team</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRole === 'admin'): ?>
                            <li><a href="dashboard.php">Admin Panel</a></li>
                        <?php elseif ($userRole === 'vc'): ?>
                            <li><a href="vc_dashboard.php">VC Panel</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.html">Login</a></li>
                    <?php endif; ?>
                </ul>
                <button class="mobile-menu-btn" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>
    <main>
        <section class="about-hero" style="background: linear-gradient(rgba(15, 44, 89, 0.8), rgba(15, 44, 89, 0.8)), url('../img/contact-1.jpg'); background-size: cover; background-position: center; color: #fff; text-align: center; padding: 160px 0 100px; margin-top: 0;">
            <div class="container">
                <h1>Contact Us</h1>
                <p class="subtitle">GET IN TOUCH WITH OUR TEAM</p>
            </div>
        </section>

        <section class="contact section-padding" id="contact">
            <div class="container">
                <?php if ($isLoggedIn): ?>
                <!-- Two-column layout with form for logged-in users -->
                <div class="contact-container">
                    <div class="contact-form">
                        <form action="php/contact.php" method="POST">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($userName); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Your Message</label>
                                <textarea id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                        <a href="https://www.instagram.com/the_nordlion_international/" target="_blank"><img src="img/insta.png" alt="Instagram"></a>
                        <a href="https://www.linkedin.com/company/nordlion-international/" target="_blank"><img src="img/linkedin.png" alt="LinkedIn"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="js/main.js"></script>

<!-- Form submission script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.contact-form');
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                submitButton.textContent = 'Sending...';
                submitButton.disabled = true;

                const formData = new FormData(form);
                const response = await fetch('php/contact.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    form.reset();
                    alert('Message sent successfully!');
                } else {
                    alert('Error sending message: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
            }
        });
    });
</script>
</body>
</html>

<?php if ($isLoggedIn): ?>
    <!-- Two-column layout with form for logged-in users -->
    <div class="contact-container">
        <div class="contact-form">
            <form action="php/contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($userName); ?>">
                    <select id="timeline" name="timeline" required>
                        <option value="">Select Timeline</option>
                        <option value="short">Short-term (1-2 years)</option>
                        <option value="medium">Medium-term (3-5 years)</option>
                        <option value="long">Long-term (5+ years)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Investment Goals and Additional Details</label>
                    <textarea id="message" name="message" rows="5" required placeholder="Please describe your investment goals, preferences, and any specific vehicles or markets you're interested in."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Inquiry</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if ($showRequestForm && $userRole === 'vc'): ?>
    <section class="vc-form-section">
        <h2>Request a Specific Vehicle</h2>
        <p>Our extensive global network allows us to source even the rarest vehicles for our valued clients. Please provide details about the vehicle you're looking for.</p>
        
        <div class="form-container">
            <form action="php/car_request.php" method="POST" class="vc-form">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                
                <div class="form-group">
                    <label for="vehicle_name">Vehicle Name/Model</label>
                    <input type="text" id="vehicle_name" name="vehicle_name" required placeholder="e.g., Ferrari 250 GTO, Koenigsegg Jesko">
                </div>
                
                <div class="form-group">
                    <label for="vehicle_type">Vehicle Type</label>
                    <select id="vehicle_type" name="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        <option value="hypercar">Hypercar</option>
                        <option value="supercar">Supercar</option>
                        <option value="luxury">Luxury Car</option>
                        <option value="classic">Classic/Vintage</option>
                        <option value="limited">Limited Edition</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="budget">Budget (€)</label>
                    <input type="text" id="budget" name="budget" required placeholder="e.g., 1,500,000">
                </div>
                
                <div class="form-group">
                    <label for="details">Specific Requirements</label>
                    <textarea id="details" name="details" rows="5" required placeholder="Please provide any specific details about the vehicle you're looking for, such as year, condition, specifications, color preferences, etc."></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Submit Request</button>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php if ($showInvestmentForm && $userRole === 'vc'): ?>
    <section class="vc-form-section">
        <h2>Investment Inquiry</h2>
        <p>Our team specializes in facilitating high-value automotive investments with strong appreciation potential. Please share your investment criteria below.</p>
        
        <div class="form-container">
            <form action="php/investment_inquiry.php" method="POST" class="vc-form">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                
                <div class="form-group">
                    <label for="investment_type">Investment Type</label>
                    <select id="investment_type" name="investment_type" required>
                        <option value="">Select Investment Type</option>
                        <option value="single_vehicle">Single Vehicle Investment</option>
                        <option value="collection">Collection Investment</option>
                        <option value="fractional">Fractional Ownership</option>
                        <option value="dealership">Dealership Partnership</option>
                        <option value="other">Other Opportunity</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="budget_range">Budget Range (€)</label>
                    <input type="text" id="budget_range" name="budget_range" required placeholder="e.g., 1,000,000 - 5,000,000">
                </div>
                
                <div class="form-group">
                    <label for="timeline">Investment Timeline</label>
                    <select id="timeline" name="timeline" required>
                        <option value="">Select Timeline</option>
                        <option value="short">Short-term (1-2 years)</option>
                        <option value="medium">Medium-term (3-5 years)</option>
                        <option value="long">Long-term (5+ years)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Investment Goals and Additional Details</label>
                    <textarea id="message" name="message" rows="5" required placeholder="Please describe your investment goals, preferences, and any specific vehicles or markets you're interested in."></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Submit Inquiry</button>
            </form>
        </div>
    </section>
<?php endif; ?>

<!-- Office Locations Section -->
<div class="office-locations" style="margin-top: 80px;">
    <h2 class="section-heading">Our Offices</h2>
    <div class="locations-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
        <div class="location-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--box-shadow);">
            <h3 style="margin-bottom: 15px; font-size: 1.3rem;">London Office</h3>
            <p style="margin-bottom: 10px;">United Kingdom</p>
            <p style="color: var(--secondary-color);">+44 7947 977474</p>
        </div>
        
        <div class="location-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--box-shadow);">
            <h3 style="margin-bottom: 15px; font-size: 1.3rem;">Turku Office</h3>
            <p style="margin-bottom: 10px;">Finland</p>
            <p style="color: var(--secondary-color);">+358 40 0186049</p>
        </div>
        
        <div class="location-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--box-shadow);">
            <h3 style="margin-bottom: 15px; font-size: 1.3rem;">Singapore Office</h3>
            <p style="margin-bottom: 10px;">Singapore</p>
            <p style="color: var(--secondary-color);">+65 8333 0905 (Whatsapp)</p>
        </div>
    </div>
</div>

<!-- Business Hours Section -->
<div class="business-hours" style="margin-top: 80px; text-align: center;">
    <h2 class="section-heading">Business Hours</h2>
    <div class="hours-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 40px;">
        <div class="hours-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--box-shadow);">
            <h3 style="margin-bottom: 15px; font-size: 1.3rem;">Monday - Friday</h3>
            <p>4:00 PM - 10:00 PM GMT</p>
        </div>
        
        <div class="hours-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--box-shadow);">
            <h3 style="margin-bottom: 15px; font-size: 1.3rem;">Saturday</h3>
            <p>4:00 PM - 8:00 PM GMT</p>
        </div>
        
        <div class="hours-card" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--box-shadow);">
            <h3 style="margin-bottom: 15px; font-size: 1.3rem;">Sunday</h3>
            <p>10:30 AM - 11:00 PM GMT</p>
        </div>
    </div>
</div>
</div>
</section>
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-content">
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="onmarket.php">Cars</a></li>
                    <li><a href="jets.html">Jets</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
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
        // Add scroll behavior for navbar
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        <?php if ($isLoggedIn): ?>
        // Contact form submission
        document.getElementById('inquiry-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.textContent = 'Sending...';
            submitButton.disabled = true;

            try {
                const formData = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    subject: document.getElementById('subject').value,
                    message: document.getElementById('message').value
                };

                const response = await fetch('http://localhost:3000/api/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Message sent successfully! We will get back to you soon.');
                    this.reset();
                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>