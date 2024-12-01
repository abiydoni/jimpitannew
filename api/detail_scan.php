<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Pengguna tidak terautentikasi']);
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="screen-2">
    <a style="font-weight: bold; font-size: 15px;">Data Scan Jimpitan</a>
    <a style="color: grey; font-size: 10px;">Hari Ini : <span id="tanggal"></span></a>
    <div class="table-container overflow-x-auto bg-white rounded-lg shadow-md" style="font-size: 12px;">
        <table>
            <thead>
                <tr>
                    <th>Nama KK</th>
                    <th style="text-align: center">Nominal</th>
                    <th style="text-align: center">Jaga</th>
                </tr>
            </thead>
            <tbody id="data-table">
            <?php foreach($data as $row): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?php echo htmlspecialchars($row["kk_name"]); ?></td> 
                    <td style="text-align: center"><?php echo htmlspecialchars(number_format($row["nominal"], 0, ',', '.')); ?></td>
                    <td style="text-align: center"><?php echo htmlspecialchars($row["collector"]); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol-tombol di samping satu sama lain -->
    <div class="button-container">
        <!-- Tombol Navigasi Kembali -->
        <button class="round-button" onclick="window.location.href='../index.php'">
            <span>&#8592;</span> <!-- Ikon panah kiri -->
        </button>
    </div>
        <!-- Tombol Baru - Navigasi ke Halaman Lain -->
        <button class="round-button" onclick="window.location.href='halaman_lain.php'">
            <span>&#8594;</span> <!-- Ikon panah kanan -->
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

    /* Wadah tombol agar tombol-tombolnya disusun berdampingan */
    .button-container {
        display: flex;
        gap: 10px; /* Memberi jarak antar tombol */
        margin-top: 20px; /* Memberi jarak atas jika perlu */
    }

    /* Mengatur gaya untuk tombol-tombol */
    .round-button {
        background-color: #4CAF50;
        border: none;
        color: white;
        padding: 15px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        transition: background-color 0.3s ease;
    }

    .round-button:hover {
        background-color: #45a049;
    }

    .round-button span {
        font-size: 20px;
    }
</style>

</body>
</html>
