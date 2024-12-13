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
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            Rekor Scan Terbanyak 
            <ion-icon name="star" class="text-yellow-500 ml-2 star-spin"></ion-icon>
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
        </h1>
        <p class="text-sm text-gray-500 mb-4">Per : <span id="tanggal"></span></p>
        <!-- Kontainer tabel dengan scrollable dan tinggi dinamis -->
        <div class="table-container flex-1 border rounded-md mb-4" style="font-size: 12px;">
            <?php
                // Eksekusi query
                $stmt = $pdo->prepare("SELECT nama_u, COUNT(*) AS jumlah_scan FROM report GROUP BY kode_u ORDER BY jumlah_scan DESC");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Hitung total scan
                $total_scans = 0;
                foreach ($results as $row) {
                    $total_scans += $row['jumlah_scan'];
                }

                // Tampilkan data dalam tabel
                if (count($results) > 0) {
                    echo "<table class='min-w-full border-collapse text-sm text-gray-700'>";
                    echo "<thead>
                            <tr class='bg-gray-100 border-b'>
                                <th>No.</th>
                                <th>Nama User</th>
                                <th class='text-right'>Jumlah Scan</th>
                                <th>Grafik</th>
                            </tr>
                          </thead>";
                    echo "<tbody>";
                    $no = 1;
                    foreach ($results as $row) {
                    // Menentukan ikon bintang atau jempol berdasarkan peringkat
                    if ($no <= 3) {
                        // Peringkat 1 hingga 3: ikon bintang
                        $bintang = str_repeat('<ion-icon name="star" class="text-yellow-500 star-animate"></ion-icon>', $no);
                    } elseif ($no >= 4 && $no <= 6) {
                        // Peringkat 4 hingga 6: ikon jempol perunggu
                        $bintang = '<ion-icon name="thumbs-up" class="text-orange-500 star-animate"></ion-icon>';
                    } else {
                        // Peringkat 7 ke atas: ikon jempol perunggu
                        $bintang = str_repeat('<ion-icon name="thumbs-up" class="text-orange-500 star-animate"></ion-icon>', 3);
                    }
                    echo "<tr class='border-b hover:bg-gray-50' data-no='{$no}'>
                                <td>{$no}</td>
                                <td>{$row['nama_u']} $bintang</td>
                                <td class='text-right'>" . number_format($row['jumlah_scan'], 0, ',', '.') . "</td>
                                <td>
                                    <div class='chart-container'>
                                        <canvas id='chart_{$no}'></canvas>
                                    </div>
                                </td>
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

        // Membuat grafik untuk setiap pengguna
        window.addEventListener('load', function () {
            const rows = document.querySelectorAll('tr[data-no]'); // Menargetkan semua baris dalam tabel

            rows.forEach(row => {
                const no = row.getAttribute('data-no');
                const chartContainer = row.querySelector('.chart-container');

                // Mendapatkan elemen canvas untuk grafik
                const ctx = document.getElementById('chart_' + no).getContext('2d');

                // Data untuk grafik
                const jumlahScan = parseInt(row.cells[2].textContent.replace(/[^\d]/g, '')); // Mengambil jumlah scan dari tabel
                const totalScans = <?php echo $total_scans; ?>;

                // Membuat grafik batang horizontal
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [row.cells[1].textContent], // Nama collector
                        datasets: [{
                            label: 'Jumlah Scan',
                            data: [jumlahScan],
                            backgroundColor: '#4CAF50', // Warna batang
                            barThickness: 20 // Mengatur ketebalan batang grafik
                        }]
                    },
                    options: {
                        responsive: true,
                        indexAxis: 'y', // Grafik horizontal
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: totalScans, // Set batas maksimal berdasarkan total scan
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
            });
        });
    </script>
</body>
</html>
