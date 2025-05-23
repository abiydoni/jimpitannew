<?php
session_start();

// Pastikan user sudah login
$user = $_SESSION['user'] ?? null;
if (!$user) {
    header('Location: login.php');
    exit;
}

$menus = $_SESSION['menus'] ?? [];
require_once 'db.php';
$stmt = $conn->query("SELECT catatan FROM tb_profil LIMIT 1");
$profil = $stmt->fetch(PDO::FETCH_ASSOC);
$catatan = $profil['catatan'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menu Jimpitan</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet" />

    <link rel="manifest" href="manifest.json" />

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
        }
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        @media (max-width: 768px) {
            .animate-marquee {
                animation-duration: 10s;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">

    <!-- Loader -->
    <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <img src="assets/image/loading.gif" alt="Loading..." class="w-32 h-auto" />
    </div>

    <div id="overlayDiv" class="absolute inset-0"></div>

    <div class="relative z-10">
        <div class="flex flex-col max-w-4xl mx-auto p-4 rounded-lg" style="max-width: 60vh;">

            <h2 class="text-2xl font-bold text-gray-700 mb-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <ion-icon name="information-circle-outline" class="text-3xl"></ion-icon>
                    <span>Hello.. <?= htmlspecialchars($user['name']) ?></span>
                </div>                
                <!-- Pilih Warna -->
                <input type="color" id="overlayColor"
                    class="w-8 h-8 border-none p-0 cursor-pointer rounded-full bg-transparent"
                    title="Pilih warna latar belakang"
                    style="z-index:9999;" />
            </h2>

            <div class="flex flex-col items-center p-2 rounded-lg mb-2 bg-gray-800 opacity-50 w-full">
                <div class="text-sm font-semibold text-white overflow-hidden w-full">
                    <span class="animate-marquee"><?= htmlspecialchars($profil['catatan'] ?? '') ?></span>
                </div>
            </div>
            <!-- Tanggal dan Waktu -->
            <div class="flex flex-col items-center p-4 rounded-lg mb-4">
                <div class="text-3xl font-semibold text-gray-500" id="time"></div>
                <div class="text-gray-500" id="date"></div>
            </div>

            <!-- Menu Grid -->
            <div class="p-4 rounded-lg max-h-[70vh] overflow-y-auto bg-black bg-opacity-50 shadow-md w-full">
                <div class="grid grid-cols-4 md:grid-cols-4 gap-1 text-xs">
                    <?php foreach ($menus as $menu): ?>
                        <a href="api/<?= htmlspecialchars($menu['alamat_url']) ?>.php"
                            class="py-2 px-2 rounded-lg flex flex-col items-center transition-transform transform hover:scale-110"
                            title="<?= htmlspecialchars($menu['nama']) ?>">
                            <div class="bg-white shadow-md rounded-lg p-2 w-full max-w-lg min-h-[50px] flex items-center justify-center opacity-75">
                                <ion-icon name="<?= htmlspecialchars($menu['ikon'] ?: 'grid-outline') ?>" class="text-4xl"></ion-icon>
                            </div>
                            <span class="text-white text-sm text-center"><?= htmlspecialchars($menu['nama']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (strtolower($user['role']) !== 'warga'): ?>
                <!-- Scan Button -->
                <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 flex flex-col items-center">
                    <h1 class="text-center font-bold mb-2 text-gray-500">Scan Disini..!</h1>
                    <a href="scan.php"
                    class="w-20 h-20 bg-red-600 hover:bg-red-800 text-white rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110">
                        <ion-icon name="barcode-outline" class="text-4xl"></ion-icon>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Logout Floating Button -->
            <div class="floating-button" style="margin-right: 70px;">
                <a href="dashboard/logout.php" title="Logout">
                    <i class="bx bx-log-out-circle bx-tada bx-flip-horizontal" style="font-size:24px"></i>
                </a>
            </div>

            <!-- Tampilkan Role User (optional) -->
            <div class="text-xs text-gray-400 text-center mt-2">
                Anda Login sebagai: <strong><?= htmlspecialchars($user['role']) ?></strong>
            </div>
        </div>
    </div>

    <script>
        // Loader saat klik tombol/link
        document.querySelectorAll('button, a, input[type="submit"]').forEach(element => {
            element.addEventListener('click', function (e) {
                e.preventDefault();

                document.getElementById('loader').classList.remove('hidden');

                if (this.type === 'submit') {
                    setTimeout(() => this.closest('form').submit(), 500);
                } else {
                    setTimeout(() => window.location.href = this.href, 500);
                }
            });
        });

        // Update tanggal dan waktu secara realtime
        function updateTime() {
            const now = new Date();

            const tanggalFormatter = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const tanggal = tanggalFormatter.format(now);

            const jam = String(now.getHours()).padStart(2, '0');
            const menit = String(now.getMinutes()).padStart(2, '0');
            const detik = String(now.getSeconds()).padStart(2, '0');
            const waktu = `${jam}:${menit}:${detik}`;

            document.getElementById('date').textContent = tanggal;
            document.getElementById('time').textContent = waktu;
        }

        setInterval(updateTime, 1000);
        updateTime();

        // Service worker register
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('../service-worker.js')
                    .then(registration => console.log('Service Worker registered:', registration.scope))
                    .catch(error => console.error('Service Worker registration failed:', error));
            });
        }

        // Color picker overlay control
        const colorPicker = document.getElementById('overlayColor');
        const overlay = document.getElementById('overlayDiv');

        const savedColor = localStorage.getItem('overlayColor') || '#000000E6';
        overlay.style.backgroundColor = savedColor;
        colorPicker.value = savedColor.substring(0, 7);

        colorPicker.addEventListener('input', function () {
            const chosenColor = this.value + 'E6'; // Transparansi ~90%
            overlay.style.backgroundColor = chosenColor;
            localStorage.setItem('overlayColor', chosenColor);
        });
    </script>

</body>
</html>
