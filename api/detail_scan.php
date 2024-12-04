<?php
session_start();

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

include 'db.php';

// Query data
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.nominal, report.collector 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal dan scan
$totalNominal = 0;
foreach ($data as $row) {
    $totalNominal += $row['nominal'];
}
$totalScan = count($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .table-container {
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .bold {
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>

<div class="flex flex-col min-h-screen max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
    <h1 style="font-weight: bold; font-size: 15px;">Data Scan Jimpitan</h1>
    <h2 style="color: grey; font-size: 10px;">Hari <span id="tanggal"></span></h2>

    <div class="table-container flex-1 mb-4">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama KK</th>
                    <th style="text-align: center">Nominal</th>
                    <th style="text-align: center">Jaga</th>
                </tr>
            </thead>
            <tbody id="data-table">
                <?php if ($data): ?>
                    <?php $no = 1; foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row["kk_name"]); ?></td>
                            <td style="text-align: center;"><?php echo number_format($row["nominal"], 0, ',', '.'); ?></td>
                            <td style="text-align: center;"><?php echo htmlspecialchars($row["collector"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- Baris Total -->
                    <tr class="bold">
                        <td colspan="2" style="text-align: right;">Total Nominal:</td>
                        <td style="text-align: center;"><?php echo number_format($totalNominal, 0, ',', '.'); ?></td>
                        <td></td>
                    </tr>
                    <tr class="bold">
                        <td colspan="2" style="text-align: right;">Total Scan:</td>
                        <td style="text-align: center;"><?php echo $totalScan; ?></td>
                        <td></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">Tidak ada data jimpitan hari ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- Tombol Bulat -->
    <button class="round-button" onclick="window.location.href='../index.php'">
        <span>&#8592;</span> <!-- Ikon panah kiri -->
    </button>
    <!-- Tombol Kedua -->
    <button class="second-button" onclick="window.location.href='rekor_scan.php'">
        <span>&#128200;</span> <!-- Ikon untuk tombol kedua (misalnya ikon kalkulator) -->
    </button>
</div>

<script>
    // Fungsi untuk memperbarui tabel
    function updateTable() {
        // Tampilkan indikator loading
        $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Loading...</td></tr>");
        $.get("get_data.php", function(data) {
            $("#data-table").html(data); // Masukkan data baru ke tabel
        }).fail(function() {
            $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Gagal memuat data.</td></tr>");
        });
    }

    // Panggil updateTable setiap 5 detik
    setInterval(updateTable, 100000);

    // Muat data pertama kali saat halaman dimuat
    $(document).ready(updateTable);
</script>
    <script>
        // Fungsi untuk menampilkan tanggal dalam format Indonesia
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

        // Menampilkan tanggal yang diformat ke dalam elemen dengan id "tanggal"
        document.getElementById("tanggal").textContent = formatTanggalIndonesia();
    </script>
    <style>
        /* Mengatur margin dan padding untuk elemen <a> */
        a {
            margin: 0;
            padding: 0;
        }
    </style>

</body>
</html>