<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

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
    <title>Inventory Barang</title>
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
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
            <!-- Loader GIF loading -->
    <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <img src="../assets/image/loading.gif" alt="Loading..." class="w-32 h-auto">
    </div>

    <div class="relative z-10 flex flex-col max-w-4xl mx-auto p-4 shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Data Barang
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>
        <!-- Kontainer tabel dengan scrollable dan tinggi dinamis -->
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto bg-white bg-opacity-50" style="max-height: 80vh; font-size: 12px;">
            <?php
                // Eksekusi query
                $stmt = $pdo->prepare("SELECT nama, jumlah, COUNT(*) AS jumlah_barang FROM tb_barang GROUP BY nama ORDER BY jumlah_barang ASC");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Hitung total scan
                $total_barang = 0;
                foreach ($results as $row) {
                    $total_barang += $row['jumlah_barang'];
                }

                // Tampilkan data dalam tabel
                if (count($results) > 0) {
                    echo "<table class='min-w-full border-collapse text-sm text-gray-700'>";
                    echo "<thead class='sticky top-0'>
                            <tr class='bg-gray-100 border-b'>
                                <th>No.</th>
                                <th>Nama Barang</th>
                                <th class='text-right'>Jumlah</th>
                            </tr>
                          </thead>";
                    echo "<tbody>";
                    $no = 1;
                    foreach ($results as $row) {
                        echo "<tr class='border-b hover:bg-gray-50'>
                                <td>{$no}</td>
                                <td>{$row['nama']}</td>
                                <td class='text-right'>" . number_format($row['jumlah'], 0, ',', '.') . "</td>
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
        <div class="mt-4 font-bold text-gray-700 text-left">Total Jenis Barang: <?php echo number_format($total_barang, 0, ',', '.'); ?></div>

        <!-- Tombol Bulat -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='../index.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>
<script>
  // Menambahkan event listener untuk semua elemen tombol/link
  document.querySelectorAll('button, a, input[type="submit"]').forEach(element => {
    element.addEventListener('click', function (e) {
      // Mencegah form disubmit langsung atau link berpindah halaman
      e.preventDefault();

      // Tampilkan loader
      document.getElementById('loader').classList.remove('hidden');
      
      // Jika itu adalah form submit, submit form setelah beberapa detik
      if (this.type === 'submit') {
        setTimeout(function() {
          this.closest('form').submit();
        }.bind(this), 500); // Tunggu 500ms sebelum submit form
      } else {
        // Jika itu link, pindahkan halaman setelah beberapa detik
        setTimeout(() => {
          window.location.href = this.href;
        }, 500); // Tunggu 500ms sebelum pindah halaman
      }
    });
  });
</script>

    <script>
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function (e) {
        if (
            this.target !== '_blank' &&
            this.href &&
            !this.href.startsWith('javascript') &&
            !this.href.startsWith('#')
        ) {
            document.getElementById('loader').classList.remove('hidden');
        }
        });
    });
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
<script>
    const savedColor = localStorage.getItem('overlayColor') || '#000000E6';
    document.body.style.backgroundColor = savedColor;
</script>
</body>
</html>
