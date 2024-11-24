<?php
include '../helper/connection.php';

try {
    // Query untuk total saldo
    $sqlSaldo = "SELECT COALESCE(SUM(nominal), 0) AS total_scan FROM report" WHERE date(jimpitan_date) = 2024-11-23;
    $stmtSaldo = $pdo->query($sqlSaldo);
    $totalSaldo = $stmtSaldo->fetch(PDO::FETCH_ASSOC)["total_scan"];
    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>