<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

// Check if user is admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php'); // Redirect to unauthorized page
    exit;
}

// Include the database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $name = $_POST['name'];
    $shift = $_POST['shift'];
    $role = $_POST['role'];
    $id_code = $_POST['id_code']; // Menambahkan id_code untuk query

    // Validasi input
    if (empty($user_name) || empty($name) || empty($shift) || empty($role) || empty($id_code)) {
        // Tangani kesalahan input
        header("Location: ../error.php"); // Redirect ke halaman kesalahan
        exit();
    }

    // Update user data in the database
    $sql = "UPDATE users SET user_name = ?, name = ?, shift = ?, role = ? WHERE id_code = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_name, $name, $shift, $role, $id_code]); // Menambahkan id_code ke parameter

    header("Location: ../jadwal.php"); // Redirect back to the jadwal page
    exit();
}
?>