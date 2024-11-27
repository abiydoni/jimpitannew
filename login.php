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
                        header('Location: index.php'); // Redirect to Scan App
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script type="module" src="js/ionicons.esm.js"></script>
    <script nomodule src="js/ionicons.js"></script>
    <link rel="stylesheet" href="css/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<form action="login.php" method="POST">
<div class="screen-1">
    <div class="email">
        <label for="user_name">User</label>
        <div class="sec-2">
            <ion-icon name="person-outline"></ion-icon>
            <input type="text" name="user_name" placeholder="********" required/>
        </div>
    </div>
    
    <div class="password">
        <label for="password">Password</label>
        <div class="sec-2">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input class="pas" type="password" name="password" placeholder="********" required/>
        </div>
    </div>

    <div class="password">
    <label><input type="checkbox" name="redirect_option" value="dashboard"> Go To Dashboard</label>
    </div>

    <button class="login">Login</button>
    <div class="footer">
        <?php if ($error): ?>
            <div class="error-message" style="color: red; font-size: 12px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
    <p style="color:grey; font-size: 8px; text-align: center;">@2024 copyright | by doniabiy</p>
</div>
</form>
</body>
</html>