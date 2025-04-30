<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

include 'db.php';

// Ambil data jadwal dengan jumlah scan
$query = "
    SELECT u.shift, u.name, u.id_code, 
           COALESCE(COUNT(r.kode_u), 0) AS total_scan 
    FROM users u
    LEFT JOIN report r ON u.id_code = r.kode_u
    GROUP BY u.shift, u.name, u.id_code
    ORDER BY u.shift, u.name
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapping shift dari Inggris ke Indonesia
$shift_mapping = [
    "Monday"    => "Senin",
    "Tuesday"   => "Selasa",
    "Wednesday" => "Rabu",
    "Thursday"  => "Kamis",
    "Friday"    => "Jumat",
    "Saturday"  => "Sabtu",
    "Sunday"    => "Minggu"
];

// Grupkan data berdasarkan shift
$jadwal = [];
foreach ($data as $row) {
    $jadwal[$row['shift']][] = $row;
}

// Urutan hari dalam bahasa Inggris
$hari_list = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Jaga</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="max-w-6xl mx-auto p-4">
        <h1 class="text-2xl font-bold text-gray-700 mb-4 text-center">
            <ion-icon name="calendar-outline" class="text-blue-500"></ion-icon>
            Jadwal Jaga Mingguan
        </h1>

        <!-- Grid untuk menampilkan tabel -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($hari_list as $hari): ?>
                <div class="bg-white shadow-md rounded-lg p-4 w-full">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-2">
                        <?= $shift_mapping[$hari] ?>
                    </h2>
                    <table class="w-full text-sm text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="border">No.</th>
                                <th class="border">Nama</th>
                                <th class="border">Scan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($jadwal[$hari])):
                                $no = 1;
                                foreach ($jadwal[$hari] as $row): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="border text-center"><?= $no ?></td>
                                        <td class="border"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="border text-center"><?= number_format($row['total_scan'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php 
                                $no++;
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="3" class="border text-center text-gray-500">Tidak ada jadwal</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='../index.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>
</body>
</html>
