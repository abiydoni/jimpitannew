<?php
// db.php
$host = 'localhost'; // Your database host
$db = 'dafm5634_jimpitan'; // Your database name
$user = 'dafm5634_ag'; // Your database username
$pass = 'Ag7us777__'; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>