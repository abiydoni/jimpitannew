<?php
include 'db.php';
date_default_timezone_set('Asia/Jakarta');

// SQL statement untuk mengambil data
$stmt = $pdo->prepare("
SELECT m.code_id, m.kk_name
    FROM master_kk m
    LEFT JOIN report r 
        ON m.code_id = r.report_id 
        AND r.jimpitan_date = CURDATE()
    WHERE r.report_id IS NULL
    ORDER BY m.kk_name ASC
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Hitung total nominal

// Kembalikan data dalam format JSON
echo json_encode([
    'data' => $data,
]);
?>
