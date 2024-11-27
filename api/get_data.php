<?php
session_start();

// Cek login
if (!isset($_SESSION['user'])) {
    echo "Unauthorized";
    exit;
}

include 'db.php';

// Query data
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.nominal, report.collector 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = '2024-11-24'
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Variabel untuk menyimpan total nominal dan total scan
$totalNominal = 0;
$totalScan = count($data);

// Tampilkan data sebagai HTML
if ($data) {
    foreach ($data as $row) {
        $totalNominal += $row['nominal']; // Tambahkan nominal ke total
        echo "<tr>
                <td>{$row['kk_name']}</td>
                <td style='text-align: center;'>" . number_format($row['nominal'], 0, ',', '.') . "</td>
                <td style='text-align: center;'>{$row['collector']}</td>
              </tr>";
    }
    // Tampilkan total nominal dan total scan di bawah tabel
    echo "<tr>
            <td colspan='2' style='text-align: right; font-weight: bold;'>Total Yang Disetorkan:</td>
            <td style='text-align: center; font-weight: bold;'>" . number_format($totalNominal, 0, ',', '.') . "</td>
          </tr>";
    echo "<tr>
            <td colspan='2' style='text-align: right; font-weight: bold;'>Total Scan:</td>
            <td style='text-align: center; font-weight: bold;'>{$totalScan}</td>
          </tr>";
} else {
    echo "<tr><td colspan='3' style='text-align: center;'>Tidak ada data jimpitan hari ini.</td></tr>";
}
