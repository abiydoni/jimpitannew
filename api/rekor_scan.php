<?php
session_start();

// Check if user is logged in
// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Login kadaluarsa, silahkan login kembali!']);
    exit;
}

include 'db.php';

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
<div class="screen-2">
    <a style="font-weight: bold; font-size: 15px;">Recor Scan Terbanyak</a>
    <a style="color: grey; font-size: 10px;">Per : <span id="tanggal"></span></a>
    <div class="table-container overflow-x-auto bg-white rounded-lg shadow-md" style="font-size: 12px;">
        <?php
            // Eksekusi query
            $stmt = $pdo->prepare("
                SELECT 
                    collector, 
                    COUNT(*) AS jumlah_scan 
                FROM report
                GROUP BY collector
                ORDER BY jumlah_scan DESC
            ");
            $stmt->execute();

            // Fetch hasil
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Hitung total semua scan
                $total_scans = 0;
                foreach ($results as $row) {
                    $total_scans += $row['jumlah_scan'];
                }

            // Tampilkan hasil dalam tabel HTML
            if (count($results) > 0) {
                echo "<table>";
                echo "<thead><tr><th>Nama User</th><th>Jumlah Scan</th></tr></thead>";
                echo "<tbody>";
                foreach ($results as $row) {
                    echo "<tr>
                            <td>{$row['collector']}</td>
                            <td>{$row['jumlah_scan']}</td>
                        </tr>";
                }
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p>Tidak ada data untuk ditampilkan.</p>";
            }
        ?>
    </div>
    echo "<div style='margin-top: 1em; font-weight: bold;'>Total Scan: $total_scans</div>";

    <!-- Tombol Bulat -->
    <button class="round-button" onclick="window.location.href='detail_scan.php'" title="Kembali ke halaman detail sebelumnya">
    <span>&#8592;</span> <!-- Ikon panah kiri -->
</button>
</div>

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