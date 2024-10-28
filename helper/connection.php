<?php
function getDatabaseConnection() {
    $host = 'localhost';
    $db = 'umt096nh_jimpitan';
    $user = 'umt096nh_admin';
    $pass = 'A7biy777__';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die('Database connection error: ' . $e->getMessage());
    }
}
?>
