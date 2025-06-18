<?php
session_start();
include 'api/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Warga</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto py-6 px-4">
  <h1 class="text-2xl font-bold mb-4">Data Warga</h1>
  <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Tambah Warga</button>

  <table class="table-auto w-full bg-white rounded shadow">
    <thead>
      <tr class="bg-gray-200 text-left">
        <th class="px-4 py-2">Kode</th>
        <th class="px-4 py-2">Nama</th>
        <th class="px-4 py-2">NIK</th>
        <th class="px-4 py-2">Hubungan</th>
        <th class="px-4 py-2">Kelurahan</th>
        <th class="px-4 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $pdo->query("SELECT * FROM tb_warga ORDER BY tgl_warga DESC");
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo "<tr class='border-t'>";
          echo "<td class='px-4 py-2'>{$row['kode']}</td>";
          echo "<td class='px-4 py-2'>{$row['nama']}</td>";
          echo "<td class='px-4 py-2'>{$row['nik']}</td>";
          echo "<td class='px-4 py-2'>{$row['hubungan']}</td>";
          echo "<td class='px-4 py-2'>{$row['kelurahan']}</td>";
          echo "<td class='px-4 py-2 space-x-2'>
                  <button onclick='editWarga(".json_encode($row).")' class='bg-yellow-400 text-white px-2 py-1 rounded'>Edit</button>
                  <a href='warga_action.php?hapus={$row['id_warga']}' onclick='return confirm(\"Yakin hapus?\")' class='bg-red-500 text-white px-2 py-1 rounded'>Hapus</a>
                </td>";
          echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white p-6 rounded w-full max-w-2xl relative">
    <h2 class="text-xl font-bold mb-4" id="modalTitle">Tambah Warga</h2>
    <form method="post" action="api/warga_action.php" enctype="multipart/form-data">
      <input type="hidden" name="id_warga" id="id_warga">
      <input type="hidden" name="kode" id="kode" value="<?= uniqid() ?>">

      <div class="grid grid-cols-2 gap-4">
        <input type="text" name="nama" id="nama" placeholder="Nama" class="border px-3 py-2 rounded" required>
        <input type="text" name="nik" id="nik" placeholder="NIK" class="border px-3 py-2 rounded" required>
        <input type="text" name="hubungan" id="hubungan" placeholder="Hubungan" class="border px-3 py-2 rounded">
        <input type="text" name="nikk" id="nikk" placeholder="No KK" class="border px-3 py-2 rounded">
        <input type="text" name="jenkel" id="jenkel" placeholder="Jenis Kelamin" class="border px-3 py-2 rounded">
        <input type="text" name="tpt_lahir" id="tpt_lahir" placeholder="Tempat Lahir" class="border px-3 py-2 rounded">
        <input type="date" name="tgl_lahir" id="tgl_lahir" class="border px-3 py-2 rounded">
        <input type="text" name="alamat" id="alamat" placeholder="Alamat" class="border px-3 py-2 rounded">
        <input type="text" name="rt" id="rt" placeholder="RT" class="border px-3 py-2 rounded">
        <input type="text" name="rw" id="rw" placeholder="RW" class="border px-3 py-2 rounded">
        <input type="text" name="kelurahan" id="kelurahan" placeholder="Kelurahan" class="border px-3 py-2 rounded">
        <input type="text" name="kecamatan" id="kecamatan" placeholder="Kecamatan" class="border px-3 py-2 rounded">
        <input type="text" name="kota" id="kota" placeholder="Kota" class="border px-3 py-2 rounded">
        <input type="text" name="propinsi" id="propinsi" placeholder="Provinsi" class="border px-3 py-2 rounded">
        <input type="text" name="negara" id="negara" placeholder="Negara" class="border px-3 py-2 rounded">
        <input type="text" name="agama" id="agama" placeholder="Agama" class="border px-3 py-2 rounded">
        <input type="text" name="status" id="status" placeholder="Status" class="border px-3 py-2 rounded">
        <input type="text" name="pekerjaan" id="pekerjaan" placeholder="Pekerjaan" class="border px-3 py-2 rounded">
        <input type="file" name="foto" id="foto" class="border px-3 py-2 rounded">
      </div>
      <div class="mt-4 flex justify-end space-x-2">
        <button type="submit" name="simpan" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
        <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal() {
  document.getElementById('modal').classList.remove('hidden');
  document.getElementById('modalTitle').innerText = 'Tambah Warga';
  document.querySelector('form').reset();
  document.getElementById('id_warga').value = '';
}

function closeModal() {
  document.getElementById('modal').classList.add('hidden');
}

function editWarga(data) {
  openModal();
  document.getElementById('modalTitle').innerText = 'Edit Warga';
  for (const key in data) {
    if (document.getElementById(key)) {
      document.getElementById(key).value = data[key];
    }
  }
}
</script>
</body>
</html>
