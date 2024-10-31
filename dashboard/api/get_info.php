<?php
// Query untuk menghitung jumlah data
$sql = "SELECT COUNT(*) AS total_rows FROM master_kk";
$result = $conn->query($sql);

// Mengecek dan menampilkan jumlah data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Jumlah data: " . $row["total_rows"];
}
?>
