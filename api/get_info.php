<?php
include 'db.php';

try {
    // Query untuk total Scan
    $sqlScan = "SELECT COALESCE(SUM(nominal), 0) AS total_scan FROM report WHERE jimpitan_date = '2024-11-23'";
    $stmtScan = $pdo->query($sqlScan);
    $totalScan = $stmtScan->fetch(PDO::FETCH_ASSOC)["total_scan"];
    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>