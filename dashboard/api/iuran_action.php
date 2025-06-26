<?php
// iuran_action.php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nokk = $_POST['nokk'];
  $jenis = $_POST['jenis_iuran'];
  $bulan = $_POST['bulan'];
  $tahun = $_POST['tahun'];
  $jumlah = $_POST['jumlah'];

  $stmt = $pdo->prepare("REPLACE INTO tb_iuran (nokk, jenis_iuran, bulan, tahun, jumlah, status, tgl_bayar) 
                         VALUES (?, ?, ?, ?, ?, 'Lunas', NOW())");
  $stmt->execute([$nokk, $jenis, $bulan, $tahun, $jumlah]);

  echo json_encode(['success' => true]);
}
