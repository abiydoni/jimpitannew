<?php
// File: warga.php
session_start();
include 'header.php';
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Data Warga</h3>
            <div class="mb-4 text-center">
                <button id="tambahBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">+ Tambah Warga</button>
                <button id="exportBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2">Export Excel</button>
                <button id="downloadTemplateBtn" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">Download Template</button>
                <label for="importInput" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded ml-2 cursor-pointer">Import Excel
                  <input type="file" id="importInput" accept=".xlsx,.xls" class="hidden" />
                </label>
            </div>
        </div>
        <div id="table-container"> <!-- Tambahkan div untuk menampung tabel -->
            <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-6">No</th>
                        <th class="py-2 px-6 text-left">NIK</th>
                        <th class="py-2 px-6 text-left">NIK KK</th>
                        <th class="py-2 px-6 text-left">Nama</th>
                        <th class="py-2 px-6 text-center">Jenis Kelamin</th>
                        <th class="py-2 px-6 text-center">Tanggal Lahir</th>
                        <th class="py-2 px-6 text-center">RT/RW</th>
                        <th class="py-2 px-6 text-left">No HP</th>
                        <th class="py-2 px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataBody"></tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Modal -->
<div id="modal" class="modal-overlay hidden">
    <div class="modal-container bg-white rounded-lg shadow-xl p-4 w-full max-w-xs max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b pb-2 mb-4">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Warga</h2>
        </div>
        <form id="wargaForm" class="text-sm">
        <input type="hidden" name="id_warga" id="id_warga">
        <input type="hidden" name="action" id="formAction" value="create">
        <input type="hidden" name="foto" id="foto" value="">
        <!-- Hidden input untuk menyimpan nama wilayah -->
        <input type="hidden" name="propinsi_nama" id="propinsi_nama">
        <input type="hidden" name="kota_nama" id="kota_nama">
        <input type="hidden" name="kecamatan_nama" id="kecamatan_nama">
        <input type="hidden" name="kelurahan_nama" id="kelurahan_nama">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- Data Pribadi -->
            <div class="space-y-3">
            <h3 class="font-semibold text-base border-b pb-1">Data Pribadi</h3>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Nama Lengkap *</label>
                <input type="text" name="nama" id="nama" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">NIK *</label>
                <input type="text" name="nik" id="nik" class="w-full border px-2 py-0.5 rounded text-sm form-input" required maxlength="16" pattern="\d{16}" title="NIK harus 16 digit angka">
                <small class="text-gray-500 text-xs">Format: 16 digit angka</small>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">NIK KK *</label>
                <input type="text" name="nikk" id="nikk" class="w-full border px-2 py-0.5 rounded text-sm form-input" required maxlength="16" pattern="\d{16}" title="NIK KK harus 16 digit angka">
                <small class="text-gray-500 text-xs">Format: 16 digit angka</small>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Hubungan dalam KK *</label>
                <select name="hubungan" id="hubungan" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
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
                <label class="block text-xs font-medium mb-0.5">Jenis Kelamin *</label>
                <select name="jenkel" id="jenkel" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                <option value="">Pilih Jenis Kelamin</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Tempat Lahir *</label>
                <input type="text" name="tpt_lahir" id="tpt_lahir" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Tanggal Lahir *</label>
                <input type="date" name="tgl_lahir" id="tgl_lahir" class="w-full border px-2 py-0.5 rounded text-sm form-input" required max="<?php echo date('Y-m-d'); ?>">
                <small class="text-gray-500 text-xs">Tidak boleh di masa depan</small>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Agama *</label>
                <select name="agama" id="agama" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
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
                <label class="block text-xs font-medium mb-0.5">Status Perkawinan *</label>
                <select name="status" id="status" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                <option value="">Pilih Status</option>
                <option value="Belum Kawin">Belum Kawin</option>
                <option value="Kawin">Kawin</option>
                <option value="Cerai Hidup">Cerai Hidup</option>
                <option value="Cerai Mati">Cerai Mati</option>
                <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Pekerjaan *</label>
                <select name="pekerjaan" id="pekerjaan" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                <option value="">Pilih Pekerjaan</option>
                  <option value="PNS">PNS</option>
                  <option value="TNI">TNI</option>
                  <option value="Polri">Polri</option>
                  <option value="Swasta">Swasta</option>
                  <option value="Wiraswasta">Wiraswasta</option>
                  <option value="Petani">Petani</option>
                  <option value="Pedagang">Pedagang</option>
                  <option value="Pelajar">Pelajar</option>
                  <option value="Tidak Bekerja">Tidak Bekerja</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            </div>

            <!-- Data Alamat -->
            <div class="space-y-3">
            <h3 class="font-semibold text-base border-b pb-1">Data Alamat</h3>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Alamat Lengkap *</label>
                <textarea name="alamat" id="alamat" class="w-full border px-2 py-0.5 rounded text-sm form-input" rows="2" required></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <div>
                <label class="block text-xs font-medium mb-0.5">RT *</label>
                <input type="text" name="rt" id="rt" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                </div>
                <div>
                <label class="block text-xs font-medium mb-0.5">RW *</label>
                <input type="text" name="rw" id="rw" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Provinsi *</label>
                <select name="propinsi" id="propinsi" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                    <option value="">Pilih Provinsi</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Kota/Kabupaten *</label>
                <select name="kota" id="kota" class="w-full border px-2 py-0.5 rounded text-sm form-input" required disabled>
                    <option value="">Pilih Kota/Kabupaten</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Kecamatan *</label>
                <select name="kecamatan" id="kecamatan" class="w-full border px-2 py-0.5 rounded text-sm form-input" required disabled>
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Kelurahan *</label>
                <select name="kelurahan" id="kelurahan" class="w-full border px-2 py-0.5 rounded text-sm form-input" required disabled>
                    <option value="">Pilih Kelurahan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-0.5">Negara *</label>
                <input type="text" name="negara" id="negara" class="w-full border px-2 py-0.5 rounded text-sm form-input" value="Indonesia" required>
            </div>
            <div>
                <label class="block text-xs font-medium mb-0.5">No. HP</label>
                <input type="text" name="hp" id="hp" class="w-full border px-2 py-0.5 rounded text-sm form-input" maxlength="15" pattern="\d{9,15}" title="Nomor HP harus 9-15 digit angka">
                <small class="text-gray-500 text-xs">Format: 9-15 digit angka</small>
            </div>
            </div>
        </div>

        <div class="flex justify-end mt-4 space-x-2 sticky bottom-0 bg-white pt-2 border-t">
            <button type="button" id="cancelBtn" class="px-3 py-1 bg-gray-400 text-white rounded hover:bg-gray-500 text-sm">Batal</button>
            <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">Simpan</button>
        </div>
        </form>
    </div>
</div>

  <?php include 'footer.php'; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <script>
    // Fungsi untuk memuat data provinsi
    function loadProvinsi() {
      $('#propinsi').html('<option value="">Loading provinsi...</option>');
      $.get('api/wilayah.php', { action: 'provinsi' }, function(data) {
        let html = '<option value="">Pilih Provinsi</option>';
        data.forEach(item => {
          html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
        });
        $('#propinsi').html(html);
      }).fail(function(xhr, status, error) {
        console.error('Error loading provinsi:', error);
        $('#propinsi').html('<option value="">Error loading provinsi</option>');
      });
    }

    // Fungsi untuk memuat data kota berdasarkan provinsi
    function loadKota(provinsi_id) {
      if (!provinsi_id) {
        $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true);
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
        return;
      }
      
      $('#kota').html('<option value="">Loading kota...</option>').prop('disabled', true);
      $.get('api/wilayah.php', { action: 'kota', provinsi_id: provinsi_id }, function(data) {
        let html = '<option value="">Pilih Kota/Kabupaten</option>';
        data.forEach(item => {
          html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
        });
        $('#kota').html(html).prop('disabled', false);
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
      }).fail(function(xhr, status, error) {
        console.error('Error loading kota:', error);
        $('#kota').html('<option value="">Error loading kota</option>').prop('disabled', true);
      });
    }

    // Fungsi untuk memuat data kecamatan berdasarkan kota
    function loadKecamatan(kota_id) {
      if (!kota_id) {
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
        return;
      }
      
      $('#kecamatan').html('<option value="">Loading kecamatan...</option>').prop('disabled', true);
      $.get('api/wilayah.php', { action: 'kecamatan', kota_id: kota_id }, function(data) {
        let html = '<option value="">Pilih Kecamatan</option>';
        data.forEach(item => {
          html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
        });
        $('#kecamatan').html(html).prop('disabled', false);
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
      }).fail(function(xhr, status, error) {
        console.error('Error loading kecamatan:', error);
        $('#kecamatan').html('<option value="">Error loading kecamatan</option>').prop('disabled', true);
      });
    }

    // Fungsi untuk memuat data kelurahan berdasarkan kecamatan
    function loadKelurahan(kecamatan_id) {
      if (!kecamatan_id) {
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
        return;
      }
      
      $('#kelurahan').html('<option value="">Loading kelurahan...</option>').prop('disabled', true);
      $.get('api/wilayah.php', { action: 'kelurahan', kecamatan_id: kecamatan_id }, function(data) {
        let html = '<option value="">Pilih Kelurahan</option>';
        data.forEach(item => {
          html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
        });
        $('#kelurahan').html(html).prop('disabled', false);
      }).fail(function(xhr, status, error) {
        console.error('Error loading kelurahan:', error);
        $('#kelurahan').html('<option value="">Error loading kelurahan</option>').prop('disabled', true);
      });
    }

    // Fungsi untuk mendapatkan nama wilayah berdasarkan ID
    function getWilayahName(element_id) {
      const element = $(`#${element_id}`);
      const selectedOption = element.find('option:selected');
      return selectedOption.data('name') || selectedOption.val();
    }

    function loadData() {
      $.post('api/warga_action.php', { action: 'read' }, function(data) {
        try {
          const warga = JSON.parse(data);
          let html = '';
          warga.forEach((row, idx) => {
            html += `<tr class="border-b hover:bg-gray-50">
              <td class="px-6 py-2">${idx + 1}</td>
              <td class="px-6 py-2 text-left">${row.nik || '-'}</td>
              <td class="px-6 py-2 text-left">${row.nikk || '-'}</td>
              <td class="px-6 py-2 text-left">${row.nama || '-'}</td>
              <td class="px-6 py-2 text-center">${row.jenkel || '-'}</td>
              <td class="px-6 py-2 text-center">${row.tgl_lahir || '-'}</td>
              <td class="px-6 py-2 text-center">${row.rt || '-'}/${row.rw || '-'}</td>
              <td class="px-6 py-2 text-left">${row.hp || '-'}</td>
              <td class="px-6 py-2">
                <button class="editBtn px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500" data-id='${JSON.stringify(row)}'><i class='bx bx-edit'></i></button>
                <button class="deleteBtn px-2 py-1 bg-red-500 text-white rounded ml-2 hover:bg-red-600" data-id="${row.id_warga}"><i class='bx bx-trash'></i></button>
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
      loadProvinsi(); // Load provinsi saat halaman dimuat

      // Event handler untuk dropdown wilayah
      $('#propinsi').change(function() {
        const provinsi_id = $(this).val();
        const propinsi_nama = getWilayahName('propinsi');
        $('#propinsi_nama').val(propinsi_nama);
        loadKota(provinsi_id);
      });

      $('#kota').change(function() {
        const kota_id = $(this).val();
        const kota_nama = getWilayahName('kota');
        $('#kota_nama').val(kota_nama);
        loadKecamatan(kota_id);
      });

      $('#kecamatan').change(function() {
        const kecamatan_id = $(this).val();
        const kecamatan_nama = getWilayahName('kecamatan');
        $('#kecamatan_nama').val(kecamatan_nama);
        loadKelurahan(kecamatan_id);
      });

      $('#kelurahan').change(function() {
        const kelurahan_nama = getWilayahName('kelurahan');
        $('#kelurahan_nama').val(kelurahan_nama);
      });

      $('#tambahBtn').click(() => {
        $('#modalTitle').text('Tambah Warga');
        $('#wargaForm')[0].reset();
        $('#formAction').val('create');
        $('#negara').val('Indonesia');
        // Reset dropdown wilayah
        $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true);
        $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
        $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
        
        // Debug: Log sebelum menampilkan modal
        console.log('=== MODAL DEBUG START ===');
        console.log('Before showing modal - Modal element:', $('#modal')[0]);
        console.log('Before showing modal - Modal classes:', $('#modal').attr('class'));
        
        // Tampilkan modal dengan cara yang sederhana
        $('#modal').removeClass('hidden').addClass('modal-show');
        
        // Debug: Log setelah menampilkan modal
        setTimeout(() => {
          console.log('After showing modal - Modal classes:', $('#modal').attr('class'));
          console.log('After showing modal - Modal z-index:', $('#modal').css('z-index'));
          console.log('After showing modal - Modal container z-index:', $('.modal-container').css('z-index'));
          console.log('After showing modal - Modal display:', $('#modal').css('display'));
          console.log('After showing modal - Modal visibility:', $('#modal').css('visibility'));
          console.log('After showing modal - Modal opacity:', $('#modal').css('opacity'));
          console.log('=== MODAL DEBUG END ===');
        }, 100);
        
        // Focus pada input pertama
        setTimeout(() => {
          $('#nama').focus();
        }, 200);
      });

      $('#cancelBtn').click(() => {
        console.log('Closing modal via cancel button');
        $('#modal').removeClass('modal-show').addClass('hidden');
      });

      // Tutup modal ketika klik di luar modal
      $('#modal').click(function(e) {
        if (e.target === this) {
          console.log('Closing modal via overlay click');
          $(this).removeClass('modal-show').addClass('hidden');
        }
      });

      // Tutup modal dengan tombol ESC
      $(document).keydown(function(e) {
        if (e.key === 'Escape' && !$('#modal').hasClass('hidden')) {
          console.log('Closing modal via ESC key');
          $('#modal').removeClass('modal-show').addClass('hidden');
        }
      });

      // Validasi real-time untuk NIK
      $('#nik, #nikk').on('input', function() {
        const value = $(this).val();
        const isValid = /^\d*$/.test(value);
        
        if (!isValid) {
          $(this).val(value.replace(/\D/g, ''));
        }
        
        // Validasi panjang NIK
        if (value.length === 16) {
          $(this).removeClass('border-red-500').addClass('border-green-500');
        } else if (value.length > 0) {
          $(this).removeClass('border-green-500').addClass('border-red-500');
        } else {
          $(this).removeClass('border-red-500 border-green-500');
        }
      });

      // Submit form dengan Enter pada input terakhir
      $('#negara').keydown(function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          $('#wargaForm').submit();
        }
      });

      // Focus pada input pertama saat modal dibuka
      $('#modal').on('shown', function() {
        $('#nama').focus();
      });

      $('#wargaForm').submit(function(e) {
        e.preventDefault();
        
        // Ambil nama wilayah dari hidden input
        const propinsi_nama = $('#propinsi_nama').val();
        const kota_nama = $('#kota_nama').val();
        const kecamatan_nama = $('#kecamatan_nama').val();
        const kelurahan_nama = $('#kelurahan_nama').val();
        
        // Validasi wilayah
        if (!propinsi_nama || !kota_nama || !kecamatan_nama || !kelurahan_nama) {
          alert('Silakan pilih wilayah lengkap (Provinsi, Kota, Kecamatan, Kelurahan)');
          return;
        }
        
        // Disable tombol submit dan tampilkan loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Menyimpan...');
        
        const formData = $(this).serialize();
        
        $.post('api/warga_action.php', formData, function(res) {
          $('#modal').removeClass('modal-show').addClass('hidden');
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
        }).always(function() {
          // Re-enable tombol submit
          submitBtn.prop('disabled', false).text(originalText);
        });
      });

      // Fungsi untuk menunggu dropdown ter-load
      function waitForDropdown(selector, maxWait = 5000) {
        return new Promise((resolve, reject) => {
          const startTime = Date.now();
          const checkInterval = setInterval(() => {
            const element = $(selector);
            const options = element.find('option');
            
            // Cek apakah dropdown sudah ter-load (bukan loading state)
            if (options.length > 1 && !element.prop('disabled') && 
                !options.first().text().includes('Loading') && 
                !options.first().text().includes('Error')) {
              clearInterval(checkInterval);
              resolve(element);
            }
            
            // Timeout setelah maxWait ms
            if (Date.now() - startTime > maxWait) {
              clearInterval(checkInterval);
              reject(new Error('Timeout waiting for dropdown'));
            }
          }, 100);
        });
      }

      // Fungsi untuk set nilai dropdown berdasarkan nama
      function setDropdownValue(selector, name) {
        const option = $(`${selector} option[data-name="${name}"]`);
        if (option.length > 0) {
          $(selector).val(option.val());
          return true;
        }
        return false;
      }

      $(document).on('click', '.editBtn', async function() {
        const data = $(this).data('id');
        $('#modalTitle').text('Edit Warga');
        
        // Set nilai form
        for (const key in data) {
          $(`#${key}`).val(data[key]);
        }
        
        // Set nama wilayah ke hidden input
        $('#propinsi_nama').val(data.propinsi || '');
        $('#kota_nama').val(data.kota || '');
        $('#kecamatan_nama').val(data.kecamatan || '');
        $('#kelurahan_nama').val(data.kelurahan || '');
        
        // Load dropdown wilayah berdasarkan data yang ada
        if (data.propinsi) {
          try {
            // Pastikan provinsi sudah ter-load
            await waitForDropdown('#propinsi');
            
            // Set provinsi
            if (setDropdownValue('#propinsi', data.propinsi) && data.kota) {
              // Load kota
              loadKota($('#propinsi').val());
              
              // Tunggu kota ter-load
              await waitForDropdown('#kota');
              
              // Set kota
              if (setDropdownValue('#kota', data.kota) && data.kecamatan) {
                // Load kecamatan
                loadKecamatan($('#kota').val());
                
                // Tunggu kecamatan ter-load
                await waitForDropdown('#kecamatan');
                
                // Set kecamatan
                if (setDropdownValue('#kecamatan', data.kecamatan) && data.kelurahan) {
                  // Load kelurahan
                  loadKelurahan($('#kecamatan').val());
                  
                  // Tunggu kelurahan ter-load
                  await waitForDropdown('#kelurahan');
                  
                  // Set kelurahan
                  setDropdownValue('#kelurahan', data.kelurahan);
                }
              }
            }
          } catch (error) {
            console.error('Error loading dropdown:', error);
          }
        }
        
        $('#formAction').val('update');
        $('#modal').removeClass('hidden').addClass('modal-show');
        // Focus pada input pertama
        setTimeout(() => {
          $('#nama').focus();
        }, 100);
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

      // Export ke Excel (semua field, dengan styling, tanpa id_warga dan tgl_warga)
      $('#exportBtn').click(function() {
        $.post('api/warga_action.php', { action: 'read' }, function(data) {
          try {
            const warga = JSON.parse(data);
            if (!warga.length) {
              alert('Tidak ada data untuk diexport!');
              return;
            }
            // Ambil semua key dari field tb_warga, kecuali id_warga dan tgl_warga
            const header = Object.keys(warga[0]).filter(h => h !== 'id_warga' && h !== 'tgl_warga');
            const rows = [header];
            warga.forEach(row => {
              rows.push(header.map(h => row[h] || ''));
            });
            const ws = XLSX.utils.aoa_to_sheet(rows);
            // Styling header: bold & background color
            header.forEach((h, idx) => {
              const cell = XLSX.utils.encode_cell({ r:0, c:idx });
              if (!ws[cell]) return;
              ws[cell].s = {
                font: { bold: true },
                fill: { patternType: 'solid', fgColor: { rgb: 'D1E7DD' } },
                alignment: { horizontal: 'center' },
                border: {
                  top: { style: 'thin', color: { rgb: 'AAAAAA' } },
                  bottom: { style: 'thin', color: { rgb: 'AAAAAA' } },
                  left: { style: 'thin', color: { rgb: 'AAAAAA' } },
                  right: { style: 'thin', color: { rgb: 'AAAAAA' } }
                }
              };
            });
            // Styling border untuk semua cell data
            for (let r = 1; r < rows.length; r++) {
              for (let c = 0; c < header.length; c++) {
                const cell = XLSX.utils.encode_cell({ r, c });
                if (!ws[cell]) continue;
                ws[cell].s = {
                  border: {
                    top: { style: 'thin', color: { rgb: 'AAAAAA' } },
                    bottom: { style: 'thin', color: { rgb: 'AAAAAA' } },
                    left: { style: 'thin', color: { rgb: 'AAAAAA' } },
                    right: { style: 'thin', color: { rgb: 'AAAAAA' } }
                  }
                };
              }
            }
            // Set auto width
            const wscols = header.map(() => ({ wch: 18 }));
            ws['!cols'] = wscols;
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'DataWarga');
            XLSX.writeFile(wb, 'data_warga.xlsx');
          } catch (e) {
            alert('Gagal export: ' + e);
          }
        });
      });

      // Import dari Excel (tanpa id_warga dan tgl_warga)
      $('#importInput').change(function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, { type: 'array' });
          const sheetName = workbook.SheetNames[0];
          const worksheet = workbook.Sheets[sheetName];
          const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
          if (json.length < 2) {
            alert('File kosong atau tidak ada data!');
            return;
          }
          const header = json[0].filter(h => h !== 'id_warga' && h !== 'tgl_warga');
          let sukses = 0, gagal = 0;
          for (let i = 1; i < json.length; i++) {
            const row = json[i];
            if (!row.length) continue;
            // Buat objek data sesuai header
            const dataWarga = { action: 'create' };
            header.forEach((h, idx) => {
              dataWarga[h] = row[idx] || '';
            });
            $.ajax({
              url: 'api/warga_action.php',
              type: 'POST',
              data: dataWarga,
              async: false,
              success: function(res) { sukses++; },
              error: function() { gagal++; }
            });
          }
          loadData();
          alert('Import selesai! Sukses: ' + sukses + ', Gagal: ' + gagal);
        };
        reader.readAsArrayBuffer(file);
      });

      // Download template Excel
      $('#downloadTemplateBtn').click(function() {
        // Header lengkap semua field tb_warga kecuali id_warga dan tgl_warga
        const header = [
          'nama', 'nik', 'nikk', 'hubungan', 'jenkel', 'tpt_lahir', 'tgl_lahir', 'alamat', 'rt', 'rw',
          'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'agama', 'status', 'pekerjaan', 'foto', 'hp'
        ];
        // Contoh data
        const contoh = [
          'Budi Santoso', '1234567890123456', '1234567890123456', 'Kepala Keluarga', 'Laki-laki', 'Jakarta', '1980-01-01', 'Jl. Mawar No. 1', '01', '02',
          'Kelurahan Mawar', 'Kecamatan Melati', 'Kota Jakarta', 'DKI Jakarta', 'Indonesia', 'Islam', 'Kawin', 'Karyawan', '', '081234567890'
        ];
        const rows = [header, contoh];
        const ws = XLSX.utils.aoa_to_sheet(rows);
        // Styling header: bold & background color
        header.forEach((h, idx) => {
          const cell = XLSX.utils.encode_cell({ r:0, c:idx });
          if (!ws[cell]) return;
          ws[cell].s = {
            font: { bold: true },
            fill: { patternType: 'solid', fgColor: { rgb: 'D1E7DD' } },
            alignment: { horizontal: 'center' }
          };
        });
        // Set auto width
        const wscols = header.map(() => ({ wch: 18 }));
        ws['!cols'] = wscols;
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'TemplateWarga');
        XLSX.writeFile(wb, 'template_warga.xlsx');
      });
    });
  </script>
