<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

// Jika tahun dipilih
$selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Eksekusi query
$stmt = $pdo->prepare("SELECT 
    MONTH(jimpitan_date) AS month, 
    SUM(nominal) AS total_nominal
FROM 
    report
WHERE 
    YEAR(jimpitan_date) = :year
GROUP BY 
    MONTH(jimpitan_date)
ORDER BY 
    month ASC;
");
// Menyiapkan dan mengeksekusi query
$stmt = $conn->prepare($sql);
$stmt->bindParam(':year', $selected_year, PDO::PARAM_INT);
$stmt->execute();

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
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg" style="max-width: 60vh;">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Data Barang
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>
        <!-- Kontainer tabel dengan scrollable dan tinggi dinamis -->
        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-width: 60vh; max-height: 80vh; font-size: 12px;">
            <!-- Form untuk memilih tahun -->
            <form method="POST" class="mb-6">
                <label for="year" class="text-xl font-semibold mr-4">Pilih Tahun:</label>
                <select name="year" id="year" class="p-2 border rounded">
                    <?php
                    // Menampilkan dropdown untuk memilih tahun
                    $current_year = date('Y');
                    for ($i = $current_year; $i >= 2000; $i--) {
                        $selected = ($i == $selected_year) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="ml-4 p-2 bg-blue-500 text-white rounded">Tampilkan</button>
            </form>

            <!-- Tabel Laporan Jimpitan -->
            <h2 class="text-2xl font-semibold mb-4">Laporan Jimpitan Tahun <?php echo $selected_year; ?></h2>
            <table class="min-w-full border-collapse text-sm text-gray-700">
                <thead class="sticky top-0">
                    <tr class="bg-gray-100 border-b">
                        <th class="py-2 px-4 border-b text-left">Bulan</th>
                        <th class="py-2 px-4 border-b text-left">Total Jimpitan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Inisialisasi array untuk bulan
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];

                    // Menampilkan data bulan dan total nominal
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $data = [];
                    foreach ($result as $row) {
                        $data[$row['month']] = $row['total_nominal'];
                    }

                    // Tampilkan tabel untuk setiap bulan
                    for ($i = 1; $i <= 12; $i++) {
                        $total_nominal = isset($data[$i]) ? $data[$i] : 0;
                        echo "<tr class='border-b hover:bg-gray-50'>";
                        echo "<td class='py-2 px-4 border-b'>" . $months[$i] . "</td>";
                        echo "<td class='py-2 px-4 border-b'>" . number_format($total_nominal) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Tombol Bulat -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='menu.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>
</body>
</html>
