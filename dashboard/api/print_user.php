<?php
// Koneksi ke database
include 'db.php';
// Prepare and execute the SQL statement
$stmt = $pdo->prepare("SELECT id_code,user_name,name,password,shift,role FROM users");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="../js/cetak_user.js"></script> <!-- Memanggil file JavaScript eksternal -->
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5 text-center">Report Users</h1>
        <!-- Tambah tombol cetak -->
        <div class="mb-5 text-center">
            <button onclick="printTable()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Cetak Laporan
            </button>
            <button >
                <a href="../jadwal.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    < Kembali
                </a>
            </button>
            </button>
        </div>
        <!-- Tabel Data Users -->
        <div id="printableTable">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">ID Code</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Shift</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light">
                    <?php
                    if ($data) {  // Menggunakan $data dari PDO
                        foreach($data as $row) {  // Menggunakan foreach untuk array PDO
                            echo "<tr class='border-b border-gray-200 hover:bg-gray-100'>";
                            echo "<td class='py-3 px-6 text-left'>" . htmlspecialchars($row["id_code"]) . "</td>";
                            echo "<td class='py-3 px-6 text-left'>" . htmlspecialchars($row["name"]) . "</td>";
                            echo "<td class='py-3 px-6 text-left'>" . htmlspecialchars($row["shift"]) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='py-3 px-6 text-center'>Tidak ada data</td></tr>";
                    }
                    // Menghapus $conn->close() karena menggunakan PDO
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
