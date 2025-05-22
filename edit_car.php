<?php
require 'php/db.php';

if (!isset($_GET['id'])) {
    die("Car ID not provided.");
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found.");
}

$imageStmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ?");
$imageStmt->execute([$id]);
$images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Car | NordLion</title>
  <link rel="stylesheet" href="css/style.css" />
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

<main class="section-padding" style="max-width: 800px; margin: auto;">
    
  <h1 class="section-heading">Edit <?php echo htmlspecialchars($car['name'] . ' ' . $car['model']); ?></h1>
  <a href="current_cars.php" class="btn btn-outline" style="margin-bottom: 20px; display: inline-block;">← Back to Car List</a>
  <form action="php/update_car.php" method="POST" enctype="multipart/form-data" class="form">
    <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">

    <?php
    $fields = [
      'name' => 'Car Make',
      'model' => 'Model',
      'price' => 'Price (€)',
      'year' => 'Year',
      'mileage' => 'Mileage (km)',
      'int_colour' => 'Interior Colour',
      'ext_colour' => 'Exterior Colour',
    ];
    foreach ($fields as $field => $label) {
        $value = htmlspecialchars($car[$field]);
        echo "
        <div class='form-group'>
          <label for='{$field}'>{$label}</label>
          <input type='text' name='{$field}' id='{$field}' value='{$value}' required>
        </div>";
    }
    ?>

    
    <div class="form-group">
      <label for="description">Description</label>
      <textarea name="description" id="description" rows="5" required><?php echo htmlspecialchars($car['description']); ?></textarea>
    </div>

    <div class="form-group">
      <label for="features">Exceptional Features (one per line)</label>
      <textarea name="features" id="features" rows="5"><?php echo htmlspecialchars($car['features']); ?></textarea>
    </div>

    <div class="form-group">
      <label>Upload New Images & Captions</label>
      <div id="image-list"></div>
      <button type="button" onclick="addImageInput()" class="btn-outline">Add Image</button>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Update Car</button>
  </form>

  <?php if ($images): ?>
  <div class="form-group" style="margin-top: 40px;">
    <label>Existing Images</label>
    <div style="display: flex; flex-wrap: wrap; gap: 16px;">
      <?php foreach ($images as $img): ?>
        <div style="position: relative; width: 160px; text-align: center;">
          <img src="uploads/<?php echo htmlspecialchars(basename($img['image_path'])); ?>" style="width: 100%; border-radius: 6px;">
          <form action="php/delete_image.php" method="POST" style="position: absolute; top: -12px; right: -12px;">
            <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
            <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
            <button type="submit" style="
              background: #c0392b;
              border: none;
              color: white;
              font-weight: bold;
              border-radius: 50%;
              width: 26px;
              height: 26px;
              cursor: pointer;
              font-size: 16px;
              display: flex;
              align-items: center;
              justify-content: center;">×</button>
          </form>
          <small style="display: block; margin-top: 4px;"><?php echo htmlspecialchars($img['caption']); ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</main>

<script>
function addImageInput() {
  const container = document.getElementById('image-list');

  const wrapper = document.createElement('div');
  wrapper.className = 'form-group';

  const fileInput = document.createElement('input');
  fileInput.type = 'file';
  fileInput.name = 'images[]';
  fileInput.accept = 'image/*';
  fileInput.required = true;

  const captionInput = document.createElement('input');
  captionInput.type = 'text';
  captionInput.name = 'captions[]';
  captionInput.placeholder = 'Enter caption';
  captionInput.className = 'form-input';
  captionInput.required = true;

  wrapper.appendChild(fileInput);
  wrapper.appendChild(captionInput);
  container.appendChild(wrapper);
}
</script>
</body>
</html>
