<?php
// inquiries.php — Side-by-side Vehicle Requests & General Inquiries
session_start();
require_once 'php/db.php';
require_once 'php/session_check.php'; // should set $isLoggedIn, $isAdmin, $isVC

// OPTIONAL (helps debugging quietly): make PDO throw exceptions if not already
try { $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); } catch (Throwable $e) {}

$allowed_roles = ['admin','vc'];
$userRole = $_SESSION['user_role'] ?? '';
if (!$isLoggedIn || !in_array($userRole, $allowed_roles, true)) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

// ---- Allowed status options (hardcoded workflow) ----
$carReqStatuses = ['pending','in_progress','fulfilled','rejected'];
$inqStatuses    = ['new','in_progress','resolved'];

// ---- Handle inline status updates ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update car_requests status
    if (!empty($_POST['update_cr_id'])) {
        $id  = (int)$_POST['update_cr_id'];
        $new = trim($_POST['new_status_cr'] ?? '');

        if ($id <= 0) {
            header("Location: ".$_SERVER['PHP_SELF']."?cr_err=bad_id"); exit;
        }
        if (!in_array($new, $carReqStatuses, true)) {
            header("Location: ".$_SERVER['PHP_SELF']."?cr_err=bad_status"); exit;
        }
        try {
            $u = $pdo->prepare("UPDATE car_requests SET status = ?, updated_at = NOW() WHERE id = ?");
            $u->execute([$new, $id]);
            header("Location: ".$_SERVER['PHP_SELF']."?cr_updated=1"); exit;
        } catch (PDOException $e) {
            header("Location: ".$_SERVER['PHP_SELF']."?cr_err=db"); exit;
        }
    }

    // Update inquiries status
    if (!empty($_POST['update_inq_id'])) {
        $id  = (int)$_POST['update_inq_id'];
        $new = trim($_POST['new_status_inq'] ?? '');

        if ($id <= 0) {
            header("Location: ".$_SERVER['PHP_SELF']."?inq_err=bad_id"); exit;
        }
        if (!in_array($new, $inqStatuses, true)) {
            header("Location: ".$_SERVER['PHP_SELF']."?inq_err=bad_status"); exit;
        }
        try {
            $u = $pdo->prepare("UPDATE inquiries SET status = ?, updated_at = NOW() WHERE id = ?");
            $u->execute([$new, $id]);
            header("Location: ".$_SERVER['PHP_SELF']."?inq_updated=1"); exit;
        } catch (PDOException $e) {
            header("Location: ".$_SERVER['PHP_SELF']."?inq_err=db"); exit;
        }
    }
}

// ---- Fetch data (force default status if blank) ----
$carReqStmt = $pdo->query("
    SELECT id, user_id, vehicle_name, vehicle_type, budget, details,
           COALESCE(NULLIF(status,''), 'pending') AS status,
           created_at, updated_at
    FROM car_requests
    ORDER BY created_at DESC
");
$carRequests = $carReqStmt->fetchAll(PDO::FETCH_ASSOC);

$inqStmt = $pdo->query("
    SELECT  i.id, i.user_id, i.type, i.subject, i.message, i.car_id,
            COALESCE(NULLIF(i.status,''), 'new') AS status,
            i.created_at, i.updated_at,
            c.name  AS car_name,
            c.model AS car_model
    FROM inquiries i
    LEFT JOIN cars c ON c.id = i.car_id
    ORDER BY i.created_at DESC
");
$inquiries = $inqStmt->fetchAll(PDO::FETCH_ASSOC);


// ---- helpers ----
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function moneyfmt($n){ return ($n !== null && $n !== '') ? number_format((float)$n, 2, '.', ',') : '—'; }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inquiries | NordLion International</title>
  <link rel="icon" type="image/x-icon" href="img/logo-2.png">
  <link rel="stylesheet" href="css/onmarket.css" />
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    .two-col { display: grid; grid-template-columns: 1fr; gap: 40px; }
    @media (min-width: 992px) { .two-col { grid-template-columns: 1fr 1fr; } }

    .status-badge {
  display:inline-block;
  padding:4px 10px;
  border-radius:999px;
  font-size:.85rem;
  font-weight:700;
}

/* car_requests statuses */
.s-pending     { background:#fff3cd; color:#7a5c00; }   /* yellow */
.s-in_progress { background:#e7f1ff; color:#0a3c78; }   /* blue */
.s-fulfilled   { background:#d1ffe0; color:#0f6a2f; }   /* green */
.s-rejected    { background:#fdecea; color:#611a15; }   /* red */

/* inquiries statuses */
.s-new         { background:#f0e6ff; color:#4b267a; }   /* purple */
.s-resolved    { background:#ececec; color:#333; }      /* grey */


    .request-card .card-details p{margin:.25rem 0;}
    .request-card .card-title{margin-bottom:.25rem}
    .meta{color:#666;font-size:.9rem}
    .card-actions{display:flex;gap:8px;align-items:center;margin-top:10px}
    .card-actions select{padding:8px;border:1px solid #ddd;border-radius:6px}
    .btn-ghost{background:transparent;border:1px solid var(--primary-color);color:var(--primary-color);padding:8px 12px;border-radius:6px;cursor:pointer}
  </style>
</head>
<body>
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
        <?php if ($isVC): ?><li><a href="offmarket.php">Off-Market</a></li><?php endif; ?>
        <li><a href="offmarket.php">Off Market</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="team.php">Our Team</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if ($isLoggedIn): ?>
          <?php if ($isAdmin): ?><li><a href="dashboard.php">Admin Panel</a></li>
          <?php elseif ($isVC): ?><li><a href="vc_dashboard.php">VC Panel</a></li><?php endif; ?>
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

<main class="section-padding" style="max-width:1200px;margin:100px auto 0;padding:40px 20px;">
  <h1 class="section-heading">Client Inquiries</h1>
  <p class="subtitle" style="text-align:center; margin-bottom:30px;">Review inbound requests and general messages. Update statuses inline.</p>


  <div class="two-col">
    <!-- LEFT: Vehicle Requests (car_requests) -->
    <section>
      <h2 class="section-heading" style="margin-bottom:16px;">Vehicle Requests</h2>
      <div class="featured-grid">
        <?php if (!$carRequests): ?>
          <div class="car-card request-card"><div class="card-details"><h3 class="card-title">No vehicle requests</h3></div></div>
        <?php else: ?>
          <?php foreach ($carRequests as $r): ?>
            <div class="car-card request-card">
              <div class="card-details">
                <h3 class="card-title"><?php echo h($r['vehicle_name']); ?></h3>
                <p class="meta"><?php echo h($r['vehicle_type']); ?> • Budget: $<?php echo moneyfmt($r['budget']); ?></p>
                <p><?php echo nl2br(h($r['details'])); ?></p>

                <div style="margin-top:8px;">
                  <span class="status-badge s-<?php echo h($r['status']); ?>"><?php echo h(ucwords(str_replace('_',' ',$r['status']))); ?></span>
                  <span class="meta" style="margin-left:8px;">Created: <?php echo h($r['created_at']); ?></span>
                </div>

                <div class="card-actions">
                  <form method="post" action="inquiries.php">
                    <input type="hidden" name="update_cr_id" value="<?php echo h($r['id']); ?>">
                    <select name="new_status_cr" onchange="this.form.submit()">
                      <?php foreach ($carReqStatuses as $s): ?>
                        <option value="<?php echo h($s); ?>" <?php echo ($r['status']===$s?'selected':''); ?>>
                          <?php echo h(ucwords(str_replace('_',' ',$s))); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                  <a class="btn-ghost" href="mailto:info@nordlion.com?subject=Car%20Request%20-%20<?php echo rawurlencode($r['vehicle_name']); ?>">Email</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <!-- RIGHT: General Inquiries (inquiries) -->
    <section>
      <h2 class="section-heading" style="margin-bottom:16px;">General Inquiries</h2>
      <div class="featured-grid">
        <?php if (!$inquiries): ?>
          <div class="car-card request-card"><div class="card-details"><h3 class="card-title">No inquiries</h3></div></div>
        <?php else: ?>
          <?php foreach ($inquiries as $i): ?>
            <div class="car-card request-card">
              <div class="card-details">
                <h3 class="card-title"><?php echo h($i['subject'] ?: (ucwords($i['type']).' Inquiry')); ?></h3>
                  <?php if (!empty($i['car_id']) && !empty($i['car_name'])): ?>
                    <p class="meta">
                      Related Car: <?php echo h($i['car_name'] . ' ' . $i['car_model']); ?>
                      (ID #<?php echo h($i['car_id']); ?>)
                    </p>
                  <?php elseif (!empty($i['car_id'])): ?>
                    <p class="meta">Related Car ID: #<?php echo h($i['car_id']); ?></p>
                  <?php else: ?>
                    <p class="meta"><em>No specific car linked</em></p>
                  <?php endif; ?>
                <p><?php echo nl2br(h($i['message'])); ?></p>

                <div style="margin-top:8px;">
                  <span class="status-badge s-<?php echo h($i['status']); ?>"><?php echo h(ucwords(str_replace('_',' ',$i['status']))); ?></span>
                  <span class="meta" style="margin-left:8px;">Created: <?php echo h($i['created_at']); ?></span>
                </div>

                <div class="card-actions">
                  <form method="post" action="inquiries.php">
                    <input type="hidden" name="update_inq_id" value="<?php echo h($i['id']); ?>">
                    <select name="new_status_inq" onchange="this.form.submit()">
                      <?php foreach ($inqStatuses as $s): ?>
                        <option value="<?php echo h($s); ?>" <?php echo ($i['status']===$s?'selected':''); ?>>
                          <?php echo h(ucwords(str_replace('_',' ',$s))); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                  <a class="btn-ghost" href="mailto:info@nordlion.com?subject=Inquiry%20-%20<?php echo rawurlencode($i['subject'] ?: $i['type']); ?>">Email</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

<footer class="footer" style="background-color: #0F2C59;">
        <div class="container">
        <div class="footer-container">
            <div class="footer-brand">
            <div class="footer-logo">
                <div class="social-icons">
                <a href="https://www.instagram.com/the_nordlion_international/" target="_blank"><img src="img/insta.png" alt="Instagram"></a>
                <a href="https://www.linkedin.com/company/nordlion-international/?viewAsMember=true" target="_blank"><img src="img/linkedin.png" alt="LinkedIn"></a>
                </div>
                <img src="img/logo-2.png" alt="NordLion Logo">
                <span class="footer-logo-text">NordLion International</span>
            </div>
            <p class="footer-text">Excellence in luxury vehicle brokerage.</p>
            </div>

            <div class="footer-links">
            <h4 class="footer-heading">Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="onmarket.php">Cars</a></li>
                <li><a href="offmarket.php">Off Market</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            </div>

            <div class="footer-links">
            <h4 class="footer-heading">Services</h4>
            <ul>
                <li><a href="onmarket.php">Vehicle Acquisition</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="offmarket.php">Off-Market Access</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            </div>

            <div class="footer-links">
            <h4 class="footer-heading">Legal</h4>
            <ul>
                <li><a href="privacy.php">Privacy Policy</a></li>
                <li><a href="terms.php">Terms of Service</a></li>
                <li><a href="cookie.php">Cookie Policy</a></li>
            </ul>
            </div>
        </div>

        <div class="copyright">
            <p>&copy; 2025 NordLion International. All rights reserved.</p>
        </div>
        </div>
    </footer>

<script>
  // Header scroll behavior
  window.addEventListener('scroll', function () {
    const header = document.getElementById('header');
    if (window.scrollY > 50) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });
</script>
</body>
</html>
