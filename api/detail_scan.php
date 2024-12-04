<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit; // Hentikan eksekusi jika pengguna tidak terautentikasi
}

include 'db.php';

// Query data untuk shift hari ini
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.nominal, report.collector 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
");

// Execute query
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data Jimpitan</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .screen-2 {
            padding: 20px;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f1f1f1;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .round-button, .second-button {
            position: fixed;
            width: 50px;
            height: 50px;
            bottom: 20px;
            border-radius: 50%;
            background-color: #007BFF;
            color: white;
            font-size: 20px;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .round-button:hover, .second-button:hover {
            background-color: #0056b3;
            cursor: pointer;
        }
        .second-button {
            right: 80px;
        }
        .round-button {
            right: 20px;
        }
    </style>
</head>
<body>
<div class="screen-2">
    <a style="font-weight: bold; font-size: 15px;">Data Scan Jimpitan</a>
    <a style="color: grey; font-size: 10px;">Hari Ini : <span id="tanggal"></span></a>

    <!-- Tabel Data -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama KK</th>
                    <th style="text-align: center">Nominal</th>
                    <th style="text-align: center">Jaga</th>
                </tr>
            </thead>
            <tbody id="data-table">
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["kk_name"]); ?></td>
                        <td style="text-align: center"><?php echo htmlspecialchars(number_format($row["nominal"], 0, ',', '.')); ?></td>
                        <td style="text-align: center"><?php echo htmlspecialchars($row["collector"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data untuk hari ini.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol Bulat -->
    <button class="round-button" onclick="window.location.href='../index.php'">
        &#8592; <!-- Ikon panah kiri -->
    </button>
    <!-- Tombol Kedua -->
    <button class="second-button" onclick="window.location.href='rekor_scan.php'">
        &#128200; <!-- Ikon untuk tombol kedua -->
    </button>
</div>

<script>
    // Fungsi untuk memperbarui tabel
    function updateTable() {
        $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Loading...</td></tr>");
        $.get("get_data.php", function(data) {
            const rows = JSON.parse(data);
            if (rows.length > 0) {
                let html = rows.map(row => `
                    <tr>
                        <td>${row.kk_name}</td>
                        <td style="text-align: center">${parseInt(row.nominal).toLocaleString('id-ID')}</td>
                        <td style="text-align: center">${row.collector}</td>
                    </tr>
                `).join("");
                $("#data-table").html(html);
            } else {
                $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Tidak ada data untuk hari ini.</td></tr>");
            }
        }).fail(function() {
            $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Gagal memuat data.</td></tr>");
        });
    }

    // Fungsi untuk format tanggal Indonesia
    function formatTanggalIndonesia() {
        const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const tanggal = new Date();
        return `${hari[tanggal.getDay()]}, ${tanggal.getDate()} ${bulan[tanggal.getMonth()]} ${tanggal.getFullYear()}`;
    }

    // Set tanggal saat ini
    document.getElementById("tanggal").textContent = formatTanggalIndonesia();

    // Update tabel pertama kali
    $(document).ready(() => updateTable());
</script>
</body>
</html>
