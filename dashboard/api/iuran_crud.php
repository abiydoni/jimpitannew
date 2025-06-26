<?php
// iuran_crud.php
include 'db.php';

$action = $_GET['action'] ?? '';

if ($action === 'read') {
  $stmt = $pdo->query("SELECT * FROM tb_iuran ORDER BY tgl_bayar DESC");
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);

} elseif ($action === 'delete') {
  $id = $_POST['id'] ?? 0;
  $stmt = $pdo->prepare("DELETE FROM tb_iuran WHERE id_iuran = ?");
  $stmt->execute([$id]);
  echo json_encode(['success' => true]);

} elseif ($action === 'get') {
  $id = $_GET['id'] ?? 0;
  $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE id_iuran = ? LIMIT 1");
  $stmt->execute([$id]);
  echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));

} elseif ($action === 'update') {
  $id = $_POST['id_iuran'];
  $stmt = $pdo->prepare("UPDATE tb_iuran SET nokk=?, jenis_iuran=?, bulan=?, tahun=?, jumlah=?, status=?, tgl_bayar=NOW(), keterangan=? WHERE id_iuran=?");
  $stmt->execute([
    $_POST['nokk'], $_POST['jenis_iuran'], $_POST['bulan'], $_POST['tahun'],
    $_POST['jumlah'], $_POST['status'], $_POST['keterangan'], $id
  ]);
  echo json_encode(['success' => true]);

} elseif ($action === 'create') {
  $stmt = $pdo->prepare("INSERT INTO tb_iuran (nokk, jenis_iuran, bulan, tahun, jumlah, status, tgl_bayar, keterangan) 
                         VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
  $stmt->execute([
    $_POST['nokk'], $_POST['jenis_iuran'], $_POST['bulan'], $_POST['tahun'],
    $_POST['jumlah'], $_POST['status'], $_POST['keterangan']
  ]);
  echo json_encode(['success' => true]);
}
?>
