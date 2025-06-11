<?php
require 'db.php'; // koneksi PDO

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="data_warga.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['NIK', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'RT', 'RW', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', 'Negara', 'Agama', 'Status', 'Pekerjaan', 'HP']);

$query = $pdo->query("SELECT * FROM tb_warga");
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['nik'],
        $row['nama'],
        $row['jenkel'],
        $row['tpt_lahir'],
        $row['tgl_lahir'],
        $row['alamat'],
        $row['rt'],
        $row['rw'],
        $row['kelurahan'],
        $row['kecamatan'],
        $row['kota'],
        $row['propinsi'],
        $row['negara'],
        $row['agama'],
        $row['status'],
        $row['pekerjaan'],
        $row['hp']
    ]);
}
fclose($output);
exit;
