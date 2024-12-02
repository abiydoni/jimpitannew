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
    <title>Detail</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col min-h-screen max-w-4xl mx-auto mt-8 p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">Rekor Scan Terbanyak</h1>
        <p class="text-sm text-gray-500 mb-4">Per : <span id="tanggal"></span></p>

        <!-- Kontainer tabel dengan scrollable dan tinggi dinamis -->
        <div class="flex-1 overflow-y-auto border rounded-md mb-4">
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
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Hitung total scan
                $total_scans = 0;
                foreach ($results as $row) {
                    $total_scans += $row['jumlah_scan'];
                }

                if (count($results) > 0) {
                    echo "<table class='min-w-full border-collapse text-sm text-gray-700'>";
                    echo "<thead>
                            <tr class='bg-gray-100 border-b'>
                                <th class='px-4 py-2 text-left'>No.</th>
                                <th class='px-4 py-2 text-left'>Nama User</th>
                                <th class='px-4 py-2 text-right'>Jumlah Scan</th>
                                <th class='px-4 py-2 text-center'>Grafik</th>
                            </tr>
                          </thead>";
                    echo "<tbody>";
                    $no = 1;
                    foreach ($results as $row) {
                        // Hitung persentase jumlah scan per user terhadap total scan
                        $persentase = ($row['jumlah_scan'] / $total_scans) * 100;
                        echo "<tr class='border-b hover:bg-gray-50'>
                                <td class='px-4 py-2'>{$no}</td>
                                <td class='px-4 py-2'>{$row['collector']}</td>
                                <td class='px-4 py-2 text-right'>" . number_format($row['jumlah_scan'], 0, ',', '.') . "</td>
                                <td class='px-4 py-2'>
                                    <canvas id='chart_{$no}' class='w-48 h-6'></canvas>
                                </td>
                            </tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<div class='text-center py-4 text-gray-500'>"
                       . "<ion-icon name='folder-open-outline' size='large'></ion-icon>"
                       . "<p>Data tidak tersedia</p>"
                       . "</div>";
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

        // Menambahkan grafik batang horizontal di setiap baris tabel dengan panjang berdasarkan persentase
        <?php $no = 1; ?>
        <?php foreach ($results as $row): ?>
            const ctx_<?= $no ?> = document.getElementById('chart_<?= $no ?>').getContext('2d');
            new Chart(ctx_<?= $no ?>, {
                type: 'bar', // Grafik batang
                data: {
                    labels: [<?= json_encode($row['collector']); ?>],
                    datasets: [{
                        label: 'Jumlah Scan',
                        data: [<?= $row['jumlah_scan']; ?>],
                        backgroundColor: '#4CAF50', // Warna batang
                        borderColor: '#388E3C', // Warna border batang
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y', // Membuat grafik menjadi horizontal
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: <?= $total_scans ?>, // Set batas maksimal berdasarkan total scan
                            display: false // Menyembunyikan sumbu X
                        },
                        y: {
                            display: false // Menyembunyikan sumbu Y
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // Menyembunyikan legend
                        }
                    }
                }
            });
            <?php $no++; ?>
        <?php endforeach; ?>
    </script>
</body>
</html>
