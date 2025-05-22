<?php
$host = 'localhost';
$db = 'nordlion_db';
$user = 'root';
$pass = ''; // default XAMPP MySQL password is blank

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
