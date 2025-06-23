<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

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
    <title>Kirim Pesan Group</title>
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
    <div class="relative z-10 flex flex-col max-w-4xl mx-auto p-4 shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Kirim Pesan Group WA
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
        <form method="post" action="send_wa_group.php">
            <label>ID Group WA:</label>
            <div id="nomor-container">
                <div class="input-group flex items-center">
                    <input type="text" name="groupId[]" value="120363398680818900@g.us" class="flex-1 px-2 py-1 border rounded">
                    <button type="button" onclick="tambahNomor(event)" class="ml-2 px-2 py-1 bg-green-500 text-white rounded">+</button>
                </div>
            </div>
            <?php 
            ?>
            <?php include 'ambil_data_jaga.php'; ?>
            <div class="input-group mt-4">
                <label>Pesan:</label><br>
                <textarea name="message" rows="15" cols="50" placeholder="Tulis pesan..." class="w-full px-2 py-2 border rounded"><?php echo htmlspecialchars($pesan); ?></textarea>
            </div>

            <br>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Kirim</button>
        </form>

        <!-- Tombol Bulat -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='../index.php'" title="Kembali ke halaman menu">
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

        // Fungsi untuk menambah input nomor WA
        function tambahNomor(event) {
            event.preventDefault(); // Mencegah tindakan default (seperti pengalihan atau submit form)

            const container = document.getElementById('nomor-container');

            const div = document.createElement('div');
            div.className = 'input-group flex items-center';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'groupId[]';
            input.placeholder = '120363398680818900@g.us';
            input.className = 'flex-1 px-2 py-1 border rounded';

            const hapusBtn = document.createElement('button');
            hapusBtn.type = 'button'; // Pastikan ini adalah tombol biasa, bukan submit
            hapusBtn.textContent = '–';
            hapusBtn.className = 'ml-2 px-2 py-1 bg-red-500 text-white rounded';
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
