<?php
session_start();

// Check if user is logged in
// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Pengguna tidak terautentikasi']);
    exit; // Hentikan eksekusi jika pengguna tidak terautentikasi
}
include 'db.php';

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;600;800&display=swap'>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="screen-1">
    <H4 class="text-xl font-bold">Data Scan Jimpitan</H4>
    <div class="table-container overflow-x-auto bg-white rounded-lg shadow-md" style="font-size: 12px;">
        <table>
            <thead>
                <tr>
                    <th>Nama KK</th>
                    <th style="text-align: center">Nominal</th>
                    <th style="text-align: center">Jaga</th>
                </tr>
            </thead>
            <tbody id="data-table">
            <?php foreach($data as $row): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?php echo htmlspecialchars($row["kk_name"]); ?></td> 
                    <td style="text-align: center"><?php echo htmlspecialchars(number_format($row["nominal"], 0, ',', '.')); ?></td>
                    <td style="text-align: center"><?php echo htmlspecialchars($row["collector"]); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol Bulat -->
    <button class="round-button" onclick="window.location.href='../index.php'">
        <span>&#8592;</span> <!-- Ikon panah kiri -->
    </button>
</div>

<script>
    // Fungsi untuk memperbarui tabel
    function updateTable() {
        // Tampilkan indikator loading
        $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Loading...</td></tr>");
        $.get("get_data.php", function(data) {
            $("#data-table").html(data); // Masukkan data baru ke tabel
        }).fail(function() {
            $("#data-table").html("<tr><td colspan='3' style='text-align: center;'>Gagal memuat data.</td></tr>");
        });
    }

    // Panggil updateTable setiap 5 detik
    setInterval(updateTable, 5000);

    // Muat data pertama kali saat halaman dimuat
    $(document).ready(updateTable);
</script>

</body>
</html>