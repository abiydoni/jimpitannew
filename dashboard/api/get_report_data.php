<?php
header('Content-Type: application/json');

// Include database connection
include 'db.php';

// Prepare the SQL statement to select report data
$stmt = $pdo->prepare("SELECT master_kk.kk_name, report.* FROM report JOIN master_kk ON report.report_id = master_kk.code_id");
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the data as JSON
echo json_encode($data);
?>