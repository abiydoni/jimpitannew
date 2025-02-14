<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jimpitan</title>
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
    </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg" style="max-width: 60vh;">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Data Jimpitan
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>
        
        <!-- Kontainer tabel dengan scrollable dan tinggi dinamis -->
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-width: 60vh; max-height: 80vh; font-size: 12px;">
            <?php
                // Eksekusi query dengan JOIN untuk mengambil kk_name dari tabel master_kk
                $stmt = $pdo->prepare("
                    SELECT r.report_id, m.kk_name, SUM(r.nominal) AS jumlah_nominal
                    FROM report r
                    LEFT JOIN master_kk m ON r.report_id = m.code_id
                    GROUP BY r.report_id, m.kk_name
                    ORDER BY r.report_id ASC
                ");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Hitung total nominal
                $total_nominal = 0;
                foreach ($results as $row) {
                    $total_nominal += $row['jumlah_nominal'];
                }

                // Tampilkan data dalam tabel
                if (count($results) > 0) {
                    echo "<table class='min-w-full border-collapse text-sm text-gray-700'>";
                    echo "<thead class='sticky top-0'>
                            <tr class='bg-gray-100 border-b'>
                                <th>No.</th>
                                <th>Kode</th>
                                <th>Nama Kepala Keluarga</th>
                                <th>Jumlah Nominal</th>
                            </tr>
                          </thead>";
                    echo "<tbody>";
                    $no = 1;
                    foreach ($results as $row) {
                        echo "<tr class='border-b hover:bg-gray-50'>
                                <td>{$no}</td>
                                <td>{$row['report_id']}</td>
                                <td>{$row['kk_name']}</td>
                                <td>" . number_format($row['jumlah_nominal'], 0, ',', '.') . "</td>
                            </tr>";
                        $no++;
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

        <!-- Total Nominal -->
        <div class="mt-4 font-bold text-gray-700 text-left">Total Jimpitan: <?php echo number_format($total_nominal, 0, ',', '.'); ?></div>

        <!-- Tombol Bulat -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='menu.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
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
</body>
</html>
