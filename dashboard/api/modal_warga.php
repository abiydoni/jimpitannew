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

    <form id="formWarga" enctype="multipart/form-data" method="POST" class="px-6 py-4 space-y-4">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="kode" id="kode">
      <input type="hidden" name="foto_lama" id="foto_lama">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label>NIK</label><input type="text" name="nik" id="nik" required pattern="\d{16}" title="Harus 16 digit angka" class="input w-full border"></div>
        <div><label>No KK</label><input type="text" name="nokk" id="nokk" required pattern="\d{16}" title="Harus 16 digit angka" class="input w-full border"></div>
        <div><label>Nama</label><input type="text" name="nama" id="nama" required class="input w-full border"></div>
        <div><label>Jenis Kelamin</label><select name="jenkel" id="jenkel" required class="input w-full border"><option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div>
        <div><label>Tempat Lahir</label><input type="text" name="tpt_lahir" id="tpt_lahir" required class="input w-full border"></div>
        <div><label>Tanggal Lahir</label><input type="date" name="tgl_lahir" id="tgl_lahir" required class="input w-full border"></div>
        <div><label>Agama</label><input type="text" name="agama" id="agama" required class="input w-full border"></div>
        <div><label>Status</label><input type="text" name="status" id="status" required class="input w-full border"></div>
        <div><label>Pekerjaan</label><input type="text" name="pekerjaan" id="pekerjaan" class="input w-full border"></div>
        <div><label>Alamat</label><input type="text" name="alamat" id="alamat" class="input w-full border"></div>
        <div><label>RT</label><input type="text" name="rt" id="rt" class="input w-full border"></div>
        <div><label>RW</label><input type="text" name="rw" id="rw" class="input w-full border"></div>
        <div><label>No HP</label><input type="text" name="hp" id="hp" pattern="\d{10,}" title="Minimal 10 digit angka" class="input w-full border"></div>
        <div><label>Hubungan</label><input type="text" name="hubungan" id="hubungan" class="input w-full border"></div>
        <div><label>Foto</label><input type="file" name="foto" id="foto" accept="image/*" class="input w-full border"></div>
        <div><label>Negara</label><select name="negara" id="negara" class="input w-full border"><option value="Indonesia" selected>Indonesia</option></select></div>
        <div><label>Provinsi</label><select name="provinsi" id="provinsi" class="input w-full border"></select></div>
        <div><label>Kota/Kabupaten</label><select name="kota" id="kota" class="input w-full border"></select></div>
        <div><label>Kecamatan</label><select name="kecamatan" id="kecamatan" class="input w-full border"></select></div>
        <div><label>Kelurahan</label><select name="kelurahan" id="kelurahan" class="input w-full border"></select></div>
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
}

function closeModal() {
  document.getElementById('modalWarga').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
  const negaraEl = document.getElementById('negara');
  const provinsiEl = document.getElementById('provinsi');
  const kotaEl = document.getElementById('kota');
  const kecamatanEl = document.getElementById('kecamatan');
  const kelurahanEl = document.getElementById('kelurahan');

  negaraEl.addEventListener('change', async () => {
    if (negaraEl.value === 'Indonesia') {
      try {
        const res = await fetch('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
        const data = await res.json();
        provinsiEl.innerHTML = '<option value="">Pilih Provinsi</option>' + data.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
      } catch (err) {
        alert('Gagal memuat data provinsi');
      }
    }
  });

  provinsiEl.addEventListener('change', async () => {
    const id = provinsiEl.value;
    try {
      const res = await fetch(`https://emsifa.github.io/api-wilayah-indonesia/api/regencies/${id}.json`);
      const data = await res.json();
      kotaEl.innerHTML = '<option value="">Pilih Kota</option>' + data.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
    } catch (err) {
      alert('Gagal memuat data kota');
    }
  });

  kotaEl.addEventListener('change', async () => {
    const id = kotaEl.value;
    try {
      const res = await fetch(`https://emsifa.github.io/api-wilayah-indonesia/api/districts/${id}.json`);
      const data = await res.json();
      kecamatanEl.innerHTML = '<option value="">Pilih Kecamatan</option>' + data.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
    } catch (err) {
      alert('Gagal memuat data kecamatan');
    }
  });

  kecamatanEl.addEventListener('change', async () => {
    const id = kecamatanEl.value;
    try {
      const res = await fetch(`https://emsifa.github.io/api-wilayah-indonesia/api/villages/${id}.json`);
      const data = await res.json();
      kelurahanEl.innerHTML = '<option value="">Pilih Kelurahan</option>' + data.map(d => `<option value="${d.name}">${d.name}</option>`).join('');
    } catch (err) {
      alert('Gagal memuat data kelurahan');
    }
  });
});
</script>
