<?php
// modal_warga.php
include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $kode = $_POST['kode'] ?? uniqid();
    $nik = $_POST['nik'];
    $nokk = $_POST['nokk'];
    $nama = $_POST['nama'];
    $jenkel = $_POST['jenkel'];
    $tpt_lahir = $_POST['tpt_lahir'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $alamat = $_POST['alamat'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];
    $kelurahan = $_POST['kelurahan'] ?? '';
    $kecamatan = $_POST['kecamatan'] ?? '';
    $kota = $_POST['kota'] ?? '';
    $provinsi = $_POST['provinsi'] ?? '';
    $negara = 'Indonesia';
    $agama = $_POST['agama'];
    $status = $_POST['status'];
    $pekerjaan = $_POST['pekerjaan'];
    $hp = $_POST['hp'];
    $hubungan = $_POST['hubungan'];

    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $targetDir = 'uploads/';
        $foto = $targetDir . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    if ($id) {
        $sql = "UPDATE tb_warga SET nik=?, nikk=?, nama=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?, kelurahan=?, kecamatan=?, kota=?, propinsi=?, negara=?, agama=?, status=?, pekerjaan=?, hp=?, hubungan=?" .
            ($foto ? ", foto=?" : '') .
            " WHERE kode=?";
        $params = [$nik, $nokk, $nama, $jenkel, $tpt_lahir, $tgl_lahir, $alamat, $rt, $rw, $kelurahan, $kecamatan, $kota, $provinsi, $negara, $agama, $status, $pekerjaan, $hp, $hubungan];
        if ($foto) $params[] = $foto;
        $params[] = $kode;
    } else {
        $sql = "INSERT INTO tb_warga (kode, nik, nikk, nama, jenkel, tpt_lahir, tgl_lahir, alamat, rt, rw, kelurahan, kecamatan, kota, propinsi, negara, agama, status, pekerjaan, hp, hubungan, foto, tgl_warga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $params = [$kode, $nik, $nokk, $nama, $jenkel, $tpt_lahir, $tgl_lahir, $alamat, $rt, $rw, $kelurahan, $kecamatan, $kota, $provinsi, $negara, $agama, $status, $pekerjaan, $hp, $hubungan, $foto];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    header("Location: warga.php");
    exit;
}

// Modal HTML
?>
<div id="modalWarga" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-screen overflow-y-auto">
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-xl font-semibold" id="modalTitle">Tambah Warga</h2>
      <button onclick="closeModal()" class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
    </div>

    <form id="formWarga" enctype="multipart/form-data" method="POST" class="px-6 py-4 space-y-4">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="kode" id="kode">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">NIK</label>
          <input type="text" name="nik" id="nik" maxlength="16" required class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">No KK</label>
          <input type="text" name="nokk" id="nokk" maxlength="16" required class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Nama Lengkap</label>
          <input type="text" name="nama" id="nama" required class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Jenis Kelamin</label>
          <select name="jenkel" id="jenkel" required class="w-full input border border-gray-300 rounded-md">
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Tempat Lahir</label>
          <input type="text" name="tpt_lahir" id="tpt_lahir" required class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Tanggal Lahir</label>
          <input type="date" name="tgl_lahir" id="tgl_lahir" required class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Agama</label>
          <select name="agama" id="agama" required class="w-full input border border-gray-300 rounded-md">
            <option value="">Pilih Agama</option>
            <option>Islam</option>
            <option>Kristen</option>
            <option>Katolik</option>
            <option>Hindu</option>
            <option>Buddha</option>
            <option>Konghucu</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Status Perkawinan</label>
          <select name="status" id="status" required class="w-full input border border-gray-300 rounded-md">
            <option value="">Pilih Status</option>
            <option>Belum Kawin</option>
            <option>Kawin</option>
            <option>Cerai Hidup</option>
            <option>Cerai Mati</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Pekerjaan</label>
          <input type="text" name="pekerjaan" id="pekerjaan" class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">No HP</label>
          <input type="text" name="hp" id="hp" class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Hubungan</label>
          <select name="hubungan" id="hubungan" required class="w-full input border border-gray-300 rounded-md">
            <option value="">Pilih</option>
            <option>Suami</option>
            <option>Istri</option>
            <option>Anak</option>
            <option>Keluarga Lain</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Upload Foto</label>
          <input type="file" name="foto" id="foto" accept="image/*" class="w-full input border border-gray-300 rounded-md">
          <img id="previewFoto" class="mt-2 max-h-32" />
        </div>
        <div>
          <label class="block text-sm font-medium">Alamat (Jalan/Gang)</label>
          <input type="text" name="alamat" id="alamat" required class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">RT/RW</label>
          <div class="flex gap-2">
            <input type="text" name="rt" id="rt" maxlength="3" placeholder="RT" class="input w-1/2 border border-gray-300 rounded-md">
            <input type="text" name="rw" id="rw" maxlength="3" placeholder="RW" class="input w-1/2 border border-gray-300 rounded-md">
          </div>
        </div>
      </div>

      <!-- Dropdown Wilayah -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium">Provinsi</label>
          <input type="text" name="provinsi" id="provinsi" class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Kabupaten/Kota</label>
          <input type="text" name="kota" id="kota" class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Kecamatan</label>
          <input type="text" name="kecamatan" id="kecamatan" class="w-full input border border-gray-300 rounded-md">
        </div>
        <div>
          <label class="block text-sm font-medium">Kelurahan</label>
          <input type="text" name="kelurahan" id="kelurahan" class="w-full input border border-gray-300 rounded-md">
        </div>
      </div>

      <div class="flex justify-end pt-4 border-t">
        <button type="button" onclick="closeModal()" class="btn-secondary mr-2">Batal</button>
        <button type="submit" class="btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function bukaModalWarga() {
  document.getElementById('modalWarga').classList.remove('hidden');
  document.getElementById('formWarga').reset();
  document.getElementById('modalTitle').innerText = 'Tambah Warga';
  document.getElementById('id').value = '';
}

function closeModal() {
  document.getElementById('modalWarga').classList.add('hidden');
}

function editWarga(kode) {
  fetch('api/get_warga.php?kode=' + kode)
    .then(res => res.json())
    .then(data => {
      for (let key in data) {
        if (document.getElementById(key)) document.getElementById(key).value = data[key];
      }
      document.getElementById('id').value = '1';
      document.getElementById('modalTitle').innerText = 'Edit Warga';
      document.getElementById('modalWarga').classList.remove('hidden');
    });
}
</script>
