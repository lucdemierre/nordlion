<?php
$host = 'localhost';          // stays as localhost for cPanel hosting
$db   = 'nordlion_db';        // your database name
$user = 'nordlion_lucd';      // your cPanel DB username
$pass = 'spocky2008!'; // the password you set in cPanel for this user

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
