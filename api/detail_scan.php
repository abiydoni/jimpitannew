<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// SQL statement untuk mengambil data hari ini
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total scan
$total_scans = count($data);
$total_nominal = array_sum(array_column($data, 'nominal'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

    <style>
        table th, table td {
            text-align: left;
        }
        table tr {
            height: 28px;
            line-height: 1.2;
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">Data Scan Jimpitan</h1>
        <p class="text-sm text-gray-500 mb-4">Hari <span id="tanggal"></span></p>

        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 80vh; font-size: 12px;">
        <?php if (count($data) > 0): ?>
                <table class='min-w-full border-collapse text-sm text-gray-700'>
                    <thead>
                        <tr class='bg-gray-100 border-b'>
                            <th>No.</th>
                            <th>Nama KK</th>
                            <th class='text-center'>Nominal</th>
                            <th>Jaga</th>
                        </tr>
                    </thead>
                    <tbody id='data-table'>
                        <?php $no = 1; ?>
                        <?php foreach ($data as $row): ?>
                            <tr class='border-b hover:bg-gray-50'>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row["kk_name"]) ?></td>
                                <td class="text-center"><?= number_format($row["nominal"], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row["collector"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class='text-center py-4 text-gray-500'>
                    <ion-icon name='folder-open-outline' size='large'></ion-icon>
                    <p>Data tidak tersedia</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 font-bold text-gray-700 text-left">
            Total Scan: <?= number_format($total_scans, 0, ',', '.') ?>
        </div>
        <div class="mt-1 font-bold text-gray-700 text-left">
            Total Nominal Setor: Rp <?= number_format($total_nominal, 0, ',', '.') ?>
        </div>

        <!-- Tombol Bulat Kembali -->
        <button class="fixed bottom-4 right-20 w-12 h-12 bg-green-500 hover:bg-green-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
            onclick="window.location.href='rekor_scan.php'" title="Kembali ke halaman detail sebelumnya">
            <ion-icon name="book-outline"></ion-icon>
        </button>

        <!-- Tombol Bulat -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
            onclick="window.location.href='../index.php'" title="Pergi ke halaman baru">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>

    <script>
        // Menampilkan tanggal dalam format Indonesia
        function formatTanggalIndonesia() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const tanggal = new Date();
            return `${hari[tanggal.getDay()]}, ${tanggal.getDate()} ${bulan[tanggal.getMonth()]} ${tanggal.getFullYear()}`;
        }

        document.getElementById("tanggal").textContent = formatTanggalIndonesia();

        // Fungsi untuk memperbarui tabel setiap 60 detik
        function updateTable() {
            $.ajax({
                url: 'get_data.php',
                method: 'GET',
                success: function(response) {
                    const data = JSON.parse(response);
                    $('#data-table').empty();
                    data.data.forEach((row, index) => {
                        $('#data-table').append(`
                            <tr class='border-b hover:bg-gray-50'>
                                <td>${index + 1}</td>
                                <td>${row.kk_name}</td>
                                <td class="text-center">${parseInt(row.nominal).toLocaleString()}</td>
                                <td>${row.collector}</td>
                            </tr>
                        `);
                    });
                    $('#total-scans').text(data.data.length);
                    $('#total-nominal').text(parseInt(data.total_nominal).toLocaleString());
                },
                error: function() {
                    $('#data-table').html("<tr><td colspan='3' style='text-align: center;'>Gagal memuat data.</td></tr>");
                }
            });
        }

        // Panggil updateTable setiap 60 detik
        setInterval(updateTable, 10000);

        // Muat data pertama kali saat halaman dimuat
        $(document).ready(updateTable);

    </script>
</body>
</html>
