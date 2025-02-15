<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Ambil bulan dan tahun yang dipilih
$bulan = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y');

// Ambil tarif dari tb_tarif
$stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001'");
$stmt_tarif->execute();
$tarif = $stmt_tarif->fetchColumn();

// Hitung jumlah hari di bulan yang dipilih
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Cek apakah ada pencarian berdasarkan kode
$kode_dicari = isset($_GET['kode']) ? $_GET['kode'] : '';

if ($kode_dicari) {
    // Query untuk mencari data berdasarkan kode
    $stmt = $pdo->prepare("
        SELECT m.code_id, m.kk_name, COALESCE(SUM(r.nominal), 0) AS jumlah_nominal
        FROM master_kk m
        LEFT JOIN report r ON m.code_id = r.report_id 
        WHERE m.code_id = :kode AND MONTH(r.jimpitan_date) = :bulan AND YEAR(r.jimpitan_date) = :tahun
        GROUP BY m.code_id, m.kk_name
        ORDER BY m.kk_name ASC
    ");
    $stmt->bindParam(':kode', $kode_dicari, PDO::PARAM_STR);
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt->execute();
    
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "Data tidak ditemukan.";
        exit;
    }
} else {
    // Query untuk mengambil semua data
    $stmt = $pdo->prepare("
        SELECT m.code_id, m.kk_name, COALESCE(SUM(r.nominal), 0) AS jumlah_nominal
        FROM master_kk m
        LEFT JOIN report r ON m.code_id = r.report_id AND MONTH(r.jimpitan_date) = :bulan AND YEAR(r.jimpitan_date) = :tahun
        GROUP BY m.code_id, m.kk_name
        ORDER BY m.kk_name ASC;
    ");
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

        <form method="post" class="mb-4">
            <label for="bulan" class="mr-2">Bulan:</label>
            <select name="bulan" id="bulan" class="bg-gray-100 p-2 rounded">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <label for="tahun" class="ml-2 mr-2">Tahun:</label>
            <select name="tahun" id="tahun" class="bg-gray-100 p-2 rounded">
                <?php for ($i = date('Y') - 5; $i <= date('Y'); $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="bg-blue-500 text-white p-1 px-3 text-sm rounded">Filter</button>
        </form>

        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 73vh;">
            <?php if (!empty($results)): ?>
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
                        <?php $no = 1; foreach ($results as $row): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td><?= $no ?></td>
                                <td>
                                    <a href="detail_kk.php?nama=<?= urlencode($row['code_id']) ?>" class="text-blue-500 hover:underline">
                                        <?= htmlspecialchars($row["kk_name"]) ?>
                                    </a>
                                </td>
                                <td class="text-right"><?= number_format($jumlah_hari * $tarif, 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($row['jumlah_nominal'], 0, ',', '.') ?></td>
                                <td class="text-right"><?= number_format(($jumlah_hari * $tarif) - $row['jumlah_nominal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php $no++; endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center py-4 text-gray-500">Data tidak tersedia</p>
            <?php endif; ?>
        </div>
        <div class="mt-4 font-bold text-gray-700 text-left">Total Jimpitan: <?= number_format($total_nominal, 0, ',', '.') ?></div>
    </div>
    <script>
        document.getElementById("tanggal").textContent = new Date().toLocaleDateString("id-ID", { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    </script>
</body>
</html>
