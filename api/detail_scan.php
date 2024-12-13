<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

    <style>
        /* Membatasi tinggi grafik agar sesuai dengan tinggi baris tabel */
        .chart-container {
            width: 100%;
            height: auto;
        }

        /* Mengurangi padding di sel tabel */
        table th, table td {
            text-align: left;
        }

        /* Menyesuaikan tinggi baris tabel agar lebih rapat */
        table tr {
            height: 28px; /* Menurunkan tinggi baris tabel */
            line-height: 1.2; /* Mengatur line height agar teks lebih padat */
        }

        /* Menyusun grafik horizontal lebih ramping */
        canvas {
            width: 100% !important;  /* Memastikan canvas mengikuti lebar elemen */
            height: 20px !important; /* Menyesuaikan tinggi grafik dengan baris */
        }
        /* Animasi berkedip */
        @keyframes blink {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        /* Terapkan animasi ke ikon bintang */
        .star-animate {
            animation: blink 1.5s infinite;
        }

        /* Berikan sedikit jeda untuk efek bintang bertahap */
        .star-delay-1 { animation-delay: 0.3s; }
        .star-delay-2 { animation-delay: 0.6s; }
        /* Animasi berputar */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Terapkan animasi berputar ke ikon bintang */
        .star-spin {
        animation: spin 2s linear infinite;
}


    </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col min-h-screen max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">Data Scan Jimpitan</h1>
        <p class="text-sm text-gray-500 mb-4">Hari <span id="tanggal"></span></p>
        <!-- Kontainer tabel dengan scrollable dan tinggi dinamis -->
        <div class="table-container flex-1 border rounded-md mb-4" style="font-size: 12px;">
            <?php
                // Tampilkan data dalam tabel
                if (count($data) > 0) {
                    echo "<table class='min-w-full border-collapse text-sm text-gray-700'>";
                    echo "<thead>
                            <tr class='bg-gray-100 border-b'>
                                <th>No.</th>
                                <th>Nama KK</th>
                                <th class='text-right'>Nominal</th>
                                <th>Jaga</th>
                            </tr>
                          </thead>";
                    echo "<tbody>";
                    $no = 1;
                    foreach ($data as $row) {
                    echo "<tr class='border-b hover:bg-gray-50' data-no='{$no}'>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row["kk_name"]); ?></td> 
                            <td style="text-align: center"><?php echo htmlspecialchars(number_format($row["nominal"], 0, ',', '.')); ?></td>
                            <td style="text-align: center"><?php echo htmlspecialchars($row["collector"]); ?></td>
                        </tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<div class='text-center py-4 text-gray-500'>
                           <ion-icon name='folder-open-outline' size='large'></ion-icon>
                           <p>Data tidak tersedia</p>
                          </div>";
                }
            ?>
        </div>

        <!-- Total Scan -->
        <div class="mt-4 font-bold text-gray-700 text-left">Total Scan: <?php echo number_format($total_scans, 0, ',', '.'); ?></div>

        <!-- Tombol Bulat -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='detail_scan.php'" title="Kembali ke halaman detail sebelumnya">
            <ion-icon name="arrow-back-outline"></ion-icon>
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
    setInterval(updateTable, 60000);

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
