<?php
// warga_action.php
include 'db.php';

$aksi = $_POST['aksi'] ?? '';

if ($aksi == 'read') {
  echo '
  <table id="example" class="stripe hover w-full text-sm">
    <thead>
      <tr>
        <th class="border px-4 py-2">No</th>
        <th class="border px-4 py-2">Nama</th>
        <th class="border px-4 py-2">NIK</th>
        <th class="border px-4 py-2">Jenis Kelamin</th>
        <th class="border px-4 py-2">TTL</th>
        <th class="border px-4 py-2">Alamat</th>
        <th class="border px-4 py-2">Pekerjaan</th>
        <th class="border px-4 py-2">HP</th>
        <th class="border px-4 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
  ';

  $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC");
  $no = 1;
  while ($row = $stmt->fetch()) {
    echo "<tr>
      <td class='border px-4 py-2'>" . $no++ . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['nama']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['nik']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['jenkel']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['tpt_lahir']) . ", " . htmlspecialchars($row['tgl_lahir']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['alamat']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['pekerjaan']) . "</td>
      <td class='border px-4 py-2'>" . htmlspecialchars($row['hp']) . "</td>
      <td class='border px-4 py-2 text-center'>
        <button onclick=\"editData(" . $row['id_warga'] . ")\" class='text-blue-600 hover:text-blue-400 font-bold py-1 px-1'><i class='bx bx-edit'></i></button>
        <button onclick=\"hapusData(" . $row['id_warga'] . ")\" class='text-red-600 hover:text-red-400 font-bold py-1 px-1'><i class='bx bx-trash'></i></button>
      </td>
    </tr>";
  }

  echo '</tbody></table>';

} elseif ($aksi == 'save') {
  $data = $_POST;
  unset($data['aksi']);

  if (!empty($data['id_warga'])) {
    // UPDATE
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
    // INSERT
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
