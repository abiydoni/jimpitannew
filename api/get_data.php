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
    WHERE report.jimpitan_date = CURDATE()
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tampilkan data sebagai HTML
if ($data) {
    foreach ($data as $row) {
        echo "<tr>
                <td>{$row['kk_name']}</td>
                <td style='text-align: center;'>{$row['nominal']}</td>
                <td style='text-align: center;'>{$row['collector']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3' style='text-align: center;'>Tidak ada data jimpitan hari ini.</td></tr>";
}
