<?php
require 'db.php';
$aksi = $_POST['aksi'] ?? '';

if ($aksi == 'read') {
  $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

elseif ($aksi == 'create' || $aksi == 'update') {
  $id = $_POST['id_warga'] ?? '';
  $data = [
    'kode' => $_POST['kode'],
    'nama' => $_POST['nama'],
    'nik' => $_POST['nik'],
    'hubungan' => $_POST['hubungan'],
    'nikk' => $_POST['nikk'],
    'jenkel' => $_POST['jenkel'],
    'tpt_lahir' => $_POST['tpt_lahir'],
    'tgl_lahir' => $_POST['tgl_lahir'],
    'alamat' => $_POST['alamat'],
    'rt' => $_POST['rt'],
    'rw' => $_POST['rw'],
    'kelurahan' => $_POST['kelurahan'],
    'kecamatan' => $_POST['kecamatan'],
    'kota' => $_POST['kota'],
    'propinsi' => $_POST['propinsi'],
    'negara' => $_POST['negara'],
    'agama' => $_POST['agama'],
    'status' => $_POST['status'],
    'pekerjaan' => $_POST['pekerjaan'],
    'hp' => $_POST['hp'],
  ];

  if (!empty($_FILES['foto']['name'])) {
    $foto = 'uploads/' . time() . '_' . basename($_FILES['foto']['name']);
    move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    $data['foto'] = $foto;
  }

  if ($aksi == 'create') {
    $cols = implode(',', array_keys($data));
    $vals = ':' . implode(', :', array_keys($data));
    $stmt = $pdo->prepare("INSERT INTO tb_warga ($cols) VALUES ($vals)");
  } else {
    $set = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($data)));
    $data['id_warga'] = $id;
    $stmt = $pdo->prepare("UPDATE tb_warga SET $set WHERE id_warga = :id_warga");
  }

  echo json_encode(['status' => $stmt->execute($data)]);
}

elseif ($aksi == 'get') {
  $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
  $stmt->execute([$_POST['id']]);
  echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

elseif ($aksi == 'delete') {
  $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
  echo json_encode(['status' => $stmt->execute([$_POST['id']])]);
}
