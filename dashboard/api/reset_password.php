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
include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $new_password = $_POST['new_password'];

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the user's password in the database
    $sql = "UPDATE users SET password = ? WHERE user_name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hashed_password, $user_name]);

    // Optionally, you can add a success message or redirect
    header("Location: jadwal.php?message=Password berhasil direset"); // Redirect back to the jadwal page with a success message
    exit();
}
?>