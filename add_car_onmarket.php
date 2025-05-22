<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'vc', 'mop'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Car to On-Market | NordLion International</title>
  <link rel="icon" href="img/logo-2.png" type="image/x-icon">
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=Lato:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    .image-entry {
      border: 1px solid #ccc;
      padding: 15px;
      margin-bottom: 15px;
      position: relative;
      border-radius: 6px;
    }

    .image-entry textarea {
      width: 100%;
      margin-top: 10px;
    }

    .image-entry .remove-btn {
      position: absolute;
      top: 8px;
      right: 8px;
      background: transparent;
      border: none;
      font-size: 1.2rem;
      cursor: pointer;
      color: #c00;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .form-group input, .form-group textarea {
      width: 100%;
      padding: 8px;
      font-size: 1rem;
      margin-bottom: 15px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    .btn-secondary {
      background-color: #eee;
      border: 1px solid #aaa;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn-secondary:hover {
      background-color: #ddd;
    }
  </style>
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
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="onmarket.php">Cars</a></li>
          <li><a href="jets.html">Jets</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="section-padding" style="max-width: 800px; margin: auto;">
    <h1 class="section-heading">Add Car to On-Market</h1>
    <form action="php/submit_car.php" method="POST" enctype="multipart/form-data" class="form">
      <div class="form-group">
        <label for="name">Car Make</label>
        <input type="text" name="name" id="name" required>
      </div>

      <div class="form-group">
        <label for="model">Model</label>
        <input type="text" name="model" id="model" required>
      </div>

      <div class="form-group">
        <label for="price">Price (€)</label>
        <input type="number" name="price" id="price" required>
      </div>

      <div class="form-group">
        <label for="year">Year</label>
        <input type="number" name="year" id="year" required>
      </div>

      <div class="form-group">
        <label for="mileage">Mileage (km)</label>
        <input type="number" name="mileage" id="mileage" required>
      </div>

      <div class="form-group">
        <label for="int_colour">Interior Colour</label>
        <input type="text" name="int_colour" id="int_colour" required>
      </div>

      <div class="form-group">
        <label for="ext_colour">Exterior Colour</label>
        <input type="text" name="ext_colour" id="ext_colour" required>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="5" required></textarea>
      </div>

      <div class="form-group">
        <label>Upload Images & Captions</label>
        <div id="image-list"></div>
        <button type="button" onclick="addImageInput()" class="btn-secondary">Add Image</button>
      </div>

      <button type="submit" class="btn btn-primary">Submit Car</button>
    </form>
  </main>

  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 NordLion International. All rights reserved.</p>
    </div>
  </footer>

  <script>
    function addImageInput() {
      const container = document.getElementById('image-list');

      const wrapper = document.createElement('div');
      wrapper.className = 'image-entry';

      const fileInput = document.createElement('input');
      fileInput.type = 'file';
      fileInput.name = 'images[]';
      fileInput.accept = 'image/*';
      fileInput.required = true;

      const caption = document.createElement('textarea');
      caption.name = 'captions[]';
      caption.rows = 2;
      caption.placeholder = 'Enter caption for this image';
      caption.required = true;

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'remove-btn';
      removeBtn.innerText = '❌';
      removeBtn.onclick = () => wrapper.remove();

      wrapper.appendChild(fileInput);
      wrapper.appendChild(caption);
      wrapper.appendChild(removeBtn);

      container.appendChild(wrapper);
    }
  </script>
</body>
</html>
