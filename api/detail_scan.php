<?php
session_start();

// Check if user is logged in
// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit; // Hentikan eksekusi jika pengguna tidak terautentikasi
}
include 'db.php';

// Prepare the SQL statement to select only today's shift
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
");

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    <!-- <link rel="manifest" href="manifest.json"> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<div class="flex flex-col min-h-screen max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
    <a style="font-weight: bold; font-size: 15px;">Data Scan Jimpitan</a>
    <a style="color: grey; font-size: 10px;">Hari Ini : <span id="tanggal"></span></a>
    <div class="table-container flex-1 overflow-y-auto border rounded-md mb-4" style="font-size: 12px;">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama KK</th>
                    <th style="text-align: center">Nominal</th>
                    <th style="text-align: center">Jaga</th>
                </tr>
            </thead>
            <tbody id="data-table">
            <?php $no = 1; foreach($data as $row): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row["kk_name"]); ?></td> 
                    <td style="text-align: center"><?php echo htmlspecialchars(number_format($row["nominal"], 0, ',', '.')); ?></td>
                    <td style="text-align: center"><?php echo htmlspecialchars($row["collector"]); ?></td>
                </tr>
            <?php endforeach; ?>
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