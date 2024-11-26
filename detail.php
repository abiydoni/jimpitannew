<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}
include 'api/db.php';

// Prepare the SQL statement to select only today's shift
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
");

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail</title>
    <!-- <link rel="manifest" href="manifest.json"> -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        /* Styling umum untuk halaman */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }

        /* Container untuk tabel */
        .table-container {
            width: 100%;             /* Container mengikuti lebar halaman */
            overflow-x: auto;        /* Scroll horizontal jika konten terlalu lebar */
            overflow-y: auto;        /* Scroll vertikal jika konten terlalu tinggi */
            max-height: 400px;       /* Batas tinggi maksimum untuk tabel */
            border: 1px solid #ddd;  /* Bingkai container tabel */
            border-radius: 5px;      /* Membulatkan sudut */
        }

        /* Styling tabel */
        table {
            width: 100%;             /* Tabel menyesuaikan lebar container */
            border-collapse: collapse; /* Menggabungkan border */
            text-align: left;        /* Konten rata kiri */
        }

        th, td {
            padding: 10px;           /* Padding untuk sel */
            border: 1px solid #ddd;  /* Garis antar sel */
        }

        th {
            background-color: #007BFF; /* Warna header tabel */
            color: white;              /* Warna teks header */
            font-weight: bold;
            text-transform: uppercase; /* Teks kapital */
        }

        td {
            background-color: white;   /* Warna latar isi */
        }

        /* Efek hover pada baris */
        tr:hover td {
            background-color: #f1f1f1; /* Warna saat baris dihover */
        }
    </style>
</head>
<body>
<div class="screen-1">
    <H4>Data Scan Jimpitan</H4>
    <div class="table-container" style="font-size: 12px;">
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">Nama KK</th>
                    <th style="text-align: center;">Nominal</th>
                    <th style="text-align: center;">Jaga</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($data as $tarif): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?php echo htmlspecialchars($tarif["kk_name"]); ?></td> 
                    <td><?php echo htmlspecialchars($tarif["nominal"]); ?></td>
                    <td><?php echo htmlspecialchars($tarif["collector"]); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full">
        <a href="index.php" 
            class="flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition duration-200 ease-in-out">
            Kembali
        </a>
    </div>
</div>
</body>
</html>