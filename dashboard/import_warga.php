<?php
require 'db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['excel_file']['tmp_name'])) {
  $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);
  $sheet = $spreadsheet->getActiveSheet()->toArray();

  $pdo->beginTransaction();
  for ($i = 1; $i < count($sheet); $i++) {
    $row = $sheet[$i];
    $stmt = $pdo->prepare("INSERT INTO tb_warga (kode, nama, nik, hp, alamat, kota, propinsi, negara, agama, status, pekerjaan, jenkel, tpt_lahir, tgl_lahir, rt, rw, kelurahan, kecamatan, nikk, hubungan, foto)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '')");
    $stmt->execute([
      uniqid('W'), $row[0], $row[1], $row[2], $row[3],
      $row[4], $row[5], 'Indonesia', 'Islam', 'Kawin', 'Petani', 'L', 'Jakarta', date('Y-m-d'),
      '001', '001', 'Kelurahan', 'Kecamatan', $row[1], 'Suami'
    ]);
  }
  $pdo->commit();
  echo "Import berhasil!";
}
