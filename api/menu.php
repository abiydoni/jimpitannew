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
</head>

<body class="bg-gray-100 font-poppins text-gray-800 flex justify-center items-center min-h-screen">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg" style="max-width: 60vh;">
        <h1 class="text-2xl font-bold text-gray-700 mb-4 flex items-center">
            <!-- Ikon Ionicons -->
            <ion-icon name="information-circle-outline" class="text-3xl mr-2"></ion-icon>           
             Menu Informasi
        </h1>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <!-- Grid Menu -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($menus as $menu) : ?>
                    <a href="<?= htmlspecialchars($menu['alamat_url']) ?>.php" 
                        class="group bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md flex flex-col items-center justify-center transition-transform transform hover:scale-105"
                        title="<?= htmlspecialchars($menu['nama']) ?>">
                        
                        <!-- Ikon dari Database -->
                        <ion-icon name="<?= $menu['ikon'] ?: 'grid-outline' ?>" class="text-3xl mb-2"></ion-icon>
                        
                        <!-- Nama Menu -->
                        <span class="text-sm"><?= htmlspecialchars($menu['nama']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <a href="../index.php" 
        class="fixed bottom-4 right-50 w-20 h-20 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
        title="Kembali ke halaman Scan">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </a>

    </div>
    <!-- Tombol Kembali -->
    <a href="detail_scan.php" 
       class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
       title="Kembali ke halaman detail sebelumnya">
        <ion-icon name="arrow-back-outline"></ion-icon>
    </a>
</body>
</html>
