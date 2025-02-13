<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-100 font-poppins text-gray-800 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-lg p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-2xl font-bold text-gray-700 mb-4">Informasi</h1>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h2 class="text-lg text-gray-600">Halaman ini masih dalam pengembangan...</h2>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <a href="detail_scan.php" 
       class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
       title="Kembali ke halaman detail sebelumnya">
        <ion-icon name="arrow-back-outline"></ion-icon>
    </a>
</body>
</html>
