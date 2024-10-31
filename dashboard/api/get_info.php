<?php
// Query untuk menghitung jumlah data
$sqlkk = "SELECT COUNT(*) AS total_rows FROM master_kk";
$resultkk = $pdo->query($sqlkk);

// Mengambil jumlah data
if ($resultkk->num_rows > 0) {
    $rowkk = $resultkk->fetch_assoc();
    echo json_encode(["total" => $rowkk["total_rows"]]);
} else {
    echo json_encode(["total" => 0]);
}
?>
