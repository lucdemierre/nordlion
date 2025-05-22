<?php
require 'php/db.php';

if (!isset($_GET['id'])) {
    die("Car ID not specified.");
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found.");
}

// Fetch gallery images for this car
$imageStmt = $pdo->prepare("SELECT image_path, caption FROM car_images WHERE car_id = ?");
$imageStmt->execute([$id]);
$gallery_images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize empty array if no images found
if (!$gallery_images) {
    $gallery_images = [];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($car['name'] . ' ' . $car['model']); ?> | NordLion International</title>
  <link rel="icon" type="image/x-icon" href="img/logo-2.png">
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Lato:wght@300;400;500;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <header id="header">
    <div class="container header-container">
      <a href="index.html" class="logo">
        <img src="img/logo-2.png" alt="NordLion Logo">
        <span class="logo-text">NordLion International</span>
      </a>
      <nav>
        <ul id="nav-menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="onmarket.php">Cars</a></li>
            <li><a href="jets.html">Jets</a></li>
            <li><a href="about.html">About Us</a></li>
            <li><a href="team.html">Team</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="login.html">Login</a></li>
        </ul>
        <button class="mobile-menu-btn" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
      </nav>
    </div>
  </header>

  <!-- Enhanced Image Gallery -->
  <div class="gallery-container">
    <!-- Gallery Navigation Arrows -->
    <button class="gallery-arrow left" onclick="prevSlide()" aria-label="Previous image">
      <svg class="arrow-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <button class="gallery-arrow right" onclick="nextSlide()" aria-label="Next image">
      <svg class="arrow-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    
    <!-- Gallery Images -->
    <div class="gallery-slides" id="gallerySlides">
      <?php foreach ($gallery_images as $idx => $img): ?>
      <div class="gallery-slide <?php echo ($idx === 0) ? 'active' : ''; ?>">
        <img 
          src="uploads/<?php echo htmlspecialchars(basename($img['image_path'])); ?>" 
          alt="<?php echo htmlspecialchars($car['name'] . ' ' . $car['model']) . ' image ' . ($idx + 1); ?>" 
        />
        <?php if (!empty($img['caption'])): ?>
        <div class="gallery-caption"><?php echo htmlspecialchars($img['caption']); ?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    
    <!-- Gallery Pagination -->
    <div class="gallery-pagination" id="galleryPagination">
      <?php for ($i = 0; $i < count($gallery_images); $i++): ?>
      <button 
        class="gallery-dot <?php echo ($i === 0) ? 'active' : ''; ?>" 
        onclick="goToSlide(<?php echo $i; ?>)" 
        aria-label="Go to slide <?php echo $i + 1; ?>"
      ></button>
      <?php endfor; ?>
    </div>
  </div>
  


  <!-- Car Info -->
  <main class="jet-main">
    <section class="jet-overview">
      <div class="overview-content">
        <h1 class="jet-title"><?php echo htmlspecialchars($car['name'] . ' ' . $car['model']); ?></h1>
        <div class="jet-price">€<?php echo number_format($car['price']); ?></div>
        <p class="jet-description">
          <?php echo nl2br(htmlspecialchars($car['description'])); ?>
        </p>
      </div>
    </section>

    <section class="jet-specifications">
      <h2 class="section-title">Key Specifications</h2>
      <div class="specs-grid">
        <div class="spec-item">
          <div class="spec-icon">🏁</div>
          <div class="spec-content">
            <h3 class="spec-title">Year</h3>
            <div class="spec-value"><?php echo htmlspecialchars($car['year']); ?></div>
          </div>
        </div>
        <div class="spec-item">
          <div class="spec-icon">⚙️</div>
          <div class="spec-content">
            <h3 class="spec-title">Mileage</h3>
            <div class="spec-value"><?php echo number_format($car['mileage']); ?> km</div>
          </div>
        </div>
        <div class="spec-item">
          <div class="spec-icon">🎨</div>
          <div class="spec-content">
            <h3 class="spec-title">Interior Colour</h3>
            <div class="spec-value"><?php echo htmlspecialchars($car['int_colour']); ?></div>
          </div>
        </div>
        <div class="spec-item">
          <div class="spec-icon">🚗</div>
          <div class="spec-content">
            <h3 class="spec-title">Exterior Colour</h3>
            <div class="spec-value"><?php echo htmlspecialchars($car['ext_colour']); ?></div>
          </div>
        </div>
      </div>
    </section>

    <?php if (!empty($car['features'])): ?>
  <section class="jet-features">
    <h2 class="section-title">Exceptional Features</h2>
    <ul class="features-grid">
      <?php foreach (explode("\n", trim($car['features'])) as $feature): ?>
        <?php if (trim($feature)): ?>
          <li class="feature-card"><?php echo htmlspecialchars($feature); ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>
  </main> <!-- Close the main tag before inquiry section -->

  <!-- Inquiry Section -->
  <section class="inquiry-section">
    <div class="inquiry-left">
      <div class="inquiry-form">
        <h2>Enquire About <?php echo htmlspecialchars($car['name'] . ' ' . $car['model']); ?></h2>
        <form action="php/submit_inquiry.php" method="POST" autocomplete="on" aria-label="Enquiry form for <?php echo htmlspecialchars($car['name'] . ' ' . $car['model']); ?>">
          <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
          <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-input" required autocomplete="name" />
          </div>
          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" />
          </div>
          <div class="form-group">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-input" autocomplete="tel" />
          </div>
          <div class="form-group">
            <label for="message" class="form-label">Message</label>
            <textarea id="message" name="message" class="form-textarea" rows="5" required aria-label="Message">I'm interested in the <?php echo htmlspecialchars($car['name'] . ' ' . $car['model']); ?>.</textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Send Inquiry</button>
        </form>
      </div>
    </div>
    <div class="inquiry-right">
      <div class="testimonial-container">
        <blockquote class="testimonial">
          <p>"The CC850 is a true driver's car – the manual gearbox is a work of art."</p>
          <footer>— Hypercar Collector, Monaco</footer>
        </blockquote>
        <blockquote class="testimonial">
          <p>"A perfect blend of nostalgia and innovation. Koenigsegg has outdone themselves."</p>
          <footer>— Automotive Journalist, London</footer>
        </blockquote>
        <blockquote class="testimonial">
          <p>"The attention to detail and performance is simply unmatched."</p>
          <footer>— Supercar Owner, Dubai</footer>
        </blockquote>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-logo">
                  <div class="social-icons">
                    <a href="https://www.instagram.com/the_nordlion_international/" target="_blank" aria-label="Instagram">
                      <img src="img/insta.png" alt="Instagram Logo" />
                    </a>
                    <a href="https://www.linkedin.com/company/nordlion-international/" target="_blank" aria-label="LinkedIn">
                      <img src="img/linkedin.png" alt="LinkedIn Logo" />
                    </a>
                  </div>
                  <img src="img/logo-2.png" alt="NordLion Logo" />
                  <span class="footer-logo-text">NordLion International</span>
                </div>
                <p class="footer-text">
                  Connecting discerning clients with the world's most extraordinary vehicles and jets.
                </p>
            </div>
            <div class="footer-links">
                <h4 class="footer-heading">Quick Links</h4>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="onmarket.php">Cars</a></li>
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

  <script src="scripts.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Get all slides and navigation elements
    const slides = document.querySelectorAll('.gallery-slide');
    const dots = document.querySelectorAll('.gallery-dot');
    const slidesContainer = document.getElementById('gallerySlides');
    const totalSlides = slides.length;
    
    // Track the current state
    let currentIndex = 0;
    let isAnimating = false;
    
    // Initialize the gallery
    initializeGallery();
    
    // Set up the initial state of the gallery
    function initializeGallery() {
      if (totalSlides === 0) return;
      
      // Position all slides with transitions already applied
      slides.forEach((slide, index) => {
        // Set initial styles without transitions first
        slide.style.transition = 'none';
        slide.style.position = 'absolute';
        slide.style.left = index === 0 ? '0%' : '100%';
        slide.style.opacity = index === 0 ? '1' : '0';
        slide.style.display = 'block';
        slide.style.zIndex = index === 0 ? '2' : '1';
      });
      
      // Force browser reflow before enabling transitions
      void document.body.offsetHeight;
      
      // Now enable transitions for smooth animations
      slides.forEach(slide => {
        slide.style.transition = 'opacity 0.5s ease';
      });
      
      // Set the first slide as active
      slides[0].classList.add('active');
      if (dots.length > 0) dots[0].classList.add('active');
      
      // Set up event listeners
      document.querySelector('.gallery-arrow.right').addEventListener('click', nextSlide);
      document.querySelector('.gallery-arrow.left').addEventListener('click', prevSlide);
      
      // Set up dot navigation
      dots.forEach((dot, i) => {
        dot.addEventListener('click', () => goToSlide(i));
      });
    }
    
    // NEXT: Always animate from right to left (consistent direction)
    function nextSlide() {
      if (isAnimating || totalSlides <= 1) return;
      
      // Calculate next slide index with wraparound
      const nextIndex = (currentIndex + 1) % totalSlides;
      
      // Always animate next from right to left
      animateSlideFromRight(currentIndex, nextIndex);
    }
    
    // PREV: Always animate from left to right (consistent direction)
    function prevSlide() {
      if (isAnimating || totalSlides <= 1) return;
      
      // Calculate previous slide index with wraparound
      const prevIndex = (currentIndex - 1 + totalSlides) % totalSlides;
      
      // Always animate prev from left to right
      animateSlideFromLeft(currentIndex, prevIndex);
    }
    
    // Handle dot navigation
    function goToSlide(index) {
      if (isAnimating || index === currentIndex || index < 0 || index >= totalSlides) return;

      // Choose animation direction based on index relationship
      if (index > currentIndex || (index === 0 && currentIndex === totalSlides - 1)) {
        // Going forward or from last to first - animate from right
        animateSlideFromRight(currentIndex, index);
      } else {
        // Going backward or from first to last - animate from left
        animateSlideFromLeft(currentIndex, index);
      }
    }
    
    // Simple fade transition instead of sliding
    function animateSlideFromRight(fromIndex, toIndex) {
      isAnimating = true;
      
      // Update the dots immediately for better responsiveness
      updateDots(toIndex);
      
      // Get the slides
      const currentSlide = slides[fromIndex];
      const nextSlide = slides[toIndex];
      
      // Reset any previous transitions
      slides.forEach(slide => {
        if (slide !== currentSlide && slide !== nextSlide) {
          slide.style.display = 'none';
          slide.style.opacity = '0';
        }
      });
      
      // Position both slides in the same spot - no left/right movement
      currentSlide.style.position = 'absolute';
      nextSlide.style.position = 'absolute';
      currentSlide.style.left = '0';
      nextSlide.style.left = '0';
      
      // Set z-index to control stacking
      currentSlide.style.zIndex = '1';
      nextSlide.style.zIndex = '2';
      
      // Make current slide visible, next slide invisible
      currentSlide.style.opacity = '1';
      nextSlide.style.opacity = '0';
      nextSlide.style.display = 'block';
      
      // Set up the transition
      nextSlide.style.transition = 'opacity 0.5s ease';
      
      // Force browser reflow
      void nextSlide.offsetWidth;
      
      // Fade in the next slide
      nextSlide.style.opacity = '1';
      
      // Update state after animation completes
      setTimeout(() => {
        updateGalleryState(toIndex);
        isAnimating = false;
      }, 550);
    }
    
    // Use the same fade transition for consistency
    function animateSlideFromLeft(fromIndex, toIndex) {
      // Use the same fade animation for both directions
      animateSlideFromRight(fromIndex, toIndex);
    }
    
    // Function to update only the dot indicators immediately
    function updateDots(newIndex) {
      // Update just the active dots right away
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === newIndex);
      });
    }
    
    // Update active states and current index
    function updateGalleryState(newIndex) {
      // Update active slide
      slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === newIndex);
      });
      
      // Update current index
      currentIndex = newIndex;
    }
    
    // Make functions available for inline onclick handlers
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;
    window.goToSlide = goToSlide;
  });
</script>


</body>
</html>
