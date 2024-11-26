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

        /* Styling container tabel */
        .table-container {
            max-width: 50%;       /* Lebar maksimal tabel */
            overflow-x: auto;      /* Scroll horizontal */
            overflow-y: auto;      /* Scroll vertikal */
            max-height: 100px;     /* Maksimal tinggi tabel */
            border: 1px solid #ddd; /* Bingkai tabel */
            border-radius: 5px;    /* Membulatkan bingkai */
        }

        /* Styling tabel */
        table {
            width: 60%;
            border-collapse: collapse;
            text-align: left;
            min-width: 100px; /* Lebar minimal tabel */
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd; /* Garis antar sel */
        }

        th {
            background-color: #007BFF; /* Warna latar header */
            color: white;             /* Warna teks header */
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            background-color: white; /* Warna latar data */
        }

        /* Styling hover pada baris tabel */
        tr:hover td {
            background-color: #f1f1f1; /* Warna saat baris dihover */
        }
    </style>

</head>
<body>
<div class="screen-1">
    <H4>Data Scan Jimpitan</H4>
    <div style="font-size: 12px;">
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
            <!-- Icon Panah -->
            <svg xmlns="kk.php" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-2 w-full max-w-[200px]">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
    </div>
</div>
</body>
</html>