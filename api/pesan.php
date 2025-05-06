<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';
// Cek jika ada parameter status di URL
$status = isset($_GET['status']) ? $_GET['status'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Pesan</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        .input-group {
            margin-bottom: 10px;
        }
        button {
            margin-left: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <!-- Loader GIF loading -->
    <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <img src="../assets/image/loading.gif" alt="Loading..." class="w-32 h-auto">
    </div>

    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg" style="max-width: 60vh;">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Kirim Pesan
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>
        <!-- Notifikasi Sukses -->
        <?php if ($status === 'success') : ?>
            <div class="bg-green-500 text-white p-4 rounded mb-4">
                Pesan berhasil dikirim!
            </div>
        <?php elseif ($status === 'error') : ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.
            </div>
        <?php endif; ?>

        <form method="post" action="send-wa.php">
            <!-- Input Pesan (tetap satu) -->
            <div class="input-group">
                <label>Pesan:</label><br>
                <textarea name="pesan" rows="4" cols="50" placeholder="Tulis pesan..."></textarea>
            </div>

            <!-- Input Nomor WA (bisa ditambah/hapus) -->
            <label>Nomor WA:</label>
            <div id="nomor-container">
                <div class="input-group">
                    <input type="text" name="nomorwa[]" placeholder="Contoh: 6281234567890">
                    <button type="button" onclick="tambahNomor()">+</button>
                </div>
            </div>

            <br>
            <button type="submit">Kirim</button>
        </form>

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
<script>
    function tambahNomor() {
        const container = document.getElementById('nomor-container');

        const div = document.createElement('div');
        div.className = 'input-group';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'nomorwa[]';
        input.placeholder = 'Contoh: 6281234567890';

        const hapusBtn = document.createElement('button');
        hapusBtn.type = 'button';
        hapusBtn.textContent = '–';
        hapusBtn.onclick = function () {
            container.removeChild(div);
        };

        div.appendChild(input);
        div.appendChild(hapusBtn);
        container.appendChild(div);
    }
</script>

</body>
</html>
