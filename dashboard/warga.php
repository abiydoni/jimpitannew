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
            <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-6 w-10">No</th>
                        <th class="py-2 px-6 w-40 text-left">NIK</th>
                        <th class="py-2 px-6 w-40 text-left">NIK KK</th>
                        <th class="py-2 px-6 w-56 text-left">Nama</th>
                        <th class="py-2 px-6 w-32 text-center">Jenis Kelamin</th>
                        <th class="py-2 px-6 w-36 text-left">Tanggal Lahir</th>
                        <th class="py-2 px-6 w-32 text-center">RT/RW</th>
                        <th class="py-2 px-6 w-44 text-left">No HP</th>
                        <th class="py-2 px-6 w-32 text-center">Aksi</th>
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
                <div class="flex gap-2">
                  <select id="tgl_hari" class="border rounded px-1 py-0.5 text-sm" style="width:60px"></select>
                  <select id="tgl_bulan" class="border rounded px-1 py-0.5 text-sm" style="width:110px"></select>
                  <select id="tgl_tahun" class="border rounded px-1 py-0.5 text-sm" style="width:80px"></select>
                </div>
                <input type="hidden" name="tgl_lahir" id="tgl_lahir" required>
                <small class="text-gray-500 text-xs">Pilih hari, bulan, dan tahun</small>
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
                <select name="rt" id="rt" class="w-full border px-2 py-0.5 rounded text-sm form-input" required></select>
                </div>
                <div>
                <label class="block text-xs font-medium mb-0.5">RW *</label>
                <select name="rw" id="rw" class="w-full border px-2 py-0.5 rounded text-sm form-input" required></select>
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

<!-- Modal Biodata Warga -->
<div id="modalBiodata" class="modal-overlay hidden">
  <div class="modal-container bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-screen overflow-y-auto">
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-xl font-semibold">Biodata Lengkap Warga</h2>
      <button onclick="closeModalBiodata()" class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
    </div>
    
    <div id="biodataContent" class="px-6 py-4">
      <!-- Content akan diisi oleh JavaScript -->
    </div>
  </div>
</div>

<!-- Modal Biodata KK -->
<div id="modalKK" class="modal-overlay hidden">
  <div class="modal-container bg-white rounded-lg shadow-lg w-full max-w-6xl max-h-screen overflow-y-auto">
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h2 class="text-xl font-semibold">Biodata Kartu Keluarga</h2>
      <button onclick="closeModalKK()" class="text-gray-600 hover:text-red-600 text-xl">&times;</button>
    </div>
    
    <div id="kkContent" class="px-6 py-4">
      <!-- Content akan diisi oleh JavaScript -->
    </div>
  </div>
</div>

<!-- Modal Loading untuk Import -->
<div id="loadingModal" class="modal-overlay hidden">
    <div class="modal-container bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold mb-2">Memproses Import Data</h3>
            <p id="loadingText" class="text-gray-600 mb-4">Sedang memvalidasi file...</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                <div id="progressBar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="progressText" class="text-sm text-gray-500">0% selesai</p>
        </div>
    </div>
</div>

  <?php include 'footer.php'; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <script>
    // Fungsi global untuk modal dan print
    function showBiodata(nik) {
      if (!nik || nik === '-') {
        alert('NIK tidak valid');
        return;
      }
      
      console.log('Showing biodata for NIK:', nik);
      
      // Tampilkan loading
      $('#biodataContent').html('<div class="text-center py-8"><div class="animate-spin border-4 border-blue-500 border-t-transparent rounded-full w-8 h-8 mx-auto"></div><p class="mt-2">Memuat biodata...</p></div>');
      $('#modalBiodata').removeClass('hidden').addClass('modal-show');
      
      $.post('api/warga_action.php', { action: 'get_warga_by_nik', nik: nik }, function(data) {
        console.log('Biodata response:', data);
        try {
          const warga = JSON.parse(data);
          displayBiodata(warga);
        } catch (e) {
          console.error('Error parsing biodata:', e);
          $('#biodataContent').html('<div class="text-center py-8 text-red-500">Error: ' + e.message + '</div>');
        }
      }).fail(function(xhr, status, error) {
        console.error('Biodata AJAX error:', error);
        $('#biodataContent').html('<div class="text-center py-8 text-red-500">Error: ' + error + '</div>');
      });
    }
    
    function showKK(nikk) {
      if (!nikk || nikk === '-') {
        alert('NIKK tidak valid');
        return;
      }
      
      console.log('Showing KK for NIKK:', nikk);
      
      // Tampilkan loading
      $('#kkContent').html('<div class="text-center py-8"><div class="animate-spin border-4 border-green-500 border-t-transparent rounded-full w-8 h-8 mx-auto"></div><p class="mt-2">Memuat data KK...</p></div>');
      $('#modalKK').removeClass('hidden').addClass('modal-show');
      
      $.post('api/warga_action.php', { action: 'get_kk_by_nikk', nikk: nikk }, function(data) {
        console.log('KK response:', data);
        try {
          const kk = JSON.parse(data);
          displayKK(kk);
        } catch (e) {
          console.error('Error parsing KK:', e);
          $('#kkContent').html('<div class="text-center py-8 text-red-500">Error: ' + e.message + '</div>');
        }
      }).fail(function(xhr, status, error) {
        console.error('KK AJAX error:', error);
        $('#kkContent').html('<div class="text-center py-8 text-red-500">Error: ' + error + '</div>');
      });
    }
    
    function closeModalBiodata() {
      $('#modalBiodata').removeClass('modal-show').addClass('hidden');
    }
    
    function closeModalKK() {
      $('#modalKK').removeClass('modal-show').addClass('hidden');
    }
    
    function printBiodata() {
      const printContent = document.getElementById('biodataPrintArea').innerHTML;
      const printWindow = window.open('', '_blank');
      printWindow.document.write(`
        <html>
          <head>
            <title>Biodata Warga</title>
            <style>
              body { font-family: Arial, sans-serif; margin: 20px; }
              .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
              .space-y-4 > * + * { margin-top: 16px; }
              .space-y-6 > * + * { margin-top: 24px; }
              .border-b { border-bottom: 1px solid #e5e7eb; padding-bottom: 8px; }
              .text-lg { font-size: 18px; }
              .font-semibold { font-weight: 600; }
              .text-sm { font-size: 14px; }
              .flex { display: flex; }
              .justify-between { justify-content: space-between; }
              .gap-3 > * + * { margin-top: 12px; }
              @media print {
                .grid { grid-template-columns: 1fr 1fr; }
              }
            </style>
          </head>
          <body>
            <h2 style="text-align: center; margin-bottom: 30px;">BIODATA LENGKAP WARGA</h2>
            ${printContent}
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
    }
    
    function printKK() {
      const printContent = document.getElementById('kkPrintArea').innerHTML;
      const printWindow = window.open('', '_blank');
      printWindow.document.write(`
        <html>
          <head>
            <title>Biodata Kartu Keluarga</title>
            <style>
              body { font-family: Arial, sans-serif; margin: 20px; }
              .space-y-6 > * + * { margin-top: 24px; }
              .bg-gray-50 { background-color: #f9fafb; padding: 16px; border-radius: 8px; }
              .mb-6 { margin-bottom: 24px; }
              .text-lg { font-size: 18px; }
              .font-semibold { font-weight: 600; }
              .text-sm { font-size: 14px; }
              .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
              .flex { display: flex; }
              .justify-between { justify-content: space-between; }
              .gap-4 > * + * { margin-top: 16px; }
              .mb-4 { margin-bottom: 16px; }
              table { width: 100%; border-collapse: collapse; margin-top: 16px; }
              th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
              th { background-color: #f3f4f6; font-weight: 600; }
              .text-center { text-align: center; }
              @media print {
                .grid { grid-template-columns: 1fr 1fr; }
                table { font-size: 12px; }
                th, td { padding: 4px; }
              }
            </style>
          </head>
          <body>
            <h2 style="text-align: center; margin-bottom: 30px;">BIODATA KARTU KELUARGA</h2>
            ${printContent}
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.print();
    }

    function displayBiodata(warga) {
      const tanggalLahir = warga.tgl_lahir && warga.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(warga.tgl_lahir) : '-';
      
      const html = `
        <div class="space-y-6">
          <!-- Header dengan tombol print -->
          <div class="flex justify-between items-center border-b pb-4">
            <h3 class="text-xl font-semibold">Biodata Lengkap Warga</h3>
            <button onclick="printBiodata()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
              <i class='bx bx-printer'></i> Print
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="biodataPrintArea">
            <!-- Data Pribadi -->
            <div class="space-y-4">
              <h4 class="text-lg font-semibold border-b pb-2">Data Pribadi</h4>
              
              <div class="grid grid-cols-1 gap-3 text-sm">
                <div class="flex justify-between"><strong>NIK:</strong> <span>${warga.nik || '-'}</span></div>
                <div class="flex justify-between"><strong>NIK KK:</strong> <span>${warga.nikk || '-'}</span></div>
                <div class="flex justify-between"><strong>Nama Lengkap:</strong> <span>${warga.nama || '-'}</span></div>
                <div class="flex justify-between"><strong>Jenis Kelamin:</strong> <span>${warga.jenkel === 'L' ? 'Laki-laki' : warga.jenkel === 'P' ? 'Perempuan' : '-'}</span></div>
                <div class="flex justify-between"><strong>Tempat Lahir:</strong> <span>${warga.tpt_lahir || '-'}</span></div>
                <div class="flex justify-between"><strong>Tanggal Lahir:</strong> <span>${tanggalLahir}</span></div>
                <div class="flex justify-between"><strong>Agama:</strong> <span>${warga.agama || '-'}</span></div>
                <div class="flex justify-between"><strong>Status Perkawinan:</strong> <span>${warga.status || '-'}</span></div>
                <div class="flex justify-between"><strong>Pekerjaan:</strong> <span>${warga.pekerjaan || '-'}</span></div>
                <div class="flex justify-between"><strong>Hubungan dalam KK:</strong> <span>${warga.hubungan || '-'}</span></div>
                <div class="flex justify-between"><strong>No. HP:</strong> <span>${warga.hp || '-'}</span></div>
              </div>
            </div>
            
            <!-- Data Alamat -->
            <div class="space-y-4">
              <h4 class="text-lg font-semibold border-b pb-2">Data Alamat</h4>
              
              <div class="grid grid-cols-1 gap-3 text-sm">
                <div class="flex justify-between"><strong>Alamat:</strong> <span>${warga.alamat || '-'}</span></div>
                <div class="flex justify-between"><strong>RT/RW:</strong> <span>${warga.rt || '-'}/${warga.rw || '-'}</span></div>
                <div class="flex justify-between"><strong>Kelurahan:</strong> <span>${warga.kelurahan || '-'}</span></div>
                <div class="flex justify-between"><strong>Kecamatan:</strong> <span>${warga.kecamatan || '-'}</span></div>
                <div class="flex justify-between"><strong>Kota/Kabupaten:</strong> <span>${warga.kota || '-'}</span></div>
                <div class="flex justify-between"><strong>Provinsi:</strong> <span>${warga.propinsi || '-'}</span></div>
                <div class="flex justify-between"><strong>Negara:</strong> <span>${warga.negara || '-'}</span></div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      $('#biodataContent').html(html);
    }
    
    function displayKK(kk) {
      const kepalaKK = kk.kepala_keluarga;
      const anggotaKK = kk.anggota_keluarga;
      const tanggalLahirKK = kepalaKK.tgl_lahir && kepalaKK.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(kepalaKK.tgl_lahir) : '-';
      
      let anggotaHTML = '';
      anggotaKK.forEach((anggota, index) => {
        const tanggalLahirAnggota = anggota.tgl_lahir && anggota.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(anggota.tgl_lahir) : '-';
        anggotaHTML += `
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2 text-center">${index + 1}</td>
            <td class="px-4 py-2">${anggota.nik || '-'}</td>
            <td class="px-4 py-2">${anggota.nama || '-'}</td>
            <td class="px-4 py-2 text-center">${anggota.jenkel === 'L' ? 'Laki-laki' : anggota.jenkel === 'P' ? 'Perempuan' : '-'}</td>
            <td class="px-4 py-2">${anggota.tpt_lahir || '-'}</td>
            <td class="px-4 py-2 text-center">${tanggalLahirAnggota}</td>
            <td class="px-4 py-2">${anggota.agama || '-'}</td>
            <td class="px-4 py-2">${anggota.status || '-'}</td>
            <td class="px-4 py-2">${anggota.pekerjaan || '-'}</td>
            <td class="px-4 py-2">${anggota.hubungan || '-'}</td>
          </tr>
        `;
      });
      
      const html = `
        <div class="space-y-6">
          <!-- Header dengan tombol print -->
          <div class="flex justify-between items-center border-b pb-4">
            <h3 class="text-xl font-semibold">Biodata Kartu Keluarga</h3>
            <button onclick="printKK()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
              <i class='bx bx-printer'></i> Print
            </button>
          </div>
          
          <div id="kkPrintArea">
            <!-- Info KK -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <h4 class="text-lg font-semibold mb-4">Informasi Kartu Keluarga</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between"><strong>NIK KK:</strong> <span>${kepalaKK.nikk || '-'}</span></div>
                <div class="flex justify-between"><strong>Nama Kepala Keluarga:</strong> <span>${kepalaKK.nama || '-'}</span></div>
                <div class="flex justify-between"><strong>Alamat:</strong> <span>${kepalaKK.alamat || '-'}</span></div>
                <div class="flex justify-between"><strong>RT/RW:</strong> <span>${kepalaKK.rt || '-'}/${kepalaKK.rw || '-'}</span></div>
                <div class="flex justify-between"><strong>Kelurahan:</strong> <span>${kepalaKK.kelurahan || '-'}</span></div>
                <div class="flex justify-between"><strong>Kecamatan:</strong> <span>${kepalaKK.kecamatan || '-'}</span></div>
                <div class="flex justify-between"><strong>Kota/Kabupaten:</strong> <span>${kepalaKK.kota || '-'}</span></div>
                <div class="flex justify-between"><strong>Provinsi:</strong> <span>${kepalaKK.propinsi || '-'}</span></div>
                <div class="flex justify-between"><strong>Total Anggota:</strong> <span>${kk.total_anggota} orang</span></div>
              </div>
            </div>
            
            <!-- Daftar Anggota KK -->
            <div>
              <h4 class="text-lg font-semibold mb-4">Daftar Anggota Keluarga</h4>
              <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                  <thead>
                    <tr class="bg-gray-100">
                      <th class="border border-gray-300 px-4 py-2 text-center">No</th>
                      <th class="border border-gray-300 px-4 py-2">NIK</th>
                      <th class="border border-gray-300 px-4 py-2">Nama</th>
                      <th class="border border-gray-300 px-4 py-2 text-center">Jenis Kelamin</th>
                      <th class="border border-gray-300 px-4 py-2">Tempat Lahir</th>
                      <th class="border border-gray-300 px-4 py-2 text-center">Tanggal Lahir</th>
                      <th class="border border-gray-300 px-4 py-2">Agama</th>
                      <th class="border border-gray-300 px-4 py-2">Status</th>
                      <th class="border border-gray-300 px-4 py-2">Pekerjaan</th>
                      <th class="border border-gray-300 px-4 py-2">Hubungan</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${anggotaHTML}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      `;
      
      $('#kkContent').html(html);
    }

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
      console.log('Loading data...');
      $.post('api/warga_action.php', { action: 'read' }, function(data) {
        console.log('Raw response from server:', data);
        try {
          const warga = JSON.parse(data);
          console.log('Parsed data:', warga);
          
          if (!Array.isArray(warga)) {
            console.error('Data is not an array:', warga);
            $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error: Data format tidak valid</td></tr>');
            return;
          }
          
          let html = '';
          warga.forEach((row, idx) => {
            try {
              // Safe handling untuk tanggal lahir
              let tanggalLahir = '-';
              if (row.tgl_lahir && row.tgl_lahir !== '0000-00-00') {
                try {
                  tanggalLahir = formatDateForDisplay(row.tgl_lahir);
                } catch (dateError) {
                  console.warn(`Error formatting date for row ${idx}:`, dateError);
                  tanggalLahir = row.tgl_lahir || '-';
                }
              }
              
              html += `<tr class="border-b hover:bg-gray-50">
                <td class="px-6 py-2 w-10">${idx + 1}</td>
                <td class="px-6 py-2 w-40 text-left">
                  <span class="text-blue-600 hover:text-blue-800 cursor-pointer underline" onclick="showBiodata('${row.nik || ''}')">${row.nik || '-'}</span>
                </td>
                <td class="px-6 py-2 w-40 text-left">
                  <span class="text-green-600 hover:text-green-800 cursor-pointer underline" onclick="showKK('${row.nikk || ''}')">${row.nikk || '-'}</span>
                </td>
                <td class="px-6 py-2 w-56 text-left">${row.nama || '-'}</td>
                <td class="px-6 py-2 w-32 text-center">${row.jenkel || '-'}</td>
                <td class="px-6 py-2 w-36 text-left">${tanggalLahir}</td>
                <td class="px-6 py-2 w-32 text-center">${row.rt || '-'}/${row.rw || '-'}</td>
                <td class="px-6 py-2 w-44 text-left">${row.hp || '-'}</td>
                <td class="px-6 py-2 w-32 text-center">
                  <button class="editBtn px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500" data-id='${JSON.stringify(row)}'><i class='bx bx-edit'></i></button>
                  <button class="deleteBtn px-2 py-1 bg-red-500 text-white rounded ml-2 hover:bg-red-600" data-id="${row.id_warga}"><i class='bx bx-trash'></i></button>
                </td>
              </tr>`;
            } catch (rowError) {
              console.error(`Error processing row ${idx}:`, rowError, row);
            }
          });
          $('#dataBody').html(html);
          console.log('Data loaded successfully');
        } catch (e) {
          console.error('Error parsing data:', e);
          console.error('Raw data that caused error:', data);
          $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error loading data: ' + e.message + '</td></tr>');
        }
      }).fail(function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
        console.error('Response text:', xhr.responseText);
        console.error('Status code:', xhr.status);
        $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error loading data: ' + error + ' (Status: ' + xhr.status + ')</td></tr>');
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

      // Validasi real-time untuk tanggal lahir
      $('#tgl_lahir').on('input', function() {
        const value = $(this).val();
        
        // Hanya izinkan angka dan tanda strip
        const cleanValue = value.replace(/[^\d-]/g, '');
        if (cleanValue !== value) {
          $(this).val(cleanValue);
        }
        
        // Validasi format DD-MM-YYYY
        if (/^\d{1,2}-\d{1,2}-\d{4}$/.test(value)) {
          const parts = value.split('-');
          const day = parseInt(parts[0]);
          const month = parseInt(parts[1]);
          const year = parseInt(parts[2]);
          
          // Validasi tanggal
          if (day >= 1 && day <= 31 && month >= 1 && month <= 12 && year >= 1900 && year <= 2100) {
            $(this).removeClass('border-red-500').addClass('border-green-500');
          } else {
            $(this).removeClass('border-green-500').addClass('border-red-500');
          }
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
        
        // Buat object data dengan nama wilayah, bukan ID
        const formDataObj = {};
        $(this).serializeArray().forEach(item => {
          formDataObj[item.name] = item.value;
        });
        
        // Ganti nilai ID wilayah dengan nama wilayah
        formDataObj.propinsi = propinsi_nama;
        formDataObj.kota = kota_nama;
        formDataObj.kecamatan = kecamatan_nama;
        formDataObj.kelurahan = kelurahan_nama;
        
        // Konversi tanggal lahir dari DD-MM-YYYY ke YYYY-MM-DD
        if (formDataObj.tgl_lahir) {
          formDataObj.tgl_lahir = processExcelDate(formDataObj.tgl_lahir);
        }
        
        $.post('api/warga_action.php', formDataObj, function(res) {
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
        
        // Set nilai form (kecuali field wilayah)
        for (const key in data) {
          // Skip field wilayah karena akan dihandle secara terpisah
          if (["propinsi", "kota", "kecamatan", "kelurahan"].includes(key)) {
            continue;
          }
          // Khusus untuk tanggal lahir, konversi ke format DD-MM-YYYY untuk input
          if (key === "tgl_lahir") {
            $(`#${key}`).val(formatDateForDisplay(data[key]));
          } else {
            $(`#${key}`).val(data[key]);
          }
        }
        // Set dropdown tanggal lahir
        setDropdownTanggalLahir(formatDateForDisplay(data.tgl_lahir));
        // Set dropdown RT/RW
        setDropdownRTRW(data.rt, data.rw);
        
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
            
            // Set provinsi berdasarkan nama
            if (setDropdownValue('#propinsi', data.propinsi) && data.kota) {
              // Load kota
              loadKota($('#propinsi').val());
              
              // Tunggu kota ter-load
              await waitForDropdown('#kota');
              
              // Set kota berdasarkan nama
              if (setDropdownValue('#kota', data.kota) && data.kecamatan) {
                // Load kecamatan
                loadKecamatan($('#kota').val());
                
                // Tunggu kecamatan ter-load
                await waitForDropdown('#kecamatan');
                
                // Set kecamatan berdasarkan nama
                if (setDropdownValue('#kecamatan', data.kecamatan) && data.kelurahan) {
                  // Load kelurahan
                  loadKelurahan($('#kecamatan').val());
                  
                  // Tunggu kelurahan ter-load
                  await waitForDropdown('#kelurahan');
                  
                  // Set kelurahan berdasarkan nama
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
        // Tampilkan loading untuk export
        $('#loadingModal').removeClass('hidden').addClass('modal-show');
        $('#loadingText').text('Sedang mempersiapkan data untuk export...');
        $('#progressBar').css('width', '30%');
        $('#progressText').text('30% selesai');
        
        $.post('api/warga_action.php', { action: 'read' }, function(data) {
          $('#loadingText').text('Sedang memproses data...');
          $('#progressBar').css('width', '60%');
          $('#progressText').text('60% selesai');
          
          try {
            const warga = JSON.parse(data);
            if (!warga.length) {
              $('#loadingModal').removeClass('modal-show').addClass('hidden');
              alert('Tidak ada data untuk diexport!');
              return;
            }
            
            $('#loadingText').text('Sedang membuat file Excel...');
            $('#progressBar').css('width', '80%');
            $('#progressText').text('80% selesai');
            
            // Header hanya field wilayah tanpa _nama
            const header = [
              'nama', 'nik', 'nikk', 'hubungan', 'jenkel', 'tpt_lahir', 'tgl_lahir', 'alamat', 'rt', 'rw',
              'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'agama', 'status', 'pekerjaan', 'foto', 'hp'
            ];
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
            
            $('#loadingText').text('Sedang mengunduh file...');
            $('#progressBar').css('width', '100%');
            $('#progressText').text('100% selesai');
            
            // Tunggu sebentar sebelum download
            setTimeout(() => {
              XLSX.writeFile(wb, 'data_warga.xlsx');
              $('#loadingModal').removeClass('modal-show').addClass('hidden');
            }, 500);
            
          } catch (e) {
            $('#loadingModal').removeClass('modal-show').addClass('hidden');
            alert('Gagal export: ' + e);
          }
        }).fail(function(xhr, status, error) {
          $('#loadingModal').removeClass('modal-show').addClass('hidden');
          alert('Error loading data: ' + error);
        });
      });

      // Fungsi untuk memproses tanggal dari Excel
      function processExcelDate(dateValue) {
        if (!dateValue) return '';
        
        console.log(`Processing date: "${dateValue}" (type: ${typeof dateValue})`);
        
        // Jika sudah dalam format YYYY-MM-DD, return as is
        if (typeof dateValue === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
          console.log(`Already in YYYY-MM-DD format: ${dateValue}`);
          return dateValue;
        }
        
        // Jika berupa number (Excel date serial number)
        if (typeof dateValue === 'number') {
          console.log(`Excel serial number: ${dateValue}`);
          // Excel date serial number dimulai dari 1 Januari 1900
          const excelEpoch = new Date(1900, 0, 1);
          const date = new Date(excelEpoch.getTime() + (dateValue - 1) * 24 * 60 * 60 * 1000);
          const result = date.toISOString().split('T')[0];
          console.log(`Converted from serial number: ${result}`);
          return result;
        }
        
        // Jika berupa string dengan format lain, coba parse
        if (typeof dateValue === 'string') {
          // Prioritas untuk format DD-MM-YYYY (format yang diinginkan user)
          if (/^\d{1,2}-\d{1,2}-\d{4}$/.test(dateValue)) {
            console.log(`DD-MM-YYYY format detected: ${dateValue}`);
            const parts = dateValue.split('-');
            const day = parseInt(parts[0]);
            const month = parseInt(parts[1]);
            const year = parseInt(parts[2]);
            
            // Validasi tanggal
            if (day >= 1 && day <= 31 && month >= 1 && month <= 12 && year >= 1900 && year <= 2100) {
              const result = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
              console.log(`Converted DD-MM-YYYY to YYYY-MM-DD: ${result}`);
              return result;
            } else {
              console.log(`Invalid date parts: day=${day}, month=${month}, year=${year}`);
            }
          }
          
          // Coba parse dengan Date constructor untuk format lain
          const date = new Date(dateValue);
          if (!isNaN(date.getTime())) {
            const result = date.toISOString().split('T')[0];
            console.log(`Parsed with Date constructor: ${dateValue} -> ${result}`);
            return result;
          }
          
          // Format lain sebagai fallback
          const dateFormats = [
            /^\d{1,2}\/\d{1,2}\/\d{4}$/, // DD/MM/YYYY atau MM/DD/YYYY
            /^\d{4}\/\d{1,2}\/\d{1,2}$/, // YYYY/MM/DD
            /^\d{1,2}\/\d{1,2}\/\d{2}$/, // DD/MM/YY atau MM/DD/YY
            /^\d{1,2}-\d{1,2}-\d{2}$/ // DD-MM-YY atau MM-DD-YY
          ];
          
          for (let format of dateFormats) {
            if (format.test(dateValue)) {
              const date = new Date(dateValue);
              if (!isNaN(date.getTime())) {
                const result = date.toISOString().split('T')[0];
                console.log(`Matched format ${format}: ${dateValue} -> ${result}`);
                return result;
              }
            }
          }
        }
        
        // Jika tidak bisa diparse, return empty string
        console.log(`Could not parse date: ${dateValue}`);
        return '';
      }

      // Fungsi untuk mengkonversi format tanggal dari YYYY-MM-DD ke DD-MM-YYYY untuk display
      function formatDateForDisplay(dateString) {
        if (!dateString || dateString === '0000-00-00') return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        
        return `${day}-${month}-${year}`;
      }

      // Import dari Excel (tanpa id_warga dan tgl_warga)
      $('#importInput').change(function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Tampilkan modal loading
        $('#loadingModal').removeClass('hidden').addClass('modal-show');
        $('#loadingText').text('Sedang membaca file Excel...');
        $('#progressBar').css('width', '10%');
        $('#progressText').text('10% selesai');
        
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#loadingText').text('Sedang memproses data...');
          $('#progressBar').css('width', '20%');
          $('#progressText').text('20% selesai');
          
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, { type: 'array' });
          const sheetName = workbook.SheetNames[0];
          const worksheet = workbook.Sheets[sheetName];
          const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
          
          if (json.length < 2) {
            $('#loadingModal').removeClass('modal-show').addClass('hidden');
            alert('File kosong atau tidak ada data!');
            return;
          }
          
          $('#loadingText').text('Sedang memvalidasi data...');
          $('#progressBar').css('width', '30%');
          $('#progressText').text('30% selesai');
          
          const header = json[0].filter(h => h !== 'id_warga' && h !== 'tgl_warga');
          console.log('Header yang diproses:', header);
          console.log('Posisi field tgl_lahir:', header.indexOf('tgl_lahir'));
          
          let dataValid = [];
          let errorList = [];
          let nikSet = new Set();
          
          for (let i = 1; i < json.length; i++) {
            const row = json[i];
            if (!row.length) continue;
            
            // Debug: tampilkan data mentah dari Excel
            console.log(`Baris ${i+1} data mentah:`, row);
            
            const dataWarga = { action: 'create' };
            header.forEach((h, idx) => {
              // Khusus untuk field tanggal lahir, gunakan fungsi processExcelDate
              if (h === 'tgl_lahir') {
                const originalValue = row[idx];
                const processedValue = processExcelDate(row[idx]);
                console.log(`Baris ${i+1}, Tanggal lahir: Original="${originalValue}" (${typeof originalValue}), Processed="${processedValue}"`);
                dataWarga[h] = processedValue;
              } else {
                dataWarga[h] = row[idx] || '';
              }
            });
            // Validasi format NIK
            if (!/^\d{16}$/.test(dataWarga['nik'] || '')) {
              errorList.push({ baris: i+1, nama: dataWarga['nama'] || '-', nik: dataWarga['nik'] || '-', error: 'NIK harus 16 digit angka' });
              continue;
            }
            // Validasi duplikat NIK di file
            if (nikSet.has(dataWarga['nik'])) {
              errorList.push({ baris: i+1, nama: dataWarga['nama'] || '-', nik: dataWarga['nik'] || '-', error: 'NIK duplikat di file' });
              continue;
            }
            // Validasi tanggal lahir
            if (dataWarga['tgl_lahir'] && !/^\d{4}-\d{2}-\d{2}$/.test(dataWarga['tgl_lahir'])) {
              console.log(`Error tanggal baris ${i+1}: "${dataWarga['tgl_lahir']}" tidak sesuai format YYYY-MM-DD`);
              errorList.push({ baris: i+1, nama: dataWarga['nama'] || '-', nik: dataWarga['nik'] || '-', error: 'Format tanggal lahir tidak valid (harus DD-MM-YYYY di Excel)' });
              continue;
            }
            console.log(`Baris ${i+1}: Tanggal lahir valid = "${dataWarga['tgl_lahir']}"`);
            nikSet.add(dataWarga['nik']);
            dataValid.push(dataWarga);
          }
          
          // Update progress
          $('#loadingText').text('Sedang mengecek database...');
          $('#progressBar').css('width', '50%');
          $('#progressText').text('50% selesai');
          
          // Tampilkan rekap error sebelum cek ke database
          let info = 'Data valid: ' + dataValid.length + ', Data error: ' + errorList.length;
          if (errorList.length) {
            info += '\n\nDetail error:';
            errorList.forEach(e => {
              info += `\nBaris ${e.baris}: NIK ${e.nik}, Nama ${e.nama} => ${e.error}`;
            });
          }
          
          // Cek NIK ke database jika tidak ada error di file
          if (!errorList.length) {
            // Ambil semua NIK yang valid
            const nikList = dataValid.map(d => d.nik);
            $.ajax({
              url: 'api/warga_action.php',
              type: 'POST',
              data: { action: 'cek_nik', nik_list: JSON.stringify(nikList) },
              dataType: 'json',
              async: false,
              success: function(res) {
                if (res && res.length) {
                  // Tandai baris yang NIK-nya sudah ada di database
                  res.forEach(db => {
                    dataValid.forEach((d, idx) => {
                      if (d.nik === db.nik) {
                        errorList.push({ baris: idx+2, nama: d.nama || '-', nik: d.nik || '-', error: 'NIK sudah terdaftar di database (' + (db.nama || '-') + ')' });
                      }
                    });
                  });
                }
              }
            });
          }
          
          // Jika ada error (baik dari file atau database), tampilkan dan download report error, hentikan proses
          if (errorList.length) {
            $('#loadingModal').removeClass('modal-show').addClass('hidden');
            let infoErr = 'Import dibatalkan! Data error: ' + errorList.length + '\n\nDetail error:';
            errorList.forEach(e => {
              infoErr += `\nBaris ${e.baris}: NIK ${e.nik}, Nama ${e.nama} => ${e.error}`;
            });
            alert(infoErr);
            // Otomatis download report error
            let txt = 'Report Error Import Data Warga\n';
            txt += '==============================\n';
            errorList.forEach(e => {
              txt += `Baris ${e.baris}: NIK ${e.nik}, Nama ${e.nama} => ${e.error}\n`;
            });
            const blob = new Blob([txt], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'report_error_import_warga.txt';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            return;
          }
          
          // Jika tidak ada error, lanjut kirim ke backend
          $('#loadingText').text('Sedang menyimpan data ke database...');
          $('#progressBar').css('width', '70%').addClass('progress-bar-animated');
          $('#progressText').text('70% selesai');
          
          let sukses = 0, gagal = 0;
          let errorBackend = [];
          const totalData = dataValid.length;
          
          for (let i = 0; i < dataValid.length; i++) {
            // Update progress untuk setiap data yang diproses
            const progress = 70 + Math.round((i / totalData) * 25);
            $('#progressBar').css('width', progress + '%');
            $('#progressText').text(progress + '% selesai');
            $('#loadingText').text(`Menyimpan data ${i + 1} dari ${totalData}...`);
            
            $.ajax({
              url: 'api/warga_action.php',
              type: 'POST',
              data: dataValid[i],
              async: false,
              success: function(res) { sukses++; },
              error: function(xhr) {
                gagal++;
                let msg = 'Gagal import';
                try {
                  const res = JSON.parse(xhr.responseText);
                  msg = res.error || xhr.responseText;
                } catch (e) {
                  msg = xhr.responseText;
                }
                errorBackend.push({
                  baris: i+2, // +2 karena header dan index 0
                  nama: dataValid[i]['nama'] || '-',
                  nik: dataValid[i]['nik'] || '-',
                  error: msg
                });
              }
            });
          }
          
          $('#loadingText').text('Menyelesaikan proses...');
          $('#progressBar').css('width', '95%').removeClass('progress-bar-animated');
          $('#progressText').text('95% selesai');
          
          loadData();
          
          $('#loadingText').text('Selesai!');
          $('#progressBar').css('width', '100%');
          $('#progressText').text('100% selesai');
          
          // Tunggu sebentar sebelum menutup modal
          setTimeout(() => {
            $('#loadingModal').removeClass('modal-show').addClass('hidden');
            
            let info2 = 'Import selesai! Sukses: ' + sukses + ', Gagal: ' + gagal;
            if (errorBackend.length) {
              info2 += '\n\nDetail error:';
              errorBackend.forEach(e => {
                info2 += `\nBaris ${e.baris}: NIK ${e.nik}, Nama ${e.nama} => ${e.error}`;
              });
              alert(info2);
              // Otomatis download report error backend
              let txt = 'Report Error Import Data Warga (Backend)\n';
              txt += '==============================\n';
              errorBackend.forEach(e => {
                txt += `Baris ${e.baris}: NIK ${e.nik}, Nama ${e.nama} => ${e.error}\n`;
              });
              const blob = new Blob([txt], { type: 'text/plain' });
              const link = document.createElement('a');
              link.href = URL.createObjectURL(blob);
              link.download = 'report_error_import_warga.txt';
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);
            } else {
              alert(info2);
            }
          }, 1000);
        };
        reader.readAsArrayBuffer(file);
      });

      // Download template Excel
      $('#downloadTemplateBtn').click(function() {
        // Tampilkan loading untuk download template
        $('#loadingModal').removeClass('hidden').addClass('modal-show');
        $('#loadingText').text('Sedang membuat template Excel...');
        $('#progressBar').css('width', '50%');
        $('#progressText').text('50% selesai');
        
        // Header hanya field wilayah tanpa _nama
        const header = [
          'nama', 'nik', 'nikk', 'hubungan', 'jenkel', 'tpt_lahir', 'tgl_lahir', 'alamat', 'rt', 'rw',
          'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'agama', 'status', 'pekerjaan', 'foto', 'hp'
        ];
        // Contoh data samaran
        const contoh = [
          'Siti Mawar', '3210987654321098', '3210123456789012', 'Istri', 'P', 'Salatiga', '12-05-1992', 'Jl. Kenanga No. 5', '03', '04',
          'Kelurahan Melati', 'Kecamatan Sukajadi', 'Kota Salatiga', 'Jawa Tengah', 'Indonesia', 'Islam', 'Kawin', 'Ibu Rumah Tangga', '', '082112223333'
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
        
        $('#loadingText').text('Sedang mengunduh template...');
        $('#progressBar').css('width', '100%');
        $('#progressText').text('100% selesai');
        
        // Tunggu sebentar sebelum download
        setTimeout(() => {
          XLSX.writeFile(wb, 'template_warga.xlsx');
          $('#loadingModal').removeClass('modal-show').addClass('hidden');
        }, 500);
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

      // --- Dropdown tanggal lahir ---
      function isiDropdownTanggalLahir(selected) {
        // Hari
        let hari = '';
        for (let i = 1; i <= 31; i++) hari += `<option value="${i.toString().padStart(2,'0')}"${selected && selected.hari==i.toString().padStart(2,'0')?' selected':''}>${i}</option>`;
        $('#tgl_hari').html('<option value="">Hari</option>'+hari);
        // Bulan
        const bulanArr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        let bulan = '';
        for (let i = 1; i <= 12; i++) bulan += `<option value="${i.toString().padStart(2,'0')}"${selected && selected.bulan==i.toString().padStart(2,'0')?' selected':''}>${bulanArr[i-1]}</option>`;
        $('#tgl_bulan').html('<option value="">Bulan</option>'+bulan);
        // Tahun
        let tahun = '';
        const now = new Date().getFullYear();
        for (let i = now; i >= 1900; i--) tahun += `<option value="${i}"${selected && selected.tahun==i?' selected':''}>${i}</option>`;
        $('#tgl_tahun').html('<option value="">Tahun</option>'+tahun);
      }
      // Gabungkan ke hidden field
      function updateTglLahirHidden() {
        const h = $('#tgl_hari').val(), b = $('#tgl_bulan').val(), t = $('#tgl_tahun').val();
        if(h && b && t) $('#tgl_lahir').val(`${h}-${b}-${t}`);
        else $('#tgl_lahir').val('');
      }
      // Inisialisasi saat load
      isiDropdownTanggalLahir();
      $('#tgl_hari,#tgl_bulan,#tgl_tahun').on('change', updateTglLahirHidden);
      // Saat buka modal tambah/reset
      $('#btnTambah, #btnReset').on('click', function(){
        isiDropdownTanggalLahir();
        $('#tgl_lahir').val('');
      });
      // Saat edit data, isi dropdown sesuai tanggal
      function setDropdownTanggalLahir(tgl) {
        if(!tgl) { isiDropdownTanggalLahir(); return; }
        const [h,b,t] = tgl.split('-');
        isiDropdownTanggalLahir({hari:h, bulan:b, tahun:t});
        $('#tgl_lahir').val(tgl);
      }

      // --- Dropdown RT dan RW ---
      function isiDropdownRTRW(selectedRT, selectedRW) {
        let opsi = '<option value="">Pilih</option>';
        for (let i = 1; i <= 999; i++) {
          const val = i.toString().padStart(3, '0');
          opsi += `<option value="${val}"${selectedRT==val?' selected':''}>${val}</option>`;
        }
        $('#rt').html(opsi);
        opsi = '<option value="">Pilih</option>';
        for (let i = 1; i <= 999; i++) {
          const val = i.toString().padStart(3, '0');
          opsi += `<option value="${val}"${selectedRW==val?' selected':''}>${val}</option>`;
        }
        $('#rw').html(opsi);
      }
      // Inisialisasi saat load
      isiDropdownRTRW();
      // Saat tambah/reset
      $('#btnTambah, #btnReset').on('click', function(){
        isiDropdownRTRW();
      });
      // Saat edit data, isi RT/RW
      function setDropdownRTRW(rt, rw) {
        isiDropdownRTRW(rt, rw);
      }

      // Panggil setDropdownRTRW(data.rt, data.rw) di bagian edit data
      
      // Event handler untuk modal biodata dan KK
      $('#modalBiodata, #modalKK').click(function(e) {
        if (e.target === this) {
          $(this).removeClass('modal-show').addClass('hidden');
        }
      });
      
      // Tutup modal dengan tombol ESC
      $(document).keydown(function(e) {
        if (e.key === 'Escape') {
          if (!$('#modalBiodata').hasClass('hidden')) {
            closeModalBiodata();
          }
          if (!$('#modalKK').hasClass('hidden')) {
            closeModalKK();
          }
        }
      });
    });
  </script>
