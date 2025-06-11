<?php
require 'db.php'; // koneksi PDO

// === HANDLE SUBMIT FORM ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $data = [
        'kode' => $_POST['kode'] ?? uniqid('W'),
        'nama' => $_POST['nama'],
        'nik' => $_POST['nik'],
        'nikk' => $_POST['nokk'],
        'jenkel' => $_POST['jenkel'],
        'tpt_lahir' => $_POST['tpt_lahir'],
        'tgl_lahir' => $_POST['tgl_lahir'],
        'alamat' => $_POST['alamat'],
        'rt' => $_POST['rt'],
        'rw' => $_POST['rw'],
        'kelurahan' => $_POST['kelurahan'],
        'kecamatan' => $_POST['kecamatan'],
        'kota' => $_POST['kota'],
        'propinsi' => $_POST['provinsi'],
        'negara' => 'Indonesia',
        'agama' => $_POST['agama'],
        'status' => $_POST['status'],
        'pekerjaan' => $_POST['pekerjaan'],
        'hp' => $_POST['hp'],
        'hubungan' => $_POST['hubungan'],
        'tgl_warga' => date('Y-m-d')
    ];

    if (!empty($_FILES['foto']['name'])) {
        $foto = 'uploads/' . time() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
        $data['foto'] = $foto;
    }

    if ($id) {
        $set = '';
        foreach ($data as $key => $val) {
            $set .= "$key = :$key,";
        }
        $set = rtrim($set, ',');
        $data['id'] = $id;
        $stmt = $pdo->prepare("UPDATE tb_warga SET $set WHERE id_warga = :id");
        $stmt->execute($data);
    } else {
        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $pdo->prepare("INSERT INTO tb_warga ($fields) VALUES ($placeholders)");
        $stmt->execute($data);
    }

    header('Location: modal_warga.php');
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tb_warga WHERE id_warga = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: modal_warga.php');
    exit;
}

$data = $pdo->query("SELECT * FROM tb_warga ORDER BY id_warga DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Warga</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

  <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Tambah Warga</button>

  <!-- Modal Tambah/Edit Warga -->
  <div id="modalWarga" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-screen overflow-y-auto">
      <div class="flex justify-between items-center px-6 py-4 border-b">
        <h2 class="text-xl font-semibold" id="modalTitle">Tambah Warga</h2>
        <button onclick="closeModal()" class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
      </div>

      <form id="formWarga" method="POST" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="kode" id="kode">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">NIK</label><input type="text" name="nik" id="nik" maxlength="16" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">No KK</label><input type="text" name="nokk" id="nokk" maxlength="16" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Nama Lengkap</label><input type="text" name="nama" id="nama" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Jenis Kelamin</label><select name="jenkel" id="jenkel" required class="w-full input border border-gray-300 rounded-md"><option value="">Pilih</option><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
          <div><label class="block text-sm font-medium">Tempat Lahir</label><input type="text" name="tpt_lahir" id="tpt_lahir" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Tanggal Lahir</label><input type="date" name="tgl_lahir" id="tgl_lahir" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Agama</label><select name="agama" id="agama" required class="w-full input border border-gray-300 rounded-md"><option value="">Pilih</option><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select></div>
          <div><label class="block text-sm font-medium">Status Perkawinan</label><select name="status" id="status" required class="w-full input border border-gray-300 rounded-md"><option value="">Pilih</option><option>Belum Kawin</option><option>Kawin</option><option>Cerai Hidup</option><option>Cerai Mati</option></select></div>
          <div><label class="block text-sm font-medium">Pekerjaan</label><input type="text" name="pekerjaan" id="pekerjaan" class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">No HP</label><input type="text" name="hp" id="hp" class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Hubungan</label><select name="hubungan" id="hubungan" required class="w-full input border border-gray-300 rounded-md"><option value="">Pilih</option><option>Suami</option><option>Istri</option><option>Anak</option><option>Keluarga Lain</option></select></div>
          <div><label class="block text-sm font-medium">Upload Foto</label><input type="file" name="foto" id="foto" accept="image/*" class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Alamat (Jalan/Gang)</label><input type="text" name="alamat" id="alamat" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">RT/RW</label><div class="flex gap-2"><input type="text" name="rt" id="rt" maxlength="3" placeholder="RT" class="input w-1/2 border border-gray-300 rounded-md"><input type="text" name="rw" id="rw" maxlength="3" placeholder="RW" class="input w-1/2 border border-gray-300 rounded-md"></div></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">Provinsi</label><input type="text" name="provinsi" id="provinsi" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Kabupaten/Kota</label><input type="text" name="kota" id="kota" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Kecamatan</label><input type="text" name="kecamatan" id="kecamatan" required class="w-full input border border-gray-300 rounded-md"></div>
          <div><label class="block text-sm font-medium">Kelurahan</label><input type="text" name="kelurahan" id="kelurahan" required class="w-full input border border-gray-300 rounded-md"></div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="button" onclick="closeModal()" class="btn-secondary mr-2">Batal</button>
          <button type="submit" class="btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <table class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden">
    <thead class="bg-gray-200">
      <tr>
        <th class="px-3 py-2">Kode</th>
        <th class="px-3 py-2">Nama</th>
        <th class="px-3 py-2">NIK</th>
        <th class="px-3 py-2">NIKK</th>
        <th class="px-3 py-2">Jenkel</th>
        <th class="px-3 py-2">TTL</th>
        <th class="px-3 py-2">RT</th>
        <th class="px-3 py-2">RW</th>
        <th class="px-3 py-2">No.HP</th>
        <th class="px-3 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data as $row): ?>
      <tr class="border-b hover:bg-gray-100">
        <td class="px-3 py-1"><?= htmlspecialchars($row['kode']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['nama']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['nik']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['nikk']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['jenkel']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['tpt_lahir']) ?>, <?= date('d M Y', strtotime($row['tgl_lahir'])) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['rt']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['rw']) ?></td>
        <td class="px-3 py-1"><?= htmlspecialchars($row['hp']) ?></td>
        <td class="px-3 py-1">
          <button onclick='editWarga(<?= json_encode($row) ?>)' class="text-blue-600 hover:text-blue-400"><i class='bx bx-edit'></i></button>
          <a href="?delete=<?= $row['id_warga'] ?>" onclick="return confirm('Hapus <?= $row['nama'] ?>?')" class="text-red-600 hover:text-red-400"><i class='bx bx-trash'></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<script>
function openModal() {
  document.getElementById('modalWarga').classList.remove('hidden');
  document.getElementById('formWarga').reset();
  document.getElementById('modalTitle').innerText = 'Tambah Warga';
  document.getElementById('id').value = '';
  document.getElementById('kode').value = '';
}

function closeModal() {
  document.getElementById('modalWarga').classList.add('hidden');
}

function editWarga(data) {
  openModal();
  document.getElementById('modalTitle').innerText = 'Edit Warga';
  for (const key in data) {
    if (document.getElementById(key)) {
      document.getElementById(key).value = data[key];
    }
  }
  document.getElementById('id').value = data.id_warga;
  document.getElementById('kode').value = data.kode;
}
</script>

</body>
</html>
