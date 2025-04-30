<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Cek apakah bulan dan tahun dikirim melalui GET, jika kosong gunakan bulan & tahun sekarang
$bulan = (!empty($_GET['bulan']) && is_numeric($_GET['bulan'])) ? (int)$_GET['bulan'] : date('m');
$tahun = (!empty($_GET['tahun']) && is_numeric($_GET['tahun'])) ? (int)$_GET['tahun'] : date('Y');

// Pastikan bulan dalam rentang 1-12
if ($bulan < 1 || $bulan > 12) {
    $bulan = date('m');
}

// Pastikan tahun dalam batas wajar (5 tahun ke belakang dari tahun saat ini)
$tahun_sekarang = date('Y');
if ($tahun < ($tahun_sekarang - 5) || $tahun > $tahun_sekarang) {
    $tahun = $tahun_sekarang;
}

// Ambil tarif dari tb_tarif
$stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001'");
$stmt_tarif->execute();
$tarif = $stmt_tarif->fetchColumn();

// Hitung jumlah hari di bulan yang dipilih
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Eksekusi query untuk mengambil data dari master_kk dan report
$stmt = $pdo->prepare("
    SELECT 
        m.code_id, 
        m.kk_name, 
        COALESCE(SUM(r.nominal), 0) AS jumlah_nominal
    FROM master_kk m
    LEFT JOIN report r ON m.code_id = r.report_id 
        AND MONTH(r.jimpitan_date) = :bulan 
        AND YEAR(r.jimpitan_date) = :tahun
    GROUP BY m.code_id, m.kk_name
    ORDER BY m.kk_name ASC;
");
$stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
$stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal
$total_nominal = array_sum(array_column($results, 'jumlah_nominal'));

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jimpitan</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Data Jimpitan
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>

        <form method="GET" id="filterForm" class="mb-4">
            <!-- Dropdown Bulan -->
            <label for="bulan">Bulan:</label>
            <select name="bulan" id="bulan" class="bg-gray-100 p-2 rounded" onchange="document.getElementById('filterForm').submit()">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= ($i == $bulan) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                <?php endfor; ?>
            </select>

            <!-- Dropdown Tahun -->
            <label for="tahun">Tahun:</label>
            <select name="tahun" id="tahun" class="bg-gray-100 p-2 rounded" onchange="document.getElementById('filterForm').submit()">
                <?php for ($i = date('Y') - 5; $i <= date('Y'); $i++): ?>
                    <option value="<?= $i ?>" <?= ($i == $tahun) ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </form>

        <!-- Tabel Data -->
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 73vh;">
            <?php if (!empty($results)): ?>
                <table class="min-w-full border-collapse text-sm text-gray-700">
                    <thead class="sticky top-0 bg-gray-100 border-b">
                        <tr>
                            <th>No.</th>
                            <th>Nama Kepala Keluarga</th>
                            <th class="text-right">Target</th>
                            <th class="text-right">Diambil</th>
                            <th class="text-right">Hutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($results as $row):
                            $target = $jumlah_hari * $tarif;
                            $hutang = $target - $row['jumlah_nominal'];
                        ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td><?= $no ?></td>
                                <td>
                                    <a href="detail_kk.php?kode=<?= urlencode($row['code_id']) ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
                                    class="text-blue-500 hover:underline">
                                        <?= htmlspecialchars($row['kk_name']) ?>
                                    </a>
                                </td>
                                <td class="text-right"><?= number_format($target, 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($row['jumlah_nominal'], 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($hutang, 0, ',', '.') ?></td>
                            </tr>
                        <?php
                        $no++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-4 text-gray-500">
                    <ion-icon name="folder-open-outline" size="large"></ion-icon>
                    <p>Data tidak tersedia</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Total Nominal -->
        <div class="mt-4 font-bold text-gray-700 text-left">Total Jimpitan: <?= number_format($total_nominal, 0, ',', '.') ?></div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='../index.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>

    <script>
        function formatTanggalIndonesia() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            
            const tgl = new Date();
            return `${hari[tgl.getDay()]}, ${tgl.getDate()} ${bulan[tgl.getMonth()]} ${tgl.getFullYear()}`;
        }

        document.getElementById("tanggal").textContent = formatTanggalIndonesia();
    </script>
</body>
</html>
