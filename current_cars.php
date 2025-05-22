<?php
require 'php/db.php';

// Fetch all cars from the database
$stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Current Cars | NordLion Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
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

  <main class="jet-main">
    <section class="jet-overview">
      <h1 class="jet-title">Manage Current Cars</h1>
    </section>

    <?php if (count($cars) === 0): ?>
      <p style="text-align: center;">No cars available.</p>
    <?php else: ?>
    <section class="features-grid">
      <?php foreach ($cars as $car): ?>
        <div class="feature-card">
          <h3 class="feature-title"><?= htmlspecialchars($car['name']) . ' ' . htmlspecialchars($car['model']) ?></h3>
          <p class="feature-description">Price: €<?= number_format($car['price']) ?><br>Year: <?= htmlspecialchars($car['year']) ?></p>
          <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn btn-outline">Edit</a>
          <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-primary" onclick="return confirm('Are you sure you want to delete this car? This cannot be undone.');">Delete</a>
        </div>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>
  </main>
</body>
</html>
