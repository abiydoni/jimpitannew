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
    $negara = $_POST['negara'] ?? 'Indonesia';
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
    } else {
        $foto = $_POST['foto_lama'] ?? '';
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
?>

<div id="modalWarga" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-screen overflow-y-auto">
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-xl font-semibold" id="modalTitle">Tambah Warga</h2>
      <button onclick="closeModal()" class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
    </div>

    <form id="formWarga" enctype="multipart/form-data" method="POST" class="px-4 py-2 space-y-3 text-xs">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="kode" id="kode">
      <input type="hidden" name="foto_lama" id="foto_lama">

      <!-- Grid Input (3 kolom besar, 1 kolom di HP) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
        <div><label for="nik" class="block mb-0.5 font-medium">NIK</label><input type="text" name="nik" id="nik" required pattern="\d{16}" title="Harus 16 digit angka" class="w-full h-8 border rounded px-2"></div>
        <div><label for="nokk" class="block mb-0.5 font-medium">No KK</label><input type="text" name="nokk" id="nokk" required pattern="\d{16}" title="Harus 16 digit angka" class="w-full h-8 border rounded px-2"></div>
        <div><label for="nama" class="block mb-0.5 font-medium">Nama</label><input type="text" name="nama" id="nama" required class="w-full h-8 border rounded px-2"></div>
        <div><label for="jenkel" class="block mb-0.5 font-medium">Jenis Kelamin</label>
          <select name="jenkel" id="jenkel" required class="w-full h-8 border rounded px-2">
            <option value="L">Laki-laki</option><option value="P">Perempuan</option>
          </select>
        </div>
        <div><label for="tpt_lahir" class="block mb-0.5 font-medium">Tempat Lahir</label><input type="text" name="tpt_lahir" id="tpt_lahir" required class="w-full h-8 border rounded px-2"></div>
        <div><label for="tgl_lahir" class="block mb-0.5 font-medium">Tanggal Lahir</label><input type="date" name="tgl_lahir" id="tgl_lahir" required class="w-full h-8 border rounded px-2"></div>
        <div><label for="agama" class="block mb-0.5 font-medium">Agama</label>
          <select name="agama" id="agama" required class="w-full h-8 border rounded px-2">
            <option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Budha</option><option>Konghucu</option>
          </select>
        </div>
        <div><label for="status" class="block mb-0.5 font-medium">Status</label>
          <select name="status" id="status" required class="w-full h-8 border rounded px-2">
            <option value="TK">Tidak Kawin</option><option value="K">Kawin</option><option value="CH">Cerai Hidup</option><option value="CM">Cerai Mati</option><option>Lainnya</option>
          </select>
        </div>
        <div><label for="pekerjaan" class="block mb-0.5 font-medium">Pekerjaan</label>
          <select name="pekerjaan" id="pekerjaan" required class="w-full h-8 border rounded px-2">
            <option>Tidak Bekerja</option><option>Pelajar/Mahasiswa</option><option>Pensiunan</option><option>Wiraswasta</option><option>Swasta</option><option>PNS</option><option>TNI</option><option>POLRI</option><option>BUMN/BUMD</option><option>Buruh</option><option>Honorer</option><option>Lainnya</option>
          </select>
        </div>
        <div><label for="rt" class="block mb-0.5 font-medium">RT</label><input type="text" name="rt" id="rt" class="w-full h-8 border rounded px-2"></div>
        <div><label for="rw" class="block mb-0.5 font-medium">RW</label><input type="text" name="rw" id="rw" class="w-full h-8 border rounded px-2"></div>
        <div><label for="hp" class="block mb-0.5 font-medium">No HP</label><input type="text" name="hp" id="hp" pattern="\d{10,}" title="Minimal 10 digit angka" class="w-full h-8 border rounded px-2"></div>
        <div><label for="hubungan" class="block mb-0.5 font-medium">Hubungan</label>
          <select name="hubungan" id="hubungan" required class="w-full h-8 border rounded px-2">
            <option>Kepala Keluarga</option><option>Suami</option><option>Istri</option><option>Anak</option><option>Menantu</option><option>Orang Tua</option><option>Mertua</option><option>Cucu</option><option>Famili Lain</option><option>Pembantu</option><option>Lainnya</option>
          </select>
        </div>
        <div><label for="foto" class="block mb-0.5 font-medium">Foto</label>
          <input type="file" name="foto" id="foto" accept="image/*" class="w-full h-8 border rounded px-2 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-xs file:bg-gray-100">
        </div>
        <div><label for="negara" class="block mb-0.5 font-medium">Negara</label><select name="negara" id="negara" class="w-full h-8 border rounded px-2"><option value="Indonesia" selected>Indonesia</option></select></div>
        <div><label for="provinsi" class="block mb-0.5 font-medium">Provinsi</label><select name="provinsi" id="provinsi" class="w-full h-8 border rounded px-2"></select></div>
        <div><label for="kota" class="block mb-0.5 font-medium">Kota/Kabupaten</label><select name="kota" id="kota" class="w-full h-8 border rounded px-2"></select></div>
        <div><label for="kecamatan" class="block mb-0.5 font-medium">Kecamatan</label><select name="kecamatan" id="kecamatan" class="w-full h-8 border rounded px-2"></select></div>
        <div><label for="kelurahan" class="block mb-0.5 font-medium">Kelurahan</label><select name="kelurahan" id="kelurahan" class="w-full h-8 border rounded px-2"></select></div>
      </div>

      <!-- Alamat (1 kolom penuh) -->
      <div>
        <label for="alamat" class="block mb-0.5 font-medium">Alamat</label>
        <input type="text" name="alamat" id="alamat" class="w-full h-8 border rounded px-2">
      </div>

      <!-- Tombol -->
      <div class="flex justify-end pt-2 border-t mt-4">
        <button type="button" onclick="closeModal()" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-xs mr-2">Batal</button>
        <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs">Simpan</button>
      </div>
    </form>

  </div>
</div>

<script>
function bukaModalWarga() {
  document.getElementById('modalWarga').classList.remove('hidden');
  document.getElementById('formWarga').reset();
}

function closeModal() {
  document.getElementById('modalWarga').classList.add('hidden');
}
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const provinsiEl = document.getElementById('provinsi');
  const kotaEl = document.getElementById('kota');
  const kecamatanEl = document.getElementById('kecamatan');
  const kelurahanEl = document.getElementById('kelurahan');

  // Load provinsi saat modal dibuka
  async function loadProvinsi() {
    try {
      const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
      const data = await res.json();
      provinsiEl.innerHTML = '<option value="">Pilih Provinsi</option>' +
        data.map(p => `<option value="${p.id}">${p.name}</option>`).join('');

      kotaEl.innerHTML = '<option value="">Pilih Kota</option>';
      kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
      kelurahanEl.innerHTML = '<option value="">Pilih Kelurahan</option>';

    } catch (e) {
      provinsiEl.innerHTML = '<option>Gagal memuat provinsi</option>';
      console.error(e);
    }
  }

  provinsiEl.addEventListener('change', async () => {
    const id = provinsiEl.value;
    try {
      const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`);
      const data = await res.json();
      kotaEl.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>' +
        data.map(k => `<option value="${k.id}">${k.name}</option>`).join('');

      kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>';
      kelurahanEl.innerHTML = '<option value="">Pilih Kelurahan</option>';

    } catch (e) {
      kotaEl.innerHTML = '<option>Gagal memuat kota</option>';
      console.error(e);
    }
  });

  kotaEl.addEventListener('change', async () => {
    const id = kotaEl.value;
    try {
      const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`);
      const data = await res.json();
      kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>' +
        data.map(k => `<option value="${k.id}">${k.name}</option>`).join('');

      kelurahanEl.innerHTML = '<option value="">Pilih Kelurahan</option>';
      
    } catch (e) {
      kecamatanEl.innerHTML = '<option>Gagal memuat kecamatan</option>';
      console.error(e);
    }
  });

  kecamatanEl.addEventListener('change', async () => {
    const id = kecamatanEl.value;
    try {
      const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`);
      const data = await res.json();
      kelurahanEl.innerHTML = '<option value="">Pilih Kelurahan</option>' +
        data.map(k => `<option value="${k.name}">${k.name}</option>`).join('');
    } catch (e) {
      kelurahanEl.innerHTML = '<option>Gagal memuat kelurahan</option>';
      console.error(e);
    }
  });

  // Trigger load saat modal dibuka
  window.bukaModalWarga = function() {
    document.getElementById('formWarga').reset();
    loadProvinsi();
    document.getElementById('modalWarga').classList.remove('hidden');
  }

  window.closeModal = function() {
    document.getElementById('modalWarga').classList.add('hidden');
  }
});
</script>
