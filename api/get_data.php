<?php
include 'db.php';

// SQL statement untuk mengambil data
$stmt = $pdo->prepare("SELECT master_kk.kk_name, report.* FROM report JOIN master_kk ON report.report_id = master_kk.code_id WHERE report.jimpitan_date = CURDATE()");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal
$total_scans = count($data);
$total_nominal = array_sum(array_column($data, 'nominal'));

// Kembalikan data dalam format JSON
echo json_encode([
    'data' => $data,
    'total_nominal' => $total_nominal,
]);
?>
