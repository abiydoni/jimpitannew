<?php
require 'db.php'; // koneksi PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Lewati baris pertama (header)
        fgetcsv($handle);
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $stmt = $pdo->prepare("INSERT INTO tb_warga (nik, nama, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw, kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, hp, kode, hubungan, nikk, foto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', '', '', '', '')");
            $stmt->execute($data);
        }
        fclose($handle);
        echo "Import berhasil!";
    } else {
        echo "Gagal membuka file.";
    }
} else {
    echo "Upload file CSV untuk mengimpor data.";
}
