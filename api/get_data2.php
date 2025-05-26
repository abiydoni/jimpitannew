<?php
include 'db.php';
date_default_timezone_set('Asia/Jakarta');

$jimpitan_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = ?
    ORDER BY report.scan_time DESC
");
$stmt->execute([$jimpitan_date]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal
$total_scans = count($data);
$total_nominal = array_sum(array_column($data, 'nominal'));

// Kembalikan data dalam format JSON
echo json_encode([
    'data' => $data,
    'total_scan' => $total_scans,
    'total_nominal' => $total_nominal,
]);
?>
