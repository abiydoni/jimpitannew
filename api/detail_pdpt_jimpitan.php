<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Ambil bulan dan tahun dari URL
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Hitung jumlah hari di bulan yang dipilih
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil detail data jimpitan
$detail_transaksi = [];

// Query untuk mengambil data transaksi berdasarkan bulan dan tahun
$stmt = $pdo->prepare("SELECT jimpitan_date, SUM(nominal) as total_jimpitan 
                       FROM report 
                       WHERE MONTH(jimpitan_date) = :bulan 
                       AND YEAR(jimpitan_date) = :tahun 
                       GROUP BY jimpitan_date");
$stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
$stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
$stmt->execute();
$transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat daftar tanggal lengkap dalam bulan tersebut
for ($i = 1; $i <= $jumlah_hari; $i++) {
    $tanggal = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
    // Default nominal 0 jika tidak ada transaksi untuk tanggal ini
    $detail_transaksi[$tanggal] = ['nominal' => 0];  
}

// Masukkan data dari database ke dalam array
foreach ($transaksi as $row) {
    $detail_transaksi[$row['jimpitan_date']] = [
        'nominal' => $row['total_jimpitan'],
    ];
}

$total_nominal = array_sum(array_column($detail_transaksi, 'nominal')); // Menghitung total nominal
setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian'); // Pengaturan lokal
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Jimpitan</title>
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        /* Membatasi tinggi grafik agar sesuai dengan tinggi baris tabel */
        .chart-container {
            width: 100%;
            height: auto;
        }

        /* Mengurangi padding di sel tabel */
        table th, table td {
            text-align: left;
        }

        /* Menyesuaikan tinggi baris tabel agar lebih rapat */
        table tr {
            height: 28px; /* Menurunkan tinggi baris tabel */
            line-height: 1.2; /* Mengatur line height agar teks lebih padat */
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg bg-opacity-50" style="max-width: 60vh;">
                    <!-- Loader GIF loading -->
        <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
            <img src="../assets/image/loading.gif" alt="Loading..." class="w-32 h-auto">
        </div>

        <h1 class="text-xl font-bold text-gray-700 mb-2">
            Detail Jimpitan
        </h1>
        <p class="text-sm text-gray-600">Bulan: <?= htmlspecialchars($bulan) ?> | Tahun: <?= htmlspecialchars($tahun) ?></p>
        
        <?php if (!empty($detail_transaksi)): ?>
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-width: 60vh; max-height: 75vh; font-size: 12px;">
            <table class="min-w-full border-collapse text-sm text-gray-700">
                <thead class="sticky top-0 bg-gray-100 border-b">
                    <tr class="bg-gray-100 border-b">
                        <th class="text-left">No.</th>
                        <th class="text-left">Hari</th>
                        <th class="text-left">Tanggal</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($detail_transaksi as $tgl => $detail): ?>
                    <?php $hari = strftime('%A', strtotime($tgl)); ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= $no++ ?></td>
                        <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= ucfirst($hari) ?></td>
                        <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= date('d-m-Y', strtotime($tgl)) ?></td>
                        <td class="text-right <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>">
                            <?= number_format($detail['nominal'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-center py-4 text-gray-500">Data tidak tersedia</p>
        <?php endif; ?>

        <div class="mt-4 font-bold text-gray-700 text-left">Total Jimpitan: <?= number_format($total_nominal, 0, ',', '.') ?></div>
    </div>
    <!-- Tombol Kembali -->
    <a href="pdpt_jimpitan.php?tahun=<?= htmlspecialchars($tahun) ?>"
        class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110">
        <ion-icon name="arrow-back-outline"></ion-icon>
    </a>
    <script>
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function (e) {
        if (
            this.target !== '_blank' &&
            this.href &&
            !this.href.startsWith('javascript') &&
            !this.href.startsWith('#')
        ) {
            document.getElementById('loader').classList.remove('hidden');
        }
        });
    });
    </script>
<script>
    const savedColor = localStorage.getItem('overlayColor') || '#000000E6';
    document.body.style.backgroundColor = savedColor;
</script>

</body>
</html>
