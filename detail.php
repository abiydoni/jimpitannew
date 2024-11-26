<?php
session_start();
require 'helper/connection.php'; // Assuming this includes the getDatabaseConnection function

$error = ''; // Initialize the error variable

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- <link rel="manifest" href="manifest.json"> -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<div class="screen-1">


    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
        <a href="javascript:history.back()" 
            class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition duration-200 ease-in-out">
            <!-- Icon Panah -->
            <svg xmlns="kk.php" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-2 w-full max-w-[200px]">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
</div>
</body>
</html>