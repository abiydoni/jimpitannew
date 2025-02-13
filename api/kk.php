<?php
session_start();

// Check if user is logged in
// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit; // Hentikan eksekusi jika pengguna tidak terautentikasi
}
include 'db.php';

try {
    // Ambil data menu dari database menggunakan PDO
    $stmt = $pdo->prepare("SELECT * FROM master_kk");
    $stmt->execute();
    $kk = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
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

<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col min-h-screen max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
        <a style="font-weight: bold; font-size: 15px;">Data KK</a>
        <a style="color: grey; font-size: 10px;">Hari <span id="tanggal"></span></a>
        <!-- <div class="table-container flex-1 overflow-y-auto border rounded-md mb-4" style="font-size: 12px;"> -->
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-width: 60vh; max-height: 75vh; font-size: 12px;">
            <table class='min-w-full border-collapse text-sm text-gray-700'>
                <thead class="sticky top-0">
                    <tr class='bg-gray-100 border-b'>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama KK</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; foreach($kk as $row): ?>
                    <tr class="border-b hover:bg-gray-100">
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row["code_id"]); ?></td> 
                        <td><?php echo htmlspecialchars($row["kk_name"]); ?></td> 
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
            onclick="window.location.href='menu.php'" title="Pergi ke menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>
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