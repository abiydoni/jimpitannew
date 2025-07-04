<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Ambil bulan, tahun, dan kode_id dari URL
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');
$kode_dicari = isset($_GET['kode']) ? $_GET['kode'] : '';

// Ambil tarif dari tb_tarif
$stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001'");
$stmt_tarif->execute();
$tarif = $stmt_tarif->fetchColumn();

// Hitung jumlah hari di bulan yang dipilih
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil detail data jimpitan
$data = null;
$detail_transaksi = [];
if ($kode_dicari) {
    // Ambil data utama
    $stmt = $pdo->prepare("
        SELECT m.code_id, m.kk_name, COALESCE(SUM(r.nominal), 0) AS jumlah_nominal
        FROM master_kk m
        LEFT JOIN report r ON m.code_id = r.report_id 
        AND MONTH(r.jimpitan_date) = :bulan 
        AND YEAR(r.jimpitan_date) = :tahun
        WHERE m.code_id = :kode
        GROUP BY m.code_id, m.kk_name
    ");
    $stmt->bindParam(':kode', $kode_dicari, PDO::PARAM_STR);
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ambil detail transaksi per tanggal
    $stmt_detail = $pdo->prepare("
        SELECT DATE(r.jimpitan_date) AS tanggal, r.nominal, r.collector
        FROM report r
        WHERE r.report_id = :kode
        AND MONTH(r.jimpitan_date) = :bulan
        AND YEAR(r.jimpitan_date) = :tahun
        ORDER BY r.jimpitan_date ASC
    ");
    $stmt_detail->bindParam(':kode', $kode_dicari, PDO::PARAM_STR);
    $stmt_detail->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt_detail->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt_detail->execute();
    // $detail_transaksi = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
    $transaksi = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
    // Buat daftar tanggal lengkap dalam bulan tersebut
    for ($i = 1; $i <= $jumlah_hari; $i++) {
        $tanggal = sprintf("%04d-%02d-%02d", $tahun, $bulan, $i);
        $detail_transaksi[$tanggal] = ['nominal' => 0, 'collector' => '-'];
    }

    // Masukkan data dari database ke dalam array
    foreach ($transaksi as $row) {
        $detail_transaksi[$row['tanggal']] = [
            'nominal' => $row['nominal'],
            'collector' => $row['collector']
        ];
    }
    }

$total_nominal = $data ? $data['jumlah_nominal'] : 0;
setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data Jimpitan</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
            <!-- Loader GIF loading -->
    <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <img src="../assets/image/loading.gif" alt="Loading..." class="w-32 h-auto">
    </div>

    <div class="relative z-10 flex flex-col max-w-4xl mx-auto p-4 shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Detail Data Jimpitan
        </h1>
        <div class="flex-1 rounded-md mb-4 overflow-y-auto" style="font-size: 12px;">
            <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>
            <p class="text-sm text-gray-500">Bulan: <?= htmlspecialchars($bulan) ?> | Tahun: <?= htmlspecialchars($tahun) ?></p>
            <?php if ($data): ?>
            <div class="flex-1 border rounded-md mb-4 overflow-y-auto bg-white bg-opacity-50" style="max-height: 15vh;">
                <table class="min-w-full border-collapse rounded-md text-sm text-gray-700 ">
                    <thead class="sticky top-0">
                        <tr class="bg-gray-100 border-b">
                            <th class="text-left">Nama Kepala Keluarga</th>
                            <th class="text-right">Target</th>
                            <th class="text-right">Diambil</th>
                            <th class="text-right">Hutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b hover:bg-gray-50">
                            <td><?= htmlspecialchars($data["kk_name"]) ?></td>
                            <td class="text-right"><?= number_format($jumlah_hari * $tarif, 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($data['jumlah_nominal'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format(($jumlah_hari * $tarif) - $data['jumlah_nominal'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
                <h2 class="text-lg font-semibold mt-4 text-gray-500">Detail</h2>
            <div class="flex-1 border rounded-md mb-4 overflow-y-auto bg-white bg-opacity-50" style="max-height: 60vh;">
                <!-- Tabel Detail Per Tanggal -->
                <table class="min-w-full border-collapse text-sm text-gray-700">
                    <thead class="sticky top-0 bg-gray-100 border-b">
                        <tr class="bg-gray-100 border-b">
                            <th class="text-left">No.</th>
                            <th class="text-left">Hari</th>
                            <th class="text-left">Tanggal</th>
                            <th class="text-center">Nominal</th>
                            <th class="text-left">Collector</th>                    
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($detail_transaksi as $tgl => $detail): ?>
                        <?php $hari = strftime('%A', strtotime($tgl)); ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= $no++ ?></td>
                            <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= ucfirst($hari) ?></td>
                            <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= date('d-m-Y', strtotime($tgl)) ?></td>
                            <td class="text-center <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>">
                                <?= number_format($detail['nominal'], 0, ',', '.') ?>
                            </td>
                            <td class="text-left <?= $detail['nominal'] == 0 ? 'text-red-500' : '' ?>"><?= htmlspecialchars($detail['collector']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center py-4 text-gray-500">Data tidak tersedia</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4 font-bold text-gray-700 text-left">Total Jimpitan: <?= number_format($total_nominal, 0, ',', '.') ?></div>

    <!-- Tombol Kembali -->
    <a href="jimpitan.php?bulan=<?= htmlspecialchars($bulan) ?>&tahun=<?= htmlspecialchars($tahun) ?>"
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
        document.getElementById("tanggal").textContent = new Date().toLocaleDateString("id-ID", { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
    </script>
<script>
    const savedColor = localStorage.getItem('overlayColor') || '#000000E6';
    document.body.style.backgroundColor = savedColor;
</script>

</body>
</html>
