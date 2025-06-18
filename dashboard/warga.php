<?php
// File: warga.php
session_start();
include 'api/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Warga</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-4">
  <div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Data Warga</h1>
    <button id="tambahBtn" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded">+</button>
    <table class="min-w-full bg-white shadow rounded">
      <thead>
        <tr class="bg-gray-200 text-left">
          <th class="py-2 px-4">Nama</th>
          <th class="py-2 px-4">NIK</th>
          <th class="py-2 px-4">Alamat</th>
          <th class="py-2 px-4">Aksi</th>
        </tr>
      </thead>
      <tbody id="dataBody"></tbody>
    </table>
  </div>

  <!-- Modal -->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-xl">
      <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Warga</h2>
      <form id="wargaForm">
        <input type="hidden" name="id_warga" id="id_warga">
        <input type="hidden" name="action" id="formAction" value="create">

        <div class="mb-2">
          <label class="block">Nama</label>
          <input type="text" name="nama" id="nama" class="w-full border px-2 py-1 rounded" required>
        </div>
        <div class="mb-2">
          <label class="block">NIK</label>
          <input type="text" name="nik" id="nik" class="w-full border px-2 py-1 rounded" required>
        </div>
        <div class="mb-2">
          <label class="block">Alamat</label>
          <textarea name="alamat" id="alamat" class="w-full border px-2 py-1 rounded" required></textarea>
        </div>

        <div class="flex justify-end mt-4">
          <button type="button" id="cancelBtn" class="mr-2 px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
          <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function loadData() {
      $.post('api/warga_action.php', { action: 'read' }, function(data) {
        const warga = JSON.parse(data);
        let html = '';
        warga.forEach(row => {
          html += `<tr class="border-b">
            <td class="px-4 py-2">${row.nama}</td>
            <td class="px-4 py-2">${row.nik}</td>
            <td class="px-4 py-2">${row.alamat}</td>
            <td class="px-4 py-2">
              <button class="editBtn px-2 py-1 bg-yellow-400 text-white rounded" data-id='${JSON.stringify(row)}'>Edit</button>
              <button class="deleteBtn px-2 py-1 bg-red-500 text-white rounded ml-2" data-id="${row.id_warga}">Hapus</button>
            </td>
          </tr>`;
        });
        $('#dataBody').html(html);
      });
    }

    $(document).ready(function() {
      loadData();

      $('#tambahBtn').click(() => {
        $('#modalTitle').text('Tambah Warga');
        $('#wargaForm')[0].reset();
        $('#formAction').val('create');
        $('#modal').removeClass('hidden flex').addClass('flex');
      });

      $('#cancelBtn').click(() => {
        $('#modal').addClass('hidden');
      });

      $('#wargaForm').submit(function(e) {
        e.preventDefault();
        $.post('api/warga_action.php', $(this).serialize(), function(res) {
          $('#modal').addClass('hidden');
          loadData();
        });
      });

      $(document).on('click', '.editBtn', function() {
        const data = $(this).data('id');
        $('#modalTitle').text('Edit Warga');
        for (const key in data) {
          $(`#${key}`).val(data[key]);
        }
        $('#formAction').val('update');
        $('#modal').removeClass('hidden flex').addClass('flex');
      });

      $(document).on('click', '.deleteBtn', function() {
        if (confirm('Yakin ingin menghapus data ini?')) {
          $.post('api/warga_action.php', { action: 'delete', id_warga: $(this).data('id') }, function() {
            loadData();
          });
        }
      });
    });
  </script>
</body>
</html>
