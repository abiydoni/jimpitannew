<?php
session_start();
include 'header.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}
    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
include 'api/db.php';
?>


<div class="table-data">
    <div class="order">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">Data Warga</h1>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">+ Tambah Warga</button>
        </div>
        <table class="min-w-full bg-white border">
            <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Nama</th>
                <th class="px-4 py-2 border">NIK</th>
                <th class="px-4 py-2 border">JK</th>
                <th class="px-4 py-2 border">TTL</th>
                <th class="px-4 py-2 border">Alamat</th>
                <th class="px-4 py-2 border">Pekerjaan</th>
                <th class="px-4 py-2 border">Aksi</th>
            </tr>
            </thead>
            <tbody id="data-warga" class="text-sm">
            <!-- Data warga akan dimuat di sini -->
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- MODAL FORM -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl relative overflow-y-auto max-h-[90vh]">
    <h2 class="text-xl font-semibold mb-4">Form Warga</h2>
    <form id="formWarga">
        <input type="hidden" name="id_warga" id="id_warga">
        <input type="hidden" name="aksi" value="save">
        
        <!-- Form fields -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
        <input type="text" name="kode" id="kode" placeholder="Kode" class="border p-2 rounded">
        <input type="text" name="nama" id="nama" placeholder="Nama" class="border p-2 rounded">
        <input type="text" name="nik" id="nik" placeholder="NIK" class="border p-2 rounded">
        <input type="text" name="hubungan" id="hubungan" placeholder="Hubungan" class="border p-2 rounded">
        <input type="text" name="nikk" id="nikk" placeholder="NIK KK" class="border p-2 rounded">
        <input type="text" name="jenkel" id="jenkel" placeholder="Jenis Kelamin" class="border p-2 rounded">
        <input type="text" name="tpt_lahir" id="tpt_lahir" placeholder="Tempat Lahir" class="border p-2 rounded">
        <input type="date" name="tgl_lahir" id="tgl_lahir" class="border p-2 rounded">
        <textarea name="alamat" id="alamat" placeholder="Alamat" class="border p-2 rounded col-span-1 sm:col-span-2"></textarea>
        <input type="text" name="rt" id="rt" placeholder="RT" class="border p-2 rounded">
        <input type="text" name="rw" id="rw" placeholder="RW" class="border p-2 rounded">
        <input type="text" name="kelurahan" id="kelurahan" placeholder="Kelurahan" class="border p-2 rounded">
        <input type="text" name="kecamatan" id="kecamatan" placeholder="Kecamatan" class="border p-2 rounded">
        <input type="text" name="kota" id="kota" placeholder="Kota" class="border p-2 rounded">
        <input type="text" name="propinsi" id="propinsi" placeholder="Provinsi" class="border p-2 rounded">
        <input type="text" name="negara" id="negara" placeholder="Negara" class="border p-2 rounded">
        <input type="text" name="agama" id="agama" placeholder="Agama" class="border p-2 rounded">
        <input type="text" name="status" id="status" placeholder="Status" class="border p-2 rounded">
        <input type="text" name="pekerjaan" id="pekerjaan" placeholder="Pekerjaan" class="border p-2 rounded">
        </div>

        <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded">Batal</button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        </div>
    </form>
    </div>
</div>

  <?php include 'footer.php'; ?>

  <script>
    function loadData() {
      $.post('api/warga_action.php', { aksi: 'read' }, function(data) {
        $('#data-warga').html(data);
      });
    }

    $('#formWarga').on('submit', function(e) {
      e.preventDefault();
      $.post('api/warga_action.php', $(this).serialize(), function() {
        loadData();
        closeModal();
        $('#formWarga')[0].reset();
        $('#id_warga').val('');
      });
    });

    function editData(id) {
      $.post('api/warga_action.php', { aksi: 'get', id }, function(data) {
        const obj = JSON.parse(data);
        for (let key in obj) {
          $('#' + key).val(obj[key]);
        }
        openModal();
      });
    }

    function hapusData(id) {
      if (confirm('Yakin hapus data ini?')) {
        $.post('api/warga_action.php', { aksi: 'delete', id }, function() {
          loadData();
        });
      }
    }

    function openModal() {
      $('#modal').removeClass('hidden flex').addClass('flex');
    }

    function closeModal() {
      $('#modal').addClass('hidden').removeClass('flex');
    }

    // Load data on page load
    $(document).ready(loadData);
  </script>