<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Ambil bulan, tahun, dan kode_id dari URL
$bulan = isset($_GET['bulan']) ? (int) $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int) $_GET['tahun'] : date('Y');
$kode_dicari = isset($_GET['kode']) ? $_GET['kode'] : '';

// Ambil tarif dari tb_tarif
$stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001'");
$stmt_tarif->execute();
$tarif = $stmt_tarif->fetchColumn();

// Hitung jumlah hari di bulan yang dipilih
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil detail data jimpitan
$data = null;
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
        SELECT DATE(r.jimpitan_date) AS tanggal, r.nominal
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
    $detail_transaksi = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
}

$total_nominal = $data ? $data['jumlah_nominal'] : 0;
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
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Detail Data Jimpitan
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>
        <p class="text-sm text-gray-600">Bulan: <?= htmlspecialchars($bulan) ?> | Tahun: <?= htmlspecialchars($tahun) ?></p>

        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 73vh;">
            <?php if ($data): ?>
                <table class="min-w-full border-collapse text-sm text-gray-700">
                    <thead class="sticky top-0">
                        <tr class="bg-gray-100 border-b">
                            <th>No.</th>
                            <th>Nama Kepala Keluarga</th>
                            <th class="text-right">Target</th>
                            <th class="text-right">Diambil</th>
                            <th class="text-right">Hutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b hover:bg-gray-50">
                            <td>1</td>
                            <td><?= htmlspecialchars($data["kk_name"]) ?></td>
                            <td class="text-right"><?= number_format($jumlah_hari * $tarif, 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($data['jumlah_nominal'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= number_format(($jumlah_hari * $tarif) - $data['jumlah_nominal'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tabel Detail Per Tanggal -->
                <h2 class="text-lg font-semibold mt-4 text-gray-700">Detail Per Tanggal</h2>
                <table class="min-w-full border-collapse text-sm text-gray-700 mt-2">
                    <thead class="sticky top-0">
                        <tr class="bg-gray-100 border-b">
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detail_transaksi)): ?>
                            <?php $no = 1; foreach ($detail_transaksi as $row): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="text-right"><?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-gray-500 py-4">Tidak ada data jimpitan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <p class="text-center py-4 text-gray-500">Data tidak tersedia</p>
            <?php endif; ?>
        </div>

        <div class="mt-4 font-bold text-gray-700 text-left">Total Jimpitan: <?= number_format($total_nominal, 0, ',', '.') ?></div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
            href="menu.php" title="Kembali">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>

    <script>
        document.getElementById("tanggal").textContent = new Date().toLocaleDateString("id-ID", { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
        });
    </script>
</body>
</html>
