<?php
session_start();
require 'helper/connection.php'; // Assuming this includes the getDatabaseConnection function

$error = ''; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect_option = $_POST['redirect_option'] ?? 'scan_app'; // Default to 'scan_app'

    try {
        $pdo = getDatabaseConnection();

        $stmt = $pdo->prepare('SELECT * FROM users WHERE user_name = ?');
        $stmt->execute([$user_name]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Get the current day of the week
            $currentDay = date('l'); // e.g., "Monday", "Tuesday", etc.

            // Check if the current day is in the user's shift (skip for admin)
            if ($user['role'] === 'admin' || in_array($currentDay, explode(',', $user['shift']))) {
                $_SESSION['user'] = $user;

                // Redirect based on the selected option
                if ($redirect_option === 'dashboard' && $user['role'] === 'user') {
                    $error = 'Maaf kamu bukan Administrator';
                } else {
                    if ($redirect_option === 'dashboard') {
                        header('Location: /dashboard'); // Redirect to Dashboard
                    } else {
                        header('Location: api/menu.php'); // Redirect to Scan App
                    }
                    exit;
                }
            } else {
                $error = 'Login gagal! Hari ini bukan jadwalmu jaga';
            }
        } else {
            $error = 'username atau password salah!';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>
