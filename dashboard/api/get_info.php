<?php
include 'db.php';

// Query untuk menghitung jumlah data
$sql = "SELECT COUNT(*) AS total_rows FROM master_kk";
$result = $pdo->query($sql);

// Mengambil jumlah data dan mengirimkan dalam format JSON
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["total" => $row["total_rows"]]);
} else {
    echo json_encode(["total" => 0]);
}

// Menutup koneksi
$conn->close();

?>
