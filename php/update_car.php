<?php
// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug output
echo "<pre>POST data: ";
print_r($_POST);
echo "</pre>";

require 'db.php';

// Check if we have a database connection
if (!isset($pdo)) {
    die("Database connection failed!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['car_id'])) {
        die("Car ID not provided");
    }
    
    $id = intval($_POST['car_id']);
    $name = $_POST['name'] ?? '';
    $model = $_POST['model'] ?? '';
    $price = $_POST['price'] ?? 0;
    $year = $_POST['year'] ?? 0;
    $mileage = $_POST['mileage'] ?? 0;
    $int_colour = $_POST['int_colour'] ?? '';
    $ext_colour = $_POST['ext_colour'] ?? '';
    $description = $_POST['description'] ?? '';
    $features = $_POST['features'] ?? '';

    try {
        // Update the car info
        $stmt = $pdo->prepare("UPDATE cars SET name = ?, model = ?, price = ?, year = ?, mileage = ?, int_colour = ?, ext_colour = ?, description = ?, features = ? WHERE id = ?");
        $result = $stmt->execute([$name, $model, $price, $year, $mileage, $int_colour, $ext_colour, $description, $features, $id]);
        
        if (!$result) {
            echo "<p>Database update failed!</p>";
            echo "<pre>";
            print_r($stmt->errorInfo());
            echo "</pre>";
        } else {
            echo "<p>Car updated successfully.</p>";
        }
        
        // Handle images after successful update
        if ($result && !empty($_FILES['images']['name'][0])) {
            echo "<p>Processing uploaded images...</p>";
            
            foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $filename = uniqid() . "_" . basename($_FILES['images']['name'][$index]);
                    $targetPath = '../uploads/' . $filename;
                    
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $caption = $_POST['captions'][$index] ?? '';
                        $insertImage = $pdo->prepare("INSERT INTO car_images (car_id, image_path, caption) VALUES (?, ?, ?)");
                        $imgResult = $insertImage->execute([$id, 'uploads/' . $filename, $caption]);
                        
                        echo $imgResult ? "<p>Image uploaded: $filename</p>" : "<p>Failed to add image to database</p>";
                    } else {
                        echo "<p>Failed to upload image: {$_FILES['images']['name'][$index]}</p>";
                    }
                }
            }
        }
        
        echo "<p>Redirecting to car list page in 3 seconds...</p>";
        echo "<script>setTimeout(function() { window.location.href = '../current_cars.php'; }, 3000);</script>";
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    echo "Invalid request method: " . $_SERVER['REQUEST_METHOD'];
}
