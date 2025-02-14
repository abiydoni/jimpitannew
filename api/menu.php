<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';
try {
    // Ambil data menu dari database menggunakan PDO
    $stmt = $pdo->prepare("SELECT nama, alamat_url, ikon FROM tb_menu ORDER BY nama ASC");
    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
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
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

      <style>
            .floating-button {
            position: fixed;
            bottom: 20px; /* Jarak dari bawah */
            right: 20px; /* Jarak dari kanan */
            background-color: #14505c; /* Warna latar belakang dengan transparansi */
            border-radius: 50%; /* Membuat tombol bulat */
            width: 60px; /* Lebar tombol */
            height: 60px; /* Tinggi tombol */
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Bayangan */
            z-index: 1000; /* Pastikan di atas elemen lain */
        }

        .floating-button a {
            color: white; /* Warna teks */
            font-size: 24px; /* Ukuran teks */
            text-decoration: none; /* Menghilangkan garis bawah */
        }
        button {
            margin: 10px;
            padding: 10px 20px;
            border-radius: 25px;
            background-color: #14505c;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

    </style>

</head>

<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg" style="max-width: 60vh;">
        <h1 class="text-2xl font-bold text-gray-700 mb-2 flex items-center">
            <!-- Ikon Ionicons -->
            <ion-icon name="information-circle-outline" class="text-3xl mr-2"></ion-icon>           
             Menu Informasi
        </h1>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-[70vh] overflow-y-auto">
            <!-- Grid Menu -->
            <div class="grid grid-cols-3 md:grid-cols-4 gap-4">
                <?php foreach ($menus as $menu) : ?>
                    <a href="<?= htmlspecialchars($menu['alamat_url']) ?>.php" 
                        class="group bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md flex flex-col items-center justify-center transition-transform transform hover:scale-105"
                        title="<?= htmlspecialchars($menu['nama']) ?>">
                        
                        <!-- Ikon dari Database -->
                        <ion-icon name="<?= $menu['ikon'] ?: 'grid-outline' ?>" class="text-3xl mb-2"></ion-icon>
                        
                        <!-- Nama Menu (Ditambahkan text-center) -->
                        <span class="text-sm text-center"><?= htmlspecialchars($menu['nama']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Tombol Scan Barcode -->
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex flex-col items-center">
            <h1 class="text-center font-bold mb-2 text-gray-700">Scan Disini..!</h1>
            <a href="../index.php" 
            class="w-20 h-20 bg-red-600 hover:bg-red-800 text-white rounded-full flex items-center justify-center shadow-lg transition-transform hover:scale-110 motion-safe:animate-[wiggle_2s_infinite]">
                <ion-icon name="barcode-outline" class="text-4xl"></ion-icon>
            </a>
        </div>
        <div class="floating-button" style="margin-right : 70px;">
            <a href="dashboard/logout.php"><i class="bx bx-log-out-circle bx-tada bx-flip-horizontal" style="font-size:24px" ></i></a>
        </div>
    <!-- Tombol Kembali -->
    <!-- <a href="detail_scan.php" 
       class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
       title="Kembali ke halaman detail sebelumnya">
        <ion-icon name="arrow-back-outline"></ion-icon>
    </a> -->

    <style>
        @keyframes wiggle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</body>
</html>
