<?php
// warga_action.php
include 'db.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi == 'read') {
  $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
  $no = 1;
  while ($row = $stmt->fetch()) {
    echo "<tr>
      <td class='border px-4 py-2'>" . $no++ . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['nama']) . "</td>
      <td class='border px-4 py-2'>" . $row['nik'] . "</td>
      <td class='border px-4 py-2'>" . $row['jenkel'] . "</td>
      <td class='border px-4 py-2'>" . $row['tpt_lahir'] . ", " . $row['tgl_lahir'] . "</td>
      <td class='border px-4 py-2'>" . $row['alamat'] . "</td>
      <td class='border px-4 py-2'>" . $row['pekerjaan'] . "</td>
      <td class='border px-4 py-2'>" . $row['hp'] . "</td>
      <td class='border px-4 py-2'>
        <button onclick=\"editData(" . $row['id_warga'] . ")\" class='text-blue-600 hover:text-blue-400 font-bold py-1 px-1'><i class='bx bx-edit'></i></button>
        <button onclick=\"hapusData(" . $row['id_warga'] . ")\" class='text-red-600 hover:text-red-400 font-bold py-1 px-1'><i class='bx bx-trash'></i></button>
      </td>
    </tr>";
  }
} elseif ($aksi == 'save') {
  $data = $_POST;
  unset($data['aksi']);

  if (!empty($data['id_warga'])) {
    $id = $data['id_warga'];
    unset($data['id_warga']);
    $sql = "UPDATE tb_warga SET ";
    $params = [];
    foreach ($data as $key => $val) {
      $sql .= "$key = :$key, ";
      $params[":$key"] = $val;
    }
    $sql = rtrim($sql, ', ') . " WHERE id_warga = :id";
    $params[':id'] = $id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
  } else {
    unset($data['id_warga']);
    $cols = implode(", ", array_keys($data));
    $place = ":" . implode(", :", array_keys($data));
    $stmt = $pdo->prepare("INSERT INTO tb_warga ($cols) VALUES ($place)");
    foreach ($data as $key => $val) {
      $stmt->bindValue(":$key", $val);
    }
    $stmt->execute();
  }
} elseif ($aksi == 'get') {
  $id = $_POST['id'];
  $stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
  $stmt->execute([$id]);
  echo json_encode($stmt->fetch());
} elseif ($aksi == 'delete') {
  $id = $_POST['id'];
  $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
  $stmt->execute([$id]);
}
