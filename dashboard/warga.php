<?php
// File: warga.php
session_start();
include 'api/db.php';
include 'header.php';
?>
<div class="table-data">
    <div class="order">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">Data Warga</h1>
            <button id="tambahBtn" class="mb-4 px-4 py-2 bg-blue-500 text-white rounded">+ Tambah Warga</button>
            <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded">
                <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="py-2 px-4">Nama</th>
                    <th class="py-2 px-4">NIK</th>
                    <th class="py-2 px-4">Hubungan</th>
                    <th class="py-2 px-4">Jenis Kelamin</th>
                    <th class="py-2 px-4">Tempat Lahir</th>
                    <th class="py-2 px-4">Tanggal Lahir</th>
                    <th class="py-2 px-4">Alamat</th>
                    <th class="py-2 px-4">RT/RW</th>
                    <th class="py-2 px-4">Aksi</th>
                </tr>
                </thead>
                <tbody id="dataBody"></tbody>
            </table>
            </div>
        </div>
    </div>
</div>

    <!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl max-h-screen overflow-y-auto">
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Warga</h2>
        <form id="wargaForm">
        <input type="hidden" name="id_warga" id="id_warga">
        <input type="hidden" name="action" id="formAction" value="create">
        <input type="hidden" name="foto" id="foto" value="">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Data Pribadi -->
            <div class="space-y-4">
            <h3 class="font-semibold text-lg border-b pb-2">Data Pribadi</h3>
            
            <div>
                <label class="block text-sm font-medium mb-1">Nama Lengkap *</label>
                <input type="text" name="nama" id="nama" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">NIK *</label>
                <input type="text" name="nik" id="nik" class="w-full border px-3 py-2 rounded" required maxlength="16">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">NIK KK</label>
                <input type="text" name="nikk" id="nikk" class="w-full border px-3 py-2 rounded" maxlength="16">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Hubungan dalam KK *</label>
                <select name="hubungan" id="hubungan" class="w-full border px-3 py-2 rounded" required>
                <option value="">Pilih Hubungan</option>
                <option value="Kepala Keluarga">Kepala Keluarga</option>
                <option value="Istri">Istri</option>
                <option value="Anak">Anak</option>
                <option value="Orang Tua">Orang Tua</option>
                <option value="Mertua">Mertua</option>
                <option value="Famili Lain">Famili Lain</option>
                <option value="Pembantu">Pembantu</option>
                <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Jenis Kelamin *</label>
                <select name="jenkel" id="jenkel" class="w-full border px-3 py-2 rounded" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Tempat Lahir *</label>
                <input type="text" name="tpt_lahir" id="tpt_lahir" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Lahir *</label>
                <input type="date" name="tgl_lahir" id="tgl_lahir" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Agama *</label>
                <select name="agama" id="agama" class="w-full border px-3 py-2 rounded" required>
                <option value="">Pilih Agama</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
                <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Status Perkawinan *</label>
                <select name="status" id="status" class="w-full border px-3 py-2 rounded" required>
                <option value="">Pilih Status</option>
                <option value="Belum Kawin">Belum Kawin</option>
                <option value="Kawin">Kawin</option>
                <option value="Cerai Hidup">Cerai Hidup</option>
                <option value="Cerai Mati">Cerai Mati</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Pekerjaan *</label>
                <input type="text" name="pekerjaan" id="pekerjaan" class="w-full border px-3 py-2 rounded" required>
            </div>
            </div>

            <!-- Data Alamat -->
            <div class="space-y-4">
            <h3 class="font-semibold text-lg border-b pb-2">Data Alamat</h3>
            
            <div>
                <label class="block text-sm font-medium mb-1">Alamat Lengkap *</label>
                <textarea name="alamat" id="alamat" class="w-full border px-3 py-2 rounded" rows="3" required></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <div>
                <label class="block text-sm font-medium mb-1">RT *</label>
                <input type="text" name="rt" id="rt" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div>
                <label class="block text-sm font-medium mb-1">RW *</label>
                <input type="text" name="rw" id="rw" class="w-full border px-3 py-2 rounded" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Kelurahan *</label>
                <input type="text" name="kelurahan" id="kelurahan" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Kecamatan *</label>
                <input type="text" name="kecamatan" id="kecamatan" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Kota/Kabupaten *</label>
                <input type="text" name="kota" id="kota" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Provinsi *</label>
                <input type="text" name="propinsi" id="propinsi" class="w-full border px-3 py-2 rounded" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Negara *</label>
                <input type="text" name="negara" id="negara" class="w-full border px-3 py-2 rounded" value="Indonesia" required>
            </div>
            </div>
        </div>

        <div class="flex justify-end mt-6 space-x-2">
            <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Simpan</button>
        </div>
        </form>
    </div>
</div>

  <?php include 'footer.php'; ?>

  <script>
    function loadData() {
      $.post('api/warga_action.php', { action: 'read' }, function(data) {
        try {
          const warga = JSON.parse(data);
          let html = '';
          warga.forEach(row => {
            html += `<tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-2">${row.nama || '-'}</td>
              <td class="px-4 py-2">${row.nik || '-'}</td>
              <td class="px-4 py-2">${row.hubungan || '-'}</td>
              <td class="px-4 py-2">${row.jenkel || '-'}</td>
              <td class="px-4 py-2">${row.tpt_lahir || '-'}</td>
              <td class="px-4 py-2">${row.tgl_lahir || '-'}</td>
              <td class="px-4 py-2">${row.alamat || '-'}</td>
              <td class="px-4 py-2">${row.rt || '-'}/${row.rw || '-'}</td>
              <td class="px-4 py-2">
                <button class="editBtn px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500" data-id='${JSON.stringify(row)}'>Edit</button>
                <button class="deleteBtn px-2 py-1 bg-red-500 text-white rounded ml-2 hover:bg-red-600" data-id="${row.id_warga}">Hapus</button>
              </td>
            </tr>`;
          });
          $('#dataBody').html(html);
        } catch (e) {
          console.error('Error parsing data:', e);
          $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error loading data</td></tr>');
        }
      }).fail(function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
        $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error loading data: ' + error + '</td></tr>');
      });
    }

    $(document).ready(function() {
      loadData();

      $('#tambahBtn').click(() => {
        $('#modalTitle').text('Tambah Warga');
        $('#wargaForm')[0].reset();
        $('#formAction').val('create');
        $('#negara').val('Indonesia');
        $('#modal').removeClass('hidden flex').addClass('flex');
      });

      $('#cancelBtn').click(() => {
        $('#modal').addClass('hidden');
      });

      // Tutup modal ketika klik di luar modal
      $('#modal').click(function(e) {
        if (e.target === this) {
          $(this).addClass('hidden');
        }
      });

      $('#wargaForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.post('api/warga_action.php', formData, function(res) {
          $('#modal').addClass('hidden');
          loadData();
          if (res === 'success' || res === 'updated') {
            alert('Data berhasil disimpan!');
          } else {
            alert('Response: ' + res);
          }
        }).fail(function(xhr, status, error) {
          console.error('Submit Error:', status, error);
          let errorMsg = 'Error saving data';
          if (xhr.responseText) {
            try {
              const errorData = JSON.parse(xhr.responseText);
              errorMsg = errorData.error || errorMsg;
            } catch (e) {
              errorMsg = xhr.responseText;
            }
          }
          alert('Error: ' + errorMsg);
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
          $.post('api/warga_action.php', { action: 'delete', id_warga: $(this).data('id') }, function(res) {
            loadData();
            if (res === 'deleted') {
              alert('Data berhasil dihapus!');
            } else {
              alert('Response: ' + res);
            }
          }).fail(function(xhr, status, error) {
            console.error('Delete Error:', status, error);
            alert('Error deleting data: ' + error);
          });
        }
      });
    });
  </script>
