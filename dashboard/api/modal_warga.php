<?php
include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil data field
    $id = $_POST['id'] ?: null;
    $kode = $_POST['kode'] ?: uniqid();
    // ... (inisialisasi other fields sama seperti sebelumnya)

    // upload foto
    if (!empty($_FILES['foto']['name'])) {
        $foto = 'uploads/' . time() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    } else {
        $foto = $_POST['foto_lama'] ?? '';
    }

    // query INSERT atau UPDATE
    if ($id) {
        $sql = "UPDATE tb_warga SET nik=?, nikk=?, nama=?, jenkel=?, tpt_lahir=?, tgl_lahir=?, alamat=?, rt=?, rw=?, kelurahan=?, kecamatan=?, kota=?, propinsi=?, negara=?, agama=?, status=?, pekerjaan=?, hp=?, hubungan=?" . ($foto ? ", foto=?" : "") . " WHERE kode=?";
        $params = [$nik,$nokk,$nama,$jenkel,$tpt_lahir,$tgl_lahir,$alamat,$rt,$rw,$kelurahan,$kecamatan,$kota,$provinsi,$negara,$agama,$status,$pekerjaan,$hp,$hubungan];
        if ($foto) $params[] = $foto;
        $params[] = $kode;
    } else {
        $sql = "INSERT INTO tb_warga (...) VALUES (?,... ,NOW())";
        $params = [$kode,$nik,$nokk,$nama,$jenkel,$tpt_lahir,$tgl_lahir,$alamat,$rt,$rw,$kelurahan,$kecamatan,$kota,$provinsi,$negara,$agama,$status,$pekerjaan,$hp,$hubungan,$foto];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    header("Location: warga.php");
    exit;
}
?>

<div id="modalWarga" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg w-full max-w-3xl overflow-y-auto">
    <div class="px-6 py-4 border-b flex justify-between items-center">
      <h2 id="modalTitle" class="text-xl font-semibold">Tambah Warga</h2>
      <button onclick="closeModal()" class="text-xl">&times;</button>
    </div>
    <form id="formWarga" method="POST" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="kode" id="kode">
      <input type="hidden" name="foto_lama" id="foto_lama">

      <!-- Kolom input teks & validasi -->
      <div class="grid md:grid-cols-2 gap-4">
        <label>NIK<input type="text" id="nik" name="nik" pattern="\d{16}" required title="16 digit angka" class="border w-full"></label>
        <label>No KK<input type="text" id="nokk" name="nokk" pattern="\d{16}" required title="16 digit angka" class="border w-full"></label>
        <label>Nama<input type="text" id="nama" name="nama" required class="border w-full"></label>
        <label>Jenis Kelamin
          <select id="jenkel" name="jenkel" required class="border w-full">
            <option value="">Pilih</option>
            <option value="L">Laki-laki</option><option value="P">Perempuan</option>
          </select>
        </label>
        <label>Tempat Lahir<input type="text" id="tpt_lahir" name="tpt_lahir" required class="border w-full"></label>
        <label>Tanggal Lahir<input type="date" id="tgl_lahir" name="tgl_lahir" required class="border w-full"></label>

        <!-- Wilayah Dropdown -->
        <label>Provinsi
          <select id="provinsi" name="provinsi" required class="border w-full"></select>
        </label>
        <label>Kab/Kota
          <select id="kota" name="kota" required class="border w-full"></select>
        </label>
        <label>Kecamatan
          <select id="kecamatan" name="kecamatan" required class="border w-full"></select>
        </label>
        <label>Kelurahan
          <select id="kelurahan" name="kelurahan" required class="border w-full"></select>
        </label>

        <label>Alamat<input type="text" id="alamat" name="alamat" class="border w-full"></label>
        <label>RT<input type="text" id="rt" name="rt" class="border w-full"></label>
        <label>RW<input type="text" id="rw" name="rw" class="border w-full"></label>
        <label>No HP<input type="text" id="hp" name="hp" pattern="\d{10,}" title="min 10 digit angka" class="border w-full"></label>

        <!-- Lainnya -->
        <label>Pekerjaan<input type="text" id="pekerjaan" name="pekerjaan" class="border w-full"></label>
        <label>Agama<input type="text" id="agama" name="agama" class="border w-full"></label>
        <label>Status<input type="text" id="status" name="status" class="border w-full"></label>
        <label>Hubungan<input type="text" id="hubungan" name="hubungan" class="border w-full"></label>
        <label>Foto<input type="file" id="foto" name="foto" accept="image/*" class="border w-full"></label>
      </div>

      <div class="text-right border-t pt-4">
        <button type="button" onclick="closeModal()" class="mr-2">Batal</button>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
async function fetchJSON(url) {
  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error('Gagal fetch ' + url);
    return await response.json();
  } catch (err) {
    alert('Error memuat data wilayah: ' + err.message);
    console.error(err);
  }
}

async function initWilayah(selected = {}) {
  const provinsiSelect = document.getElementById('provinsi');
  const data = await fetchJSON('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
  provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>' +
    data.map(p => `<option value="${p.id}" ${selected.provinsi == p.id ? 'selected' : ''}>${p.name}</option>`).join('');
}

async function loadKota(provId, selected = {}) {
  const kotaSelect = document.getElementById('kota');
  const data = await fetchJSON(`https://emsifa.github.io/api-wilayah-indonesia/api/regencies/${provId}.json`);
  kotaSelect.innerHTML = '<option value="">Pilih Kota/Kab</option>' +
    data.map(k => `<option value="${k.id}" ${selected.kota == k.id ? 'selected' : ''}>${k.name}</option>`).join('');
}

async function loadKecamatan(kotaId, selected = {}) {
  const kecSelect = document.getElementById('kecamatan');
  const data = await fetchJSON(`https://emsifa.github.io/api-wilayah-indonesia/api/districts/${kotaId}.json`);
  kecSelect.innerHTML = '<option value="">Pilih Kecamatan</option>' +
    data.map(kec => `<option value="${kec.id}" ${selected.kecamatan == kec.id ? 'selected' : ''}>${kec.name}</option>`).join('');
}

async function loadKelurahan(kecId, selected = {}) {
  const kelSelect = document.getElementById('kelurahan');
  const data = await fetchJSON(`https://emsifa.github.io/api-wilayah-indonesia/api/villages/${kecId}.json`);
  kelSelect.innerHTML = '<option value="">Pilih Kelurahan</option>' +
    data.map(kel => `<option value="${kel.name}" ${selected.kelurahan == kel.name ? 'selected' : ''}>${kel.name}</option>`).join('');
}

document.getElementById('provinsi').addEventListener('change', e => {
  const provId = e.target.value;
  if (provId) loadKota(provId);
});

document.getElementById('kota').addEventListener('change', e => {
  const kotaId = e.target.value;
  if (kotaId) loadKecamatan(kotaId);
});

document.getElementById('kecamatan').addEventListener('change', e => {
  const kecId = e.target.value;
  if (kecId) loadKelurahan(kecId);
});

// Panggil init saat modal dibuka
function bukaModalWarga() {
  document.getElementById('modalWarga').classList.remove('hidden');
  document.getElementById('formWarga').reset();
  initWilayah(); // Pastikan ini dipanggil!
  document.getElementById('modalTitle').innerText = 'Tambah Warga';
}

function closeModal() {
  document.getElementById('modalWarga').classList.add('hidden');
}
</script>
