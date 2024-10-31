<?php
// Query untuk menghitung jumlah data
$sql = "SELECT COUNT(*) AS total_rows FROM nama_tabel";
$result = $conn->query($sql);

// Mengambil jumlah data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["total" => $row["total_rows"]]);
} else {
    echo json_encode(["total" => 0]);
}
?>
