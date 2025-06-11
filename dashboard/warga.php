<?php
// warga.php
require_once 'api/db.php'; // koneksi PDO
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Warga</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body class="bg-gray-100 p-4">
  <div class="container mx-auto">
    <div class="flex justify-between mb-4">
        <h1 class="text-2xl font-bold">Data Warga</h1>
        <button id="btnAdd" class="bg-blue-500 text-white px-4 py-2 rounded">+ Tambah</button>
        <form action="api/import_warga.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" accept=".csv" required>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">📥 Import CSV</button>
        </form>
        <a href="api/export_warga.php" class="bg-blue-500 text-white px-4 py-2 rounded">📤 Export CSV</a>
    </div>

    <div class="overflow-auto bg-white rounded shadow">
      <table class="min-w-full text-sm text-left border">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-2">Nama</th>
            <th class="p-2">NIK</th>
            <th class="p-2">Jenis Kelamin</th>
            <th class="p-2">Tgl Lahir</th>
            <th class="p-2">Alamat</th>
            <th class="p-2">Aksi</th>
          </tr>
        </thead>
        <tbody id="dataWarga">
          <!-- Data warga akan dimuat via AJAX -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Form -->
  <div id="modalForm" class="fixed inset-0 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-3xl p-6 rounded shadow relative overflow-y-auto max-h-screen">
      <h2 class="text-xl font-bold mb-4">Form Warga</h2>
      <form id="formWarga" enctype="multipart/form-data">
        <input type="hidden" name="id_warga" id="id_warga">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label>Nama</label>
            <input type="text" name="nama" id="nama" class="w-full border rounded p-2" required>
          </div>
          <div>
            <label>NIK</label>
            <input type="text" name="nik" id="nik" maxlength="16" pattern="\d{16}" title="16 digit angka" class="w-full border rounded p-2" required>
          </div>
          <div>
            <label>Hubungan</label>
            <select name="hubungan" id="hubungan" class="w-full border rounded p-2">
              <option>Suami</option>
              <option>Istri</option>
              <option>Anak</option>
              <option>Keluarga Lain</option>
            </select>
          </div>
          <div>
            <label>NIKK</label>
            <input type="text" name="nikk" id="nikk" maxlength="16" pattern="\d{16}" title="16 digit angka" class="w-full border rounded p-2" required>
          </div>
          <div>
            <label>Jenis Kelamin</label>
            <select name="jenkel" id="jenkel" class="w-full border rounded p-2">
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
          <div>
            <label>Tempat Lahir</label>
            <input type="text" name="tpt_lahir" id="tpt_lahir" class="w-full border rounded p-2" list="listKota">
            <datalist id="listKota"></datalist>
          </div>
          <div>
            <label>Tanggal Lahir</label>
            <input type="text" name="tgl_lahir" id="tgl_lahir" class="w-full border rounded p-2" required>
          </div>
          <div>
            <label>Alamat</label>
            <input type="text" name="alamat" id="alamat" class="w-full border rounded p-2" required>
          </div>
          <div>
            <label>RT</label>
            <input type="number" name="rt" id="rt" min="1" class="w-full border rounded p-2">
          </div>
          <div>
            <label>RW</label>
            <input type="number" name="rw" id="rw" min="1" class="w-full border rounded p-2">
          </div>
          <div>
            <label>Provinsi</label>
            <select name="propinsi" id="propinsi" class="w-full border rounded p-2"></select>
          </div>
          <div>
            <label>Kota</label>
            <select name="kota" id="kota" class="w-full border rounded p-2"></select>
          </div>
          <div>
            <label>Kecamatan</label>
            <select name="kecamatan" id="kecamatan" class="w-full border rounded p-2"></select>
          </div>
          <div>
            <label>Kelurahan</label>
            <select name="kelurahan" id="kelurahan" class="w-full border rounded p-2"></select>
          </div>
          <div>
            <label>Agama</label>
            <select name="agama" id="agama" class="w-full border rounded p-2">
              <option>Islam</option>
              <option>Kristen</option>
              <option>Katolik</option>
              <option>Hindu</option>
              <option>Budha</option>
              <option>Lainnya</option>
            </select>
          </div>
          <div>
            <label>Status</label>
            <select name="status" id="status" class="w-full border rounded p-2">
              <option>Tidak Kawin</option>
              <option>Kawin</option>
              <option>Janda</option>
              <option>Duda</option>
              <option>Lainnya</option>
            </select>
          </div>
          <div>
            <label>Pekerjaan</label>
            <select name="pekerjaan" id="pekerjaan" class="w-full border rounded p-2">
              <option>PNS</option>
              <option>Swasta</option>
              <option>Wirausaha</option>
              <option>Pelajar</option>
              <option>Lainnya</option>
            </select>
          </div>
          <div>
            <label>No. HP</label>
            <input type="text" name="hp" id="hp" class="w-full border rounded p-2">
          </div>
          <div>
            <label>Foto</label>
            <input type="file" name="foto" id="foto" class="w-full border rounded p-2">
          </div>
        </div>
        <div class="flex flex-wrap justify-between gap-2 mb-4">
          <button type="button" id="btnCancel" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
        </div>
      </form>
    </div>
  </div>

    <script src="js/warga.js"></script>

  <script>
    flatpickr("#tgl_lahir", { dateFormat: "Y-m-d" });

    $(document).ready(function() {
      // load data kota/kabupaten ke datalist tempat lahir
      axios.get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/36.json")
        .then(res => {
          let list = res.data;
          list.forEach(k => {
            $('#listKota').append(`<option value="${k.name}">`);
          });
        });

      // dropdown berantai wilayah
      axios.get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json").then(res => {
        res.data.forEach(p => {
          $('#propinsi').append(`<option value="${p.id}" data-name="${p.name}">${p.name}</option>`);
        });
      });

      $('#propinsi').on('change', function() {
        let id = $(this).val();
        $('#kota').html('');
        axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`).then(res => {
          res.data.forEach(k => {
            $('#kota').append(`<option value="${k.id}" data-name="${k.name}">${k.name}</option>`);
          });
        });
      });

      $('#kota').on('change', function() {
        let id = $(this).val();
        $('#kecamatan').html('');
        axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${id}.json`).then(res => {
          res.data.forEach(kec => {
            $('#kecamatan').append(`<option value="${kec.id}" data-name="${kec.name}">${kec.name}</option>`);
          });
        });
      });

      $('#kecamatan').on('change', function() {
        let id = $(this).val();
        $('#kelurahan').html('');
        axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${id}.json`).then(res => {
          res.data.forEach(kel => {
            $('#kelurahan').append(`<option value="${kel.name}">${kel.name}</option>`);
          });
        });
      });

      // tombol dan modal
      $('#btnAdd').click(() => $('#modalForm').removeClass('hidden flex'));
      $('#btnCancel').click(() => $('#modalForm').addClass('hidden'));
    });
  </script>
  
  <script>
  function loadWarga() {
    $.post('warga_action.php', { aksi: 'read' }, function(data) {
      let rows = '';
      JSON.parse(data).forEach(w => {
        rows += `
          <tr class="border-b">
            <td class="p-2">${w.nama}</td>
            <td class="p-2">${w.nik}</td>
            <td class="p-2">${w.jenkel}</td>
            <td class="p-2">${w.tgl_lahir}</td>
            <td class="p-2">${w.alamat}</td>
            <td class="p-2 flex gap-1">
              <button class="bg-yellow-400 text-white px-2 py-1 rounded editBtn" data-id="${w.id_warga}">Edit</button>
              <button class="bg-red-500 text-white px-2 py-1 rounded deleteBtn" data-id="${w.id_warga}">Hapus</button>
            </td>
          </tr>
        `;
      });
      $('#dataWarga').html(rows);
    });
  }

  // Simpan data
  $('#formWarga').submit(function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('aksi', $('#id_warga').val() ? 'update' : 'create');

    $.ajax({
      type: 'POST',
      url: 'warga_action.php',
      data: formData,
      contentType: false,
      processData: false,
      success: function(res) {
        const hasil = JSON.parse(res);
        if (hasil.status) {
          $('#modalForm').addClass('hidden');
          $('#formWarga')[0].reset();
          $('#id_warga').val('');
          loadWarga();
        } else {
          alert('Gagal menyimpan data.');
        }
      }
    });
  });

  // Edit
  $(document).on('click', '.editBtn', function() {
    const id = $(this).data('id');
    $.post('warga_action.php', { aksi: 'get', id }, function(res) {
      const data = JSON.parse(res);
      for (const k in data) {
        if (k != 'foto') $(`#${k}`).val(data[k]);
      }
      $('#modalForm').removeClass('hidden flex');
    });
  });

  // Hapus
  $(document).on('click', '.deleteBtn', function() {
    if (confirm('Yakin ingin menghapus data ini?')) {
      const id = $(this).data('id');
      $.post('warga_action.php', { aksi: 'delete', id }, function(res) {
        const hasil = JSON.parse(res);
        if (hasil.status) loadWarga();
        else alert('Gagal menghapus data.');
      });
    }
  });

  // Load awal
  loadWarga();
</script>

</body>
</html>
