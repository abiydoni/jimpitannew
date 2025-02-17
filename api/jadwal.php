<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';

$stmt_users = $pdo->prepare("SELECT * FROM users");
$stmt_users->execute();
$data = $stmt_users->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jaga</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <div class="flex flex-col max-w-4xl mx-auto p-4 bg-white shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">
            <ion-icon name="star" class="text-yellow-500 ml-1 star-spin"></ion-icon>
            Data Jaga
        </h1>
        <p class="text-sm text-gray-500 mb-4">Tanggal: <span id="tanggal"></span></p>

        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 73vh;">
            <?php if (!empty($results)): ?>
                <table class="min-w-full border-collapse text-sm text-gray-700">
                    <thead class="sticky top-0 bg-gray-100 border-b">
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($data as $row):
                        ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td><?= $no ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                            </tr>
                        <?php
                        $no++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-4 text-gray-500">
                    <ion-icon name="folder-open-outline" size="large"></ion-icon>
                    <p>Data tidak tersedia</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Total Nominal -->
        <div class="mt-4 font-bold text-gray-700 text-left">Jumlah orang: <?= number_format($no, 0, ',', '.') ?></div>

        <!-- Tombol Kembali -->
        <button class="fixed bottom-4 right-4 w-12 h-12 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
                onclick="window.location.href='menu.php'" title="Kembali ke halaman menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>

    <script>
        function formatTanggalIndonesia() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            
            const tgl = new Date();
            return `${hari[tgl.getDay()]}, ${tgl.getDate()} ${bulan[tgl.getMonth()]} ${tgl.getFullYear()}`;
        }

        document.getElementById("tanggal").textContent = formatTanggalIndonesia();
    </script>
</body>
</html>
