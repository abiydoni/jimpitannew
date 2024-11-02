<?php
include 'db.php';

try {
    // Query untuk total KK
    $sqlKK = "SELECT COUNT(*) AS total_kk FROM master_kk";
    $stmtKK = $pdo->query($sqlKK);
    $totalKK = $stmtKK->fetch(PDO::FETCH_ASSOC)["total_kk"];

    // Query untuk total saldo
    $sqlSaldo = "SELECT COALESCE((SUM(debet)-SUM(kredit)), 0) AS total_saldo FROM kas_umum";
    $stmtSaldo = $pdo->query($sqlSaldo);
    $totalSaldo = $stmtSaldo->fetch(PDO::FETCH_ASSOC)["total_saldo"];

    // Query untuk total saldo
    $sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
    $stmtUsers = $pdo->query($sqlUsers);
    $totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)["total_users"];


    echo json_encode([
        "total_kk" => $totalKK,
        "total_saldo" => $totalSaldo,
        "total_users" => $totalUsers
    ]);
    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>