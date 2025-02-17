<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

include 'db.php';

// Ambil jadwal dari database
$stmt = $pdo->prepare("SELECT name, shift FROM users ORDER BY shift, name");
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
    $jadwal[$row['shift']][] = $row['name'];
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($hari_list as $hari): ?>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-2">
                        <?= $shift_mapping[$hari] ?>
                    </h2>
                    <table class="w-full text-sm text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-2 border">No.</th>
                                <th class="p-2 border">Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($jadwal[$hari])):
                                $no = 1;
                                foreach ($jadwal[$hari] as $nama): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 border text-center"><?= $no ?></td>
                                        <td class="p-2 border"><?= htmlspecialchars($nama) ?></td>
                                    </tr>
                                <?php 
                                $no++;
                                endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="2" class="p-2 border text-center text-gray-500">Tidak ada jadwal</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='menu.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>
</body>
</html>
