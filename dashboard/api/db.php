<?php
// db.php
$host = 'localhost';
$db = 'umt096nh_jimpitan';
$user = 'umt096nh_admin';
$pass = 'A7biy777__';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>