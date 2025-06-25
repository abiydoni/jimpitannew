<?php
include 'db.php';

try {

    
    // Query untuk total KK
    $sqlKKj = "SELECT COUNT(*) AS total_kkj FROM master_kk";
    $stmtKKj = $pdo->query($sqlKKj);
    $totalKKj = $stmtKKj->fetch(PDO::FETCH_ASSOC)["total_kkj"];

    $sqlKK = "SELECT COUNT(DISTINCT nikk) AS jumlah_kk FROM tb_warga WHERE nikk IS NOT NULL AND nikk != ''";
    $stmtKK = $pdo->query($sqlKK);
    $totalKK = $stmtKK->fetch(PDO::FETCH_ASSOC)["jumlah_kk"];

    $sqlWarga = "SELECT COUNT(*) AS total_warga FROM tb_warga";
    $stmtWarga = $pdo->query($sqlWarga);
    $totalWarga = $stmtWarga->fetch(PDO::FETCH_ASSOC)["total_warga"];

    // Query untuk total saldo
    $sqlSaldo = "SELECT COALESCE((SUM(debet)-SUM(kredit)), 0) AS total_saldo FROM kas_umum";
    $stmtSaldo = $pdo->query($sqlSaldo);
    $totalSaldo = $stmtSaldo->fetch(PDO::FETCH_ASSOC)["total_saldo"];

    // Query untuk total saldo
    $sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
    $stmtUsers = $pdo->query($sqlUsers);
    $totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)["total_users"];


    //echo json_encode([
    //    "total_kk" => $totalKK,
    //    "total_saldo" => $totalSaldo,
    //    "total_users" => $totalUsers
    //]);
    
} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

?>