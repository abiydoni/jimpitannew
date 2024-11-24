<?php
include 'db.php';

try {
    // Query untuk total Scan
    $sqlScan = "SELECT COALESCE(SUM(nominal), 0) AS total_scan FROM report WHERE jimpitan_date = '2014-11-23'";
    $stmtScan = $pdo->query($sqlScan);
    $totalScan = $stmtScan->fetch(PDO::FETCH_ASSOC)["total_scan"];
    
    // Query untuk total Scan
    $sqlData = "SELECT COALESCE(count(nominal), 0) AS total_data FROM report WHERE jimpitan_date = '2014-11-23'";
    $stmtData = $pdo->query($sqlData);
    $totalData = $stmtData->fetch(PDO::FETCH_ASSOC)["total_data"];
    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>