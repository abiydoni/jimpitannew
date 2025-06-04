<?php
    session_start();
include 'header.php'; // Sudah termasuk koneksi dan session

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: ../login.php'); // Redirect to login page
        exit;
    }

    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
        header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
        exit;
    }
    // Include the database connection
    include 'api/db.php';

    // Mengambil parameter nama dari URL
    $nama_dicari = isset($_GET['nama']) ? $_GET['nama'] : '';

    // ... existing code ...

    if ($nama_dicari) {
        // Query untuk mencari data berdasarkan nama
        $query = "SELECT * FROM master_kk WHERE kk_name = :nama";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nama', $nama_dicari, PDO::PARAM_STR);
        $stmt->execute();
        
        // Cek apakah data ditemukan
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "Data tidak ditemukan.";
            exit;
        }
    } else {
        echo "Nama tidak valid.";
        exit;
    }

    // Menutup statement (tidak perlu menutup koneksi PDO secara manual)
    $stmt = null;

// ... existing code ...
?>

            
<div class="flex flex-wrap justify-center gap-8 p-4">
    <!-- Card Container -->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
        <!-- Profile Image -->
        <div class="flex items-center justify-center">
            <img src="<?= htmlspecialchars($data['kk_foto']) ?>" alt="Profile" class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-md">
        </div>
        
        <!-- Name and Position -->
        <div class="text-center mt-4">
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($data['kk_name']) ?></h1>
            <p class="text-blue-500 text-sm font-medium"><?= htmlspecialchars($data['code_id']) ?></p>
        </div>
        
        <!-- Divider -->
        <hr class="my-4 border-gray-300">
        
        <!-- Contact Information -->
        <div class="text-center text-gray-600">
            <p><strong>Alamat : </strong><?= htmlspecialchars($data['kk_alamat']) ?></p>
            <p><strong>No HP : </strong><?= htmlspecialchars($data['kk_hp']) ?></p>
        </div>
    </div>

    <!-- Card Container 2-->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
        <div id="qrcode-container" class="space-y-4 flex items-center justify-center"></div>
    </div>
    <!-- Card Container 3-->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
        <button onclick="printPage()" 
            class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition duration-200 ease-in-out w-full max-w-[200px]">
            <!-- Ikon Cetak -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18h12v-4H6v4zM6 22h12M8 18v4m8-4v4" />
            </svg>
            Cetak
        </button>
    </div>
    <!-- Card Container 4-->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
        <a href="javascript:history.back()" 
            class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition duration-200 ease-in-out">
            <!-- Icon Panah -->
            <svg xmlns="kk.php" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-2 w-full max-w-[200px]">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
</div>
<?php include 'footer.php'; ?>
    <script>
    function generateQRCodes() {
        // Ambil code_id langsung dari data PHP
        const codeId = "<?= htmlspecialchars($data['code_id']) ?>";
        
        // Kosongkan konten QR code sebelumnya
        const qrContainer = document.getElementById("qrcode-container");
        qrContainer.innerHTML = "";

        // Buat elemen div untuk QR code
        const qrDiv = document.createElement("div");
        qrContainer.appendChild(qrDiv);

        // Generate QR code
        new QRCode(qrDiv, {
            text: codeId,
            width: 250,
            height: 250,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    // Panggil generateQRCodes() saat halaman selesai dimuat
    window.onload = generateQRCodes;

    </script>


    <script>
        function printPage() {
            const printContent = document.querySelector('.flex.flex-wrap.justify-center.gap-8.p-4'); // Ambil konten yang ingin dicetak
            const originalContent = document.body.innerHTML; // Simpan konten asli
            
            // Tambahkan gaya untuk tampilan cetak
            const printStyles = `
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 20px;
                    }
                    .flex {
                        display: flex;
                        flex-wrap: wrap; /* Pastikan konten bisa berjajar */
                        justify-content: center; /* Pusatkan konten */
                    }
                    .gap-8 {
                        gap: 2rem; /* Atur jarak antar elemen */
                    }
                    .max-w-xs { /* Ubah ukuran maksimum container */
                        max-width: 300px; /* Sesuaikan ukuran sesuai kebutuhan */
                    }
                </style>            
            `;
            
            document.body.innerHTML = printStyles + printContent.innerHTML; // Ganti konten dengan yang ingin dicetak
            window.print(); // Panggil fungsi cetak
            document.body.innerHTML = originalContent; // Kembalikan konten asli
        }
    </script>    
