<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Ambil bulan dan tahun dari GET request (default ke bulan dan tahun saat ini)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query untuk mengambil data berdasarkan bulan dan tahun yang dipilih
$stmt = $pdo->prepare("
    SELECT 
        m.code_id, 
        m.kk_name, 
        COALESCE(SUM(r.nominal), 0) AS jumlah_nominal
    FROM master_kk m
    LEFT JOIN report r ON m.code_id = r.report_id 
    AND MONTH(r.created_at) = :bulan 
    AND YEAR(r.created_at) = :tahun
    GROUP BY m.code_id, m.kk_name
    ORDER BY m.code_id ASC;
");
$stmt->execute(['bulan' => $bulan, 'tahun' => $tahun]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal
$total_nominal = array_sum(array_column($results, 'jumlah_nominal'));

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-gray-700">
                <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
                Data Jimpitan
            </h1>
            <div class="flex items-center space-x-2">
                <!-- Dropdown Pilih Bulan -->
                <select id="bulan" class="border rounded p-1">
                    <?php
                    $bulan_arr = [
                        "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April",
                        "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus",
                        "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
                    ];
                    foreach ($bulan_arr as $key => $nama) {
                        $selected = ($bulan == $key) ? "selected" : "";
                        echo "<option value='$key' $selected>$nama</option>";
                    }
                    ?>
                </select>

                <!-- Dropdown Pilih Tahun -->
                <select id="tahun" class="border rounded p-1">
                    <?php
                    $tahun_now = date('Y');
                    for ($i = $tahun_now - 5; $i <= $tahun_now; $i++) {
                        $selected = ($tahun == $i) ? "selected" : "";
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>

        <!-- Kontainer tabel dengan scrollable -->
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 60vh; font-size: 12px;">
            <?php if (count($results) > 0) : ?>
                <table class="min-w-full border-collapse text-sm text-gray-700">
                    <thead class="sticky top-0">
                        <tr class="bg-gray-100 border-b">
                            <th>No.</th>
                            <th>Kode</th>
                            <th>Nama Kepala Keluarga</th>
                            <th>Jumlah Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($results as $row) : ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td><?= $no++; ?></td>
                            <td><?= $row['code_id']; ?></td>
                            <td><?= $row['kk_name']; ?></td>
                            <td><?= number_format($row['jumlah_nominal'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="text-center py-4 text-gray-500">
                    <ion-icon name="folder-open-outline" size="large"></ion-icon>
                    <p>Data tidak tersedia</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Total Nominal -->
        <div class="mt-4 font-bold text-gray-700 text-left">
            Total Jimpitan: <?= number_format($total_nominal, 0, ',', '.'); ?>
        </div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
            onclick="window.location.href='menu.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>

    <script>
        function formatTanggalIndonesia() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

            const tanggal = new Date();
            const hariNama = hari[tanggal.getDay()];
            const bulanNama = bulan[tanggal.getMonth()];
            const tanggalTanggal = tanggal.getDate();
            const tahun = tanggal.getFullYear();

            return `${hariNama}, ${tanggalTanggal} ${bulanNama} ${tahun}`;
        }

        document.getElementById("tanggal").textContent = formatTanggalIndonesia();

        // Event listener untuk perubahan dropdown
        document.getElementById("bulan").addEventListener("change", updateFilter);
        document.getElementById("tahun").addEventListener("change", updateFilter);

        function updateFilter() {
            const bulan = document.getElementById("bulan").value;
            const tahun = document.getElementById("tahun").value;
            window.location.href = `?bulan=${bulan}&tahun=${tahun}`;
        }
    </script>
</body>
</html>
