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
    <title>Data KK</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

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
        <!-- Tambahkan ini di dalam <div class="flex flex-col ...">, sebelum tombol Kirim -->
        <form onsubmit="sendMessageWhatsApp(event)" class="space-y-4">
            <div>
                <label for="phone" class="block text-sm font-medium">Nomor WhatsApp</label>
                <input type="text" id="phone" name="phone" required
                    class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="628xxx">
            </div>
            <div>
                <label for="message" class="block text-sm font-medium">Pesan</label>
                <textarea id="message" name="message" required
                        class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="3" placeholder="Tulis pesan Anda di sini..."></textarea>
            </div>
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Kirim Pesan
            </button>
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
        async function send MessageWhatsApp(event) {
        event.preventDefault(); // Mencegah reload halaman

        const phone = document.getElementById("phone").value.trim();
        const message = document.getElementById("message").value.trim();

        if (!phone || !message) {
            alert("Nomor dan pesan wajib diisi!");
            return;
        }

        const url = "https://wa.appsbee.my.id/send-message";
        const payload = {
            phoneNumber: phone,
            message: message,
        };

        try {
            const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
            });

            const result = await response.json();
            console.log(result);
            alert("Pesan berhasil dikirim: " + JSON.stringify(result));
        } catch (error) {
            console.error("Gagal mengirim pesan:", error);
            alert("Terjadi kesalahan saat mengirim pesan.");
        }
        }
    </script>

</body>
</html>
