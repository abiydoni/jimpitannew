<?php
// Query untuk menghitung jumlah data
$sql = "SELECT COUNT(*) AS total_rows FROM nama_tabel";
$result = $pdo->query($sql);

// Memeriksa hasil dan menyimpan ke variabel teks
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $jumlah_data = $row["total_rows"];
    $teks = "Jumlah data di tabel adalah: " . $jumlah_data;
    echo $teks;
} else {
    echo "Tabel kosong atau tidak ada data";
}
?>
