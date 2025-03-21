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
    $stmt = $pdo->prepare("SELECT nama, alamat_url, ikon FROM tb_menu WHERE status=1 ORDER BY nama ASC");
    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil gambar background dari tabel tb_profil
    $stmt_bg = $pdo->prepare("SELECT * FROM tb_profil WHERE kode = 1 LIMIT 1");
    $stmt_bg->execute();
    $profil = $stmt_bg->fetch(PDO::FETCH_ASSOC);
    $background = $profil ? "../assets/image/" . htmlspecialchars($profil['gambar']) : '';
    
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
    <link rel="manifest" href="../manifest.json">

    <style>
        .floating-button {
            position: fixed;
            bottom: 20px;
            right: 0px;
            background-color: #14505c;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .floating-button a {
            right: 4px;
            color: white;
            font-size: 24px;
            text-decoration: none;
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

        .animate-marquee {
        display: inline-block;
        white-space: nowrap;
        animation: marquee 15s linear infinite;
        transform: translateX(100%); /* Mulai dari luar layar kanan */
    }

    @keyframes marquee {
        0% {
            transform: translateX(100%); /* Mulai dari luar layar kanan */
        }
        100% {
            transform: translateX(-100%); /* Berakhir di luar layar kiri */
        }
    }

    /* Responsif: Percepat animasi di layar kecil */
    @media (max-width: 768px) {
        .animate-marquee {
            animation-duration: 10s; /* Animasi lebih cepat di perangkat kecil */
        }
    }
</style>

</head>
<body class="bg-gray-100 font-poppins text-gray-800"
    style="background: url('<?= $background ?>') no-repeat center center fixed; background-size: cover;">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    
    <div class="relative z-10"> 

        <div class="flex flex-col max-w-4xl mx-auto p-4 rounded-lg" style="max-width: 60vh;">
            <h2 class="text-2xl font-bold text-gray-700 mb-2 flex items-center">
                <ion-icon name="information-circle-outline" class="text-3xl mr-2"></ion-icon>           
                Menu Jimpitan
            </h2>
            <div class="flex flex-col items-center p-2 rounded-lg mb-2 bg-gray-800 opacity-50">
                <div class="text-sm font-semibold text-white overflow-hidden w-full">
                    <span class="animate-marquee"><?= htmlspecialchars($profil['catatan']) ?></span>
                </div>
            </div>

            <!-- Tanggal dan Waktu -->
            <div class="flex flex-col items-center p-4 rounded-lg mb-4">
                <div class="text-3xl font-semibold text-white" id="time"></div> <!-- Waktu Lebih Kecil -->
                <div class="text-white" id="date"></div> <!-- Tanggal Lebih Besar -->
            </div>

            <div class="p-4 rounded-lg max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-4 md:grid-cols-4 gap-1 text-xs">
                    <?php foreach ($menus as $menu) : ?>
                        <a href="<?= htmlspecialchars($menu['alamat_url']) ?>.php" 
                            class="py-3 px-3 rounded-lg flex flex-col items-center transition-transform transform hover:scale-110"
                            title="<?= htmlspecialchars($menu['nama']) ?>">
                            <div class="bg-white shadow-md rounded-lg p-2 w-full max-w-lg min-h-[50px] flex items-center justify-center opacity-75">
                                <ion-icon name="<?= $menu['ikon'] ?: 'grid-outline' ?>" class="text-4xl"></ion-icon>
                            </div>
                            <span class="text-white text-sm text-center"><?= htmlspecialchars($menu['nama']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex flex-col items-center">
                <h1 class="text-center font-bold mb-2 text-white">Scan Disini..!</h1>
                <a href="../index.php" 
                class="w-20 h-20 bg-red-600 hover:bg-red-800 text-white rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110">
                    <ion-icon name="barcode-outline" class="text-4xl"></ion-icon>
                </a>
            </div>

            <div class="floating-button" style="margin-right : 70px;">
                <a href="../dashboard/logout.php">
                    <i class="bx bx-log-out-circle bx-tada bx-flip-horizontal" style="font-size:24px"></i>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes wiggle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>

    <script>
        // Fungsi untuk mendapatkan tanggal dan waktu saat ini dalam format Indonesia
        function updateTime() {
            const now = new Date();

            // Format Tanggal Lengkap dalam Bahasa Indonesia
            const tanggalFormatter = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long', // Hari (misal: Senin)
                year: 'numeric',
                month: 'long', // Bulan (misal: Januari)
                day: 'numeric'
            });
            const tanggal = tanggalFormatter.format(now);

            // Format Waktu (HH:MM:SS)
            const jam = now.getHours().toString().padStart(2, '0');
            const menit = now.getMinutes().toString().padStart(2, '0');
            const detik = now.getSeconds().toString().padStart(2, '0');
            const waktu = `${jam}:${menit}:${detik}`;

            // Menampilkan Tanggal dan Waktu
            document.getElementById('date').textContent = tanggal;
            document.getElementById('time').textContent = waktu;
        }

        // Update waktu setiap detik
        setInterval(updateTime, 1000);

        // Inisialisasi pertama kali saat halaman dimuat
        updateTime();
    </script>
  <script>
    // Register the service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
        navigator.serviceWorker.register('../service-worker.js')
            .then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch((error) => {
            console.error('Service Worker registration failed:', error);
            });
        });
    }
  </script>

</body>
</html>
