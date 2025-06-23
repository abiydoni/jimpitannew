<?php
session_start();
include 'header.php';
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Data Warga</h3>
            <div class="mb-4 text-center">
                <button id="tambahBtn" class="bg-blue-500 hover:bg-blue-700 text-white p-2 rounded text-sm" title="Tambah Warga">
                    <i class='bx bx-plus text-lg'></i>
                </button>
                <button id="printBtn" class="bg-purple-500 hover:bg-purple-700 text-white p-2 rounded text-sm ml-1" title="Print Data">
                    <i class='bx bx-printer text-lg'></i>
                </button>
                <button id="exportBtn" class="bg-green-500 hover:bg-green-700 text-white p-2 rounded text-sm ml-1" title="Export Excel">
                    <i class='bx bx-export text-lg'></i>
                </button>
                <button id="downloadTemplateBtn" class="bg-gray-500 hover:bg-gray-700 text-white p-2 rounded text-sm ml-1" title="Download Template">
                    <i class='bx bx-download text-lg'></i>
                </button>
                <label for="importInput" class="bg-yellow-500 hover:bg-yellow-700 text-white p-2 rounded text-sm ml-1 cursor-pointer" title="Import Excel">
                    <i class='bx bx-import text-lg'></i>
                    <input type="file" id="importInput" accept=".xlsx,.xls" class="hidden" />
                </label>
            </div>
        </div>
        
        <!-- Search dan Reset -->
        <div class="mb-2 flex flex-wrap gap-2 items-center">
          <input type="text" id="searchInput" class="border px-2 py-1 rounded text-xs w-48" placeholder="Cari nama/NIK/Alamat/Tgl Lahir (DD-MM-YYYY)...">
          <input type="text" id="nikkInput" class="border px-2 py-1 rounded text-xs w-32" placeholder="NIKK">
          <select id="jenkelInput" class="border px-2 py-1 rounded text-xs w-24">
            <option value="">Semua</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
          <input type="text" id="rtInput" class="border px-2 py-1 rounded text-xs w-16" placeholder="RT">
          <input type="text" id="rwInput" class="border px-2 py-1 rounded text-xs w-16" placeholder="RW">
          <button id="resetSearch" class="bg-gray-400 hover:bg-gray-600 text-white p-1 rounded text-xs" title="Reset Pencarian">
            <i class='bx bx-refresh text-sm'></i>
          </button>
        </div>
        
        <div class="mb-2 text-xs text-gray-600">
          <small>💡 Tips pencarian: Cari berdasarkan nama, NIK, alamat, atau tanggal lahir. Contoh: "Ahmad", "1234567890123456", "Jl. Sudirman", "15-08-1990", "15", "08", "1990", "agustus", "90an"</small>
        </div>
        
        <div id="table-container">
            <table id="wargaTable" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-3 w-10">No</th>
                        <th class="py-2 px-3 w-40 text-left">NIK</th>
                        <th class="py-2 px-3 w-40 text-left">NIK KK</th>
                        <th class="py-2 px-3 w-56 text-left">Nama</th>
                        <th class="py-2 px-3 w-32 text-center">Jenis Kelamin</th>
                        <th class="py-2 px-3 w-36 text-left">Tanggal Lahir</th>
                        <th class="py-2 px-3 w-32 text-center">RT/RW</th>
                        <th class="py-2 px-3 w-44 text-left">No HP</th>
                        <th class="py-2 px-3 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataBody">
                    <tr><td colspan="9" class="text-center text-gray-500">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="flex justify-center items-center gap-1 mt-2 text-xs"></div>
    </div>
</div>

<!-- Modal Biodata -->
<div id="modalBiodata" class="modal-overlay hidden">
    <div class="modal-container bg-white rounded-lg shadow-xl p-4 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div id="biodataContent">
            <!-- Content akan diisi oleh JavaScript -->
        </div>
    </div>
</div>

<!-- Modal KK -->
<div id="modalKK" class="modal-overlay hidden">
    <div class="modal-container bg-white rounded-lg shadow-xl p-4 w-full max-w-6xl max-h-[90vh] overflow-y-auto">
        <div id="kkContent">
            <!-- Content akan diisi oleh JavaScript -->
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="modal-overlay hidden">
    <div class="modal-container bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="text-center">
            <div class="animate-spin border-4 border-blue-500 border-t-transparent rounded-full w-12 h-12 mx-auto mb-4"></div>
            <h3 id="loadingText" class="text-lg font-semibold mb-2">Loading...</h3>
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div id="progressBar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="progressText" class="text-sm text-gray-600">0% selesai</p>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="modal" class="modal-overlay hidden">
    <div class="modal-container bg-white rounded-lg shadow-xl p-4 w-full max-w-xs max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b pb-2 mb-4">
            <h2 id="modalTitle" class="text-lg font-bold">Tambah Warga</h2>
        </div>
        <form id="wargaForm" class="text-sm">
            <input type="hidden" name="id_warga" id="id_warga">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="foto" id="foto" value="">
            
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
                        <input type="date" name="tgl_lahir" id="tgl_lahir" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
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
                            <option value="IRT">Ibu Rumah Tangga</option>
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
                            <select name="rt" id="rt" class="w-full border px-2 py-0.5 rounded text-sm form-select" required>
                                <option value="">Pilih RT</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-0.5">RW *</label>
                            <select name="rw" id="rw" class="w-full border px-2 py-0.5 rounded text-sm form-select" required>
                                <option value="">Pilih RW</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Provinsi *</label>
                        <select name="propinsi" id="propinsi" class="w-full border px-2 py-0.5 rounded text-sm form-select" required>
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" name="propinsi_nama" id="propinsi_nama">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Kota/Kabupaten *</label>
                        <select name="kota" id="kota" class="w-full border px-2 py-0.5 rounded text-sm form-select" required disabled>
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                        <input type="hidden" name="kota_nama" id="kota_nama">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Kecamatan *</label>
                        <select name="kecamatan" id="kecamatan" class="w-full border px-2 py-0.5 rounded text-sm form-select" required disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" name="kecamatan_nama" id="kecamatan_nama">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Kelurahan *</label>
                        <select name="kelurahan" id="kelurahan" class="w-full border px-2 py-0.5 rounded text-sm form-select" required disabled>
                            <option value="">Pilih Kelurahan</option>
                        </select>
                        <input type="hidden" name="kelurahan_nama" id="kelurahan_nama">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Negara *</label>
                        <input type="text" name="negara" id="negara" class="w-full border px-2 py-0.5 rounded text-sm form-input" value="Indonesia" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">No HP</label>
                        <input type="text" name="hp" id="hp" class="w-full border px-2 py-0.5 rounded text-sm form-input">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Foto</label>
                        <input type="file" name="foto_file" id="foto_file" accept="image/*" class="w-full border px-2 py-0.5 rounded text-sm form-input">
                        <div class="text-xs text-gray-500 mt-1">Maksimal 2MB, minimal 10KB, JPG/PNG/GIF, dimensi 100x100 s/d 1920x1080 px</div>
                        <div id="fotoPreview" class="mt-2"></div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
                <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
let allWarga = [];
let filteredWarga = [];
let currentPage = 1;
const pageSize = 10;

// Fungsi untuk format tanggal
function formatDateForDisplay(dateString) {
  if (!dateString || dateString === '0000-00-00') return '';
  
  // Jika sudah dalam format DD-MM-YYYY, return as is
  if (/^\d{2}-\d{2}-\d{4}$/.test(dateString)) {
    return dateString;
  }
  
  // Jika dalam format YYYY-MM-DD, convert ke DD-MM-YYYY
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
    const parts = dateString.split('-');
    return `${parts[2]}-${parts[1]}-${parts[0]}`;
  }
  
  // Coba parse sebagai Date object
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return '';
  
  const day = date.getDate().toString().padStart(2, '0');
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const year = date.getFullYear();
  
  return `${day}-${month}-${year}`;
}

// Fungsi untuk render tabel
function renderTable(data, page = 1) {
  const start = (page - 1) * pageSize;
  const end = start + pageSize;
  let html = '';
  
  if (!data.length) {
    html = '<tr><td colspan="9" class="text-center text-gray-500">Tidak ditemukan data yang cocok</td></tr>';
  } else {
    data.slice(start, end).forEach((row, idx) => {
      let tanggalLahir = '-';
      if (row.tgl_lahir && row.tgl_lahir !== '0000-00-00') {
        tanggalLahir = formatDateForDisplay(row.tgl_lahir);
      }
      const jsonData = JSON.stringify(row);
      const encodedData = encodeURIComponent(jsonData);
      
      html += `<tr class="border-b hover:bg-gray-50">
        <td class="px-3 py-1 w-10 text-center">${start + idx + 1}</td>
        <td class="px-3 py-1 w-40 text-left">
          <span class="text-blue-600 hover:text-blue-800 cursor-pointer underline" onclick="showBiodata('${row.nik || ''}')">${row.nik || '-'}</span>
        </td>
        <td class="px-3 py-1 w-40 text-left">
          <span class="text-green-600 hover:text-green-800 cursor-pointer underline" onclick="showKK('${row.nikk || ''}')">${row.nikk || '-'}</span>
        </td>
        <td class="px-3 py-1 w-56 text-left">${row.nama || '-'}</td>
        <td class="px-3 py-1 w-32 text-center">${row.jenkel === 'L' ? 'Laki-laki' : row.jenkel === 'P' ? 'Perempuan' : '-'}</td>
        <td class="px-3 py-1 w-36 text-left">${tanggalLahir}</td>
        <td class="px-3 py-1 w-32 text-center">${row.rt ? row.rt.toString().padStart(3, '0') : '-'}/${row.rw ? row.rw.toString().padStart(3, '0') : '-'}</td>
        <td class="px-3 py-1 w-44 text-left">${row.hp || '-'}</td>
        <td class="px-3 py-1 w-32 text-center">
          <button class="editBtn p-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs" data-id="${encodedData}" title="Edit">
            <i class='bx bx-edit'></i>
          </button>
          <button class="deleteBtn p-1 bg-red-500 text-white rounded ml-1 hover:bg-red-600 text-xs" data-id="${row.id_warga}" title="Hapus">
            <i class='bx bx-trash'></i>
          </button>
        </td>
      </tr>`;
    });
  }
  
  $('#dataBody').html(html);
}

// Fungsi untuk render pagination
function renderPagination(data, page = 1) {
  const totalPages = Math.ceil(data.length / pageSize);
  let html = '';
  
  if (totalPages > 1) {
    html += `<button class="px-2 py-1 rounded ${page === 1 ? 'bg-gray-300' : 'bg-gray-500 text-white'}" ${page === 1 ? 'disabled' : ''} onclick="goToPage(1)">&laquo;</button>`;
    html += `<button class="px-2 py-1 rounded ${page === 1 ? 'bg-gray-300' : 'bg-gray-500 text-white'}" ${page === 1 ? 'disabled' : ''} onclick="goToPage(${page - 1})">&lsaquo;</button>`;
    
    for (let i = 1; i <= totalPages; i++) {
      if (i === page || (i <= 2 || i > totalPages - 2 || Math.abs(i - page) <= 1)) {
        html += `<button class="px-2 py-1 rounded ${i === page ? 'bg-blue-500 text-white' : 'bg-gray-200'}" onclick="goToPage(${i})">${i}</button>`;
      } else if (i === 3 && page > 4) {
        html += '<span class="px-2">...</span>';
      } else if (i === totalPages - 2 && page < totalPages - 3) {
        html += '<span class="px-2">...</span>';
      }
    }
    
    html += `<button class="px-2 py-1 rounded ${page === totalPages ? 'bg-gray-300' : 'bg-gray-500 text-white'}" ${page === totalPages ? 'disabled' : ''} onclick="goToPage(${page + 1})">&rsaquo;</button>`;
    html += `<button class="px-2 py-1 rounded ${page === totalPages ? 'bg-gray-300' : 'bg-gray-500 text-white'}" ${page === totalPages ? 'disabled' : ''} onclick="goToPage(${totalPages})">&raquo;</button>`;
  }
  
  $('#pagination').html(html);
}

// Fungsi untuk pindah halaman
window.goToPage = function(page) {
  currentPage = page;
  renderTable(filteredWarga, currentPage);
  renderPagination(filteredWarga, currentPage);
}

// Fungsi untuk filter data
function filterWarga() {
  const keyword = $('#searchInput').val().toLowerCase();
  const nikk = $('#nikkInput').val().toLowerCase();
  const jenkel = $('#jenkelInput').val();
  const rt = $('#rtInput').val();
  const rw = $('#rwInput').val();

  const namaBulan = [
    'januari', 'februari', 'maret', 'april', 'mei', 'juni',
    'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
  ];

  return allWarga.filter(row => {
    const namaMatch = row.nama && row.nama.toLowerCase().includes(keyword);
    const nikMatch = row.nik && row.nik.toLowerCase().includes(keyword);
    const alamatMatch = row.alamat && row.alamat.toLowerCase().includes(keyword);
    let tglLahirMatch = false;
    if (row.tgl_lahir && row.tgl_lahir !== '0000-00-00') {
      const formattedDate = formatDateForDisplay(row.tgl_lahir);
      const dateParts = formattedDate.split('-');
      tglLahirMatch = formattedDate.toLowerCase().includes(keyword);
      if (!tglLahirMatch && dateParts.length === 3) {
        const [day, month, year] = dateParts;
        tglLahirMatch = day.includes(keyword) || month.includes(keyword) || year.includes(keyword);
        if (!tglLahirMatch && namaBulan.includes(keyword)) {
          const bulanIndex = namaBulan.indexOf(keyword) + 1;
          const bulanString = bulanIndex.toString().padStart(2, '0');
          tglLahirMatch = month === bulanString;
        }
        if (!tglLahirMatch && keyword.length === 4 && /^\d{4}$/.test(keyword)) {
          tglLahirMatch = year === keyword;
        }
        if (!tglLahirMatch && keyword.includes('an') && keyword.length >= 3) {
          const tahunPattern = keyword.replace('an', '');
          if (/^\d{2}$/.test(tahunPattern)) {
            tglLahirMatch = year.startsWith(tahunPattern);
          }
        }
      }
    }
    // Filter tambahan
    const nikkMatch = nikk ? (row.nikk && row.nikk.toLowerCase().includes(nikk)) : true;
    const jenkelMatch = jenkel ? (row.jenkel === jenkel) : true;
    const rtMatch = rt ? (row.rt && row.rt.toString().padStart(3, '0') === rt.padStart(3, '0')) : true;
    const rwMatch = rw ? (row.rw && row.rw.toString().padStart(3, '0') === rw.padStart(3, '0')) : true;
    return (namaMatch || nikMatch || alamatMatch || tglLahirMatch)
      && nikkMatch && jenkelMatch && rtMatch && rwMatch;
  });
}

// Fungsi untuk load data
function loadData() {
  $.post('api/warga_action.php', { action: 'read' }, function(data) {
    try {
      const warga = JSON.parse(data);
      allWarga = Array.isArray(warga) ? warga : [];
      filteredWarga = allWarga;
      currentPage = 1;
      renderTable(filteredWarga, currentPage);
      renderPagination(filteredWarga, currentPage);
    } catch (e) {
      $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error loading data: ' + e.message + '</td></tr>');
      $('#pagination').html('');
    }
  }).fail(function(xhr, status, error) {
    $('#dataBody').html('<tr><td colspan="9" class="text-center text-red-500">Error loading data: ' + error + '</td></tr>');
    $('#pagination').html('');
  });
}

// Fungsi untuk print data
function printWargaData() {
  const currentData = filteredWarga.length > 0 ? filteredWarga : allWarga;
  const searchKeyword = $('#searchInput').val();
  
  let printContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Data Warga</title>
      <style>
        @media print {
          @page {
            size: landscape;
            margin: 1cm;
          }
        }
        body {
          font-family: Arial, sans-serif;
          font-size: 10px;
          margin: 0;
          padding: 0;
        }
        .header {
          text-align: center;
          margin-bottom: 20px;
          border-bottom: 2px solid #000;
          padding-bottom: 10px;
        }
        .header h1 {
          margin: 0;
          font-size: 18px;
          font-weight: bold;
        }
        .header p {
          margin: 5px 0 0 0;
          font-size: 12px;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 10px;
        }
        th, td {
          border: 1px solid #000;
          padding: 4px 6px;
          text-align: left;
          font-size: 9px;
        }
        th {
          background-color: #f0f0f0;
          font-weight: bold;
          text-align: center;
        }
        .footer {
          margin-top: 20px;
          text-align: right;
          font-size: 10px;
        }
      </style>
    </head>
    <body>
      <div class="header">
        <h1>DATA WARGA</h1>
        <p>Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}</p>
        ${searchKeyword ? `<p>Hasil Pencarian: "${searchKeyword}"</p>` : ''}
        <p>Total Data: ${currentData.length} warga</p>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>NIK</th>
            <th>NIK KK</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>RT/RW</th>
            <th>No HP</th>
            <th>Alamat</th>
            <th>Agama</th>
            <th>Pekerjaan</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  currentData.forEach((row, index) => {
    let tanggalLahir = '-';
    if (row.tgl_lahir && row.tgl_lahir !== '0000-00-00') {
      tanggalLahir = formatDateForDisplay(row.tgl_lahir);
    }
    
    printContent += `
      <tr>
        <td>${index + 1}</td>
        <td>${row.nik || '-'}</td>
        <td>${row.nikk || '-'}</td>
        <td>${row.nama || '-'}</td>
        <td>${row.jenkel === 'L' ? 'Laki-laki' : row.jenkel === 'P' ? 'Perempuan' : '-'}</td>
        <td>${tanggalLahir}</td>
        <td>${row.rt ? row.rt.toString().padStart(3, '0') : '-'}/${row.rw ? row.rw.toString().padStart(3, '0') : '-'}</td>
        <td>${row.hp || '-'}</td>
        <td>${row.alamat || '-'}</td>
        <td>${row.agama || '-'}</td>
        <td>${row.pekerjaan || '-'}</td>
      </tr>
    `;
  });
  
  printContent += `
        </tbody>
      </table>
      
      <div class="footer">
        <p>Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
      </div>
    </body>
    </html>
  `;
  
  const printWindow = window.open('', '_blank');
  printWindow.document.write(printContent);
  printWindow.document.close();
  
  setTimeout(() => {
    printWindow.print();
    printWindow.close();
  }, 500);
}

// Fungsi untuk menampilkan biodata warga
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

// Fungsi untuk menampilkan data KK
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

// Fungsi untuk menutup modal biodata
function closeModalBiodata() {
  $('#modalBiodata').removeClass('modal-show').addClass('hidden');
}

// Fungsi untuk menutup modal KK
function closeModalKK() {
  $('#modalKK').removeClass('modal-show').addClass('hidden');
}

// Fungsi untuk menampilkan biodata
function displayBiodata(warga) {
  const tanggalLahir = warga.tgl_lahir && warga.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(warga.tgl_lahir) : '-';
  
  // Foto warga
  const fotoHTML = warga.foto && warga.foto !== '' 
    ? `<img src="${warga.foto}" alt="Foto ${warga.nama}" class="w-36 h-44 object-cover border-2 border-gray-300 rounded">`
    : `<div class="w-36 h-44 border-2 border-gray-300 rounded flex items-center justify-center bg-gray-100">
         <i class='bx bx-user text-6xl text-gray-400'></i>
       </div>`;
  
  const html = `
    <div class="flex justify-between items-center mb-4 border-b pb-2">
      <h3 class="text-lg font-bold">Biodata Warga</h3>
      <div class="flex gap-2">
        <button onclick="printBiodata()" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
          <i class='bx bx-printer'></i> Print
        </button>
        <button onclick="closeModalBiodata()" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
          <i class='bx bx-x'></i> Tutup
        </button>
      </div>
    </div>
    
    <div id="biodataPrintArea">
      <!-- Layout KTP Style -->
      <div class="border-2 border-gray-300 rounded-lg p-4 bg-white">
        <!-- Header KTP -->
        <div class="text-center border-b-2 border-gray-300 pb-2 mb-4">
          <h4 class="text-base font-bold text-blue-800">PROVINSI ${warga.propinsi || 'JAWA BARAT'}</h4>
          <h4 class="text-base font-bold text-blue-800">KABUPATEN/KOTA ${warga.kota || 'BANDUNG'}</h4>
          <h4 class="text-sm font-semibold text-gray-700">NIK : ${warga.nik || '-'}</h4>
        </div>
        
        <!-- Content KTP -->
        <div class="flex gap-4">
          <!-- Data KTP -->
          <div class="flex-1 space-y-1 text-xs">
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Nama</div>
              <div class="col-span-2">: ${warga.nama || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Tempat/Tgl Lahir</div>
              <div class="col-span-2">: ${warga.tpt_lahir || '-'}, ${tanggalLahir}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Jenis Kelamin</div>
              <div class="col-span-2">: ${warga.jenkel === 'L' ? 'LAKI-LAKI' : warga.jenkel === 'P' ? 'PEREMPUAN' : '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Alamat</div>
              <div class="col-span-2">: ${warga.alamat || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">RT/RW</div>
              <div class="col-span-2">: ${warga.rt ? warga.rt.toString().padStart(3, '0') : '-'}/${warga.rw ? warga.rw.toString().padStart(3, '0') : '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Kelurahan</div>
              <div class="col-span-2">: ${warga.kelurahan || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Kecamatan</div>
              <div class="col-span-2">: ${warga.kecamatan || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Agama</div>
              <div class="col-span-2">: ${warga.agama || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Status Perkawinan</div>
              <div class="col-span-2">: ${warga.status || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Pekerjaan</div>
              <div class="col-span-2">: ${warga.pekerjaan || '-'}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="font-semibold">Kewarganegaraan</div>
              <div class="col-span-2">: ${warga.negara || 'WNI'}</div>
            </div>
          </div>
          
          <!-- Foto KTP -->
          <div class="flex flex-col items-center">
            ${fotoHTML}
          </div>
        </div>
      </div>
      
      <!-- Data Tambahan -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
          <h4 class="text-base font-semibold border-b pb-1">Data Tambahan</h4>
          <div class="grid grid-cols-1 gap-1 text-xs">
            <div class="flex justify-between py-0.5"><strong>NIK KK:</strong> <span>${warga.nikk || '-'}</span></div>
            <div class="flex justify-between py-0.5"><strong>Hubungan dalam KK:</strong> <span>${warga.hubungan || '-'}</span></div>
            <div class="flex justify-between py-0.5"><strong>No. HP:</strong> <span>${warga.hp || '-'}</span></div>
          </div>
        </div>
        
        <div class="space-y-2">
          <h4 class="text-base font-semibold border-b pb-1">Data Wilayah</h4>
          <div class="grid grid-cols-1 gap-1 text-xs">
            <div class="flex justify-between py-0.5"><strong>Kota/Kabupaten:</strong> <span>${warga.kota || '-'}</span></div>
            <div class="flex justify-between py-0.5"><strong>Provinsi:</strong> <span>${warga.propinsi || '-'}</span></div>
          </div>
        </div>
      </div>
    </div>
  `;
  
  $('#biodataContent').html(html);
}

// Fungsi untuk menampilkan data KK
function displayKK(kk) {
  const kepalaKK = kk.kepala_keluarga;
  const anggotaKK = kk.anggota_keluarga;
  const tanggalLahirKK = kepalaKK.tgl_lahir && kepalaKK.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(kepalaKK.tgl_lahir) : '-';
  
  // Foto kepala keluarga
  const fotoKepalaKK = kepalaKK.foto && kepalaKK.foto !== '' 
    ? `<img src="${kepalaKK.foto}" alt="Foto ${kepalaKK.nama}" class="w-24 h-32 object-cover border-2 border-gray-300 rounded-sm">`
    : `<div class="w-24 h-32 border-2 border-gray-300 rounded-sm flex items-center justify-center bg-gray-100">
         <i class='bx bx-user text-4xl text-gray-400'></i>
       </div>`;
  
  let anggotaHTML = '';
  anggotaKK.forEach((anggota, index) => {
    const tanggalLahirAnggota = anggota.tgl_lahir && anggota.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(anggota.tgl_lahir) : '-';
    anggotaHTML += `
      <tr class="border-b hover:bg-gray-50">
        <td class="px-2 py-1 text-center text-xs">${index + 1}</td>
        <td class="px-2 py-1 text-xs">${anggota.nik || '-'}</td>
        <td class="px-2 py-1 text-xs">${anggota.nama || '-'}</td>
        <td class="px-2 py-1 text-center text-xs">${anggota.jenkel === 'L' ? 'Laki-laki' : anggota.jenkel === 'P' ? 'Perempuan' : '-'}</td>
        <td class="px-2 py-1 text-xs">${anggota.tpt_lahir || '-'}</td>
        <td class="px-2 py-1 text-center text-xs">${tanggalLahirAnggota}</td>
        <td class="px-2 py-1 text-xs">${anggota.agama || '-'}</td>
        <td class="px-2 py-1 text-xs">${anggota.status || '-'}</td>
        <td class="px-2 py-1 text-xs">${anggota.pekerjaan || '-'}</td>
        <td class="px-2 py-1 text-xs">${anggota.hubungan || '-'}</td>
      </tr>
    `;
  });
  
  const html = `
    <div class="flex justify-between items-center mb-4 border-b pb-2">
      <h3 class="text-lg font-bold">Data Kartu Keluarga</h3>
      <div class="flex gap-2">
        <button onclick="printKK()" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
          <i class='bx bx-printer'></i> Print
        </button>
        <button onclick="closeModalKK()" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
          <i class='bx bx-x'></i> Tutup
        </button>
      </div>
    </div>
    
    <div id="kkPrintArea">
      <!-- Header KK -->
      <div class="border-2 border-gray-300 rounded-lg p-4 bg-white mb-4">
        <div class="text-center border-b-2 border-gray-300 pb-2 mb-4">
          <h4 class="text-base font-bold text-green-800">KARTU KELUARGA</h4>
          <h4 class="text-sm font-semibold text-gray-700">NIK KK : ${kepalaKK.nikk || '-'}</h4>
        </div>
        
        <div class="flex gap-4">
          <!-- Data KK -->
          <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <div class="flex justify-between py-0.5"><strong>Nama Kepala Keluarga:</strong> <span>${kepalaKK.nama || '-'}</span></div>
              <div class="flex justify-between py-0.5"><strong>NIK:</strong> <span>${kepalaKK.nik || '-'}</span></div>
              <div class="flex justify-between py-0.5"><strong>Alamat:</strong> <span>${kepalaKK.alamat || '-'}</span></div>
              <div class="flex justify-between py-0.5"><strong>RT/RW:</strong> <span>${kepalaKK.rt ? kepalaKK.rt.toString().padStart(3, '0') : '-'}/${kepalaKK.rw ? kepalaKK.rw.toString().padStart(3, '0') : '-'}</span></div>
            </div>
            <div>
              <div class="flex justify-between py-0.5"><strong>Kelurahan:</strong> <span>${kepalaKK.kelurahan || '-'}</span></div>
              <div class="flex justify-between py-0.5"><strong>Kecamatan:</strong> <span>${kepalaKK.kecamatan || '-'}</span></div>
              <div class="flex justify-between py-0.5"><strong>Kota/Kabupaten:</strong> <span>${kepalaKK.kota || '-'}</span></div>
              <div class="flex justify-between py-0.5"><strong>Provinsi:</strong> <span>${kepalaKK.propinsi || '-'}</span></div>
            </div>
          </div>
          
          <!-- Foto Kepala Keluarga -->
          <div class="flex flex-col items-center">
            ${fotoKepalaKK}
          </div>
        </div>
      </div>
      
      <!-- Tabel Anggota KK -->
      <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300 text-xs">
          <thead class="bg-gray-100">
            <tr>
              <th class="border border-gray-300 px-2 py-1 text-center">No</th>
              <th class="border border-gray-300 px-2 py-1 text-center">NIK</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Nama</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Jenis Kelamin</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Tempat Lahir</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Tanggal Lahir</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Agama</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Status</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Pekerjaan</th>
              <th class="border border-gray-300 px-2 py-1 text-center">Hubungan</th>
            </tr>
          </thead>
          <tbody>
            ${anggotaHTML}
          </tbody>
        </table>
      </div>
      
      <div class="mt-4 text-xs text-gray-600">
        <p><strong>Total Anggota Keluarga:</strong> ${kk.total_anggota} orang</p>
      </div>
    </div>
  `;
  
  $('#kkContent').html(html);
}

// Fungsi untuk print biodata
function printBiodata() {
  const printContent = document.getElementById('biodataPrintArea').innerHTML;
  const printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <html>
      <head>
        <title>Biodata Warga</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .border-2 { border: 2px solid #d1d5db; }
          .border-gray-300 { border-color: #d1d5db; }
          .rounded-lg { border-radius: 8px; }
          .rounded-sm { border-radius: 2px; }
          .p-4 { padding: 16px; }
          .bg-white { background-color: white; }
          .text-center { text-align: center; }
          .border-b-2 { border-bottom: 2px solid #d1d5db; }
          .pb-2 { padding-bottom: 8px; }
          .mb-4 { margin-bottom: 16px; }
          .text-base { font-size: 16px; }
          .font-bold { font-weight: bold; }
          .text-blue-800 { color: #1e40af; }
          .text-sm { font-size: 14px; }
          .font-semibold { font-weight: 600; }
          .text-gray-700 { color: #374151; }
          .space-y-1 > * + * { margin-top: 4px; }
          .text-xs { font-size: 12px; }
          .grid { display: grid; }
          .grid-cols-1 { grid-template-columns: 1fr; }
          .md\\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
          .gap-4 { gap: 16px; }
          .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
          .gap-2 { gap: 8px; }
          .col-span-2 { grid-column: span 2; }
          .font-semibold { font-weight: 600; }
          .border-b { border-bottom: 1px solid #e5e7eb; }
          .pb-1 { padding-bottom: 4px; }
          .mb-2 { margin-bottom: 8px; }
          .flex { display: flex; }
          .flex-col { flex-direction: column; }
          .items-center { align-items: center; }
          .w-36 { width: 144px; }
          .h-44 { height: 176px; }
          .w-24 { width: 96px; }
          .h-32 { height: 128px; }
          .object-cover { object-fit: cover; }
          .bg-gray-100 { background-color: #f3f4f6; }
          .text-gray-400 { color: #9ca3af; }
          .text-6xl { font-size: 60px; }
          .text-4xl { font-size: 36px; }
          @media print {
            .w-36 { width: 144px !important; }
            .h-44 { height: 176px !important; }
            .w-24 { width: 96px !important; }
            .h-32 { height: 128px !important; }
            img { max-width: none !important; }
            .object-cover { object-fit: cover !important; }
          }
        </style>
      </head>
      <body>
        ${printContent}
      </body>
    </html>
  `);
  printWindow.document.close();
  setTimeout(() => {
    printWindow.print();
    printWindow.close();
  }, 500);
}

// Fungsi untuk print KK
function printKK() {
  const printContent = document.getElementById('kkPrintArea').innerHTML;
  const printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <html>
      <head>
        <title>Biodata Kartu Keluarga</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .border-2 { border: 2px solid #d1d5db; }
          .border-gray-300 { border-color: #d1d5db; }
          .rounded-lg { border-radius: 8px; }
          .rounded-sm { border-radius: 2px; }
          .p-4 { padding: 16px; }
          .bg-white { background-color: white; }
          .text-center { text-align: center; }
          .border-b-2 { border-bottom: 2px solid #d1d5db; }
          .pb-2 { padding-bottom: 8px; }
          .mb-4 { margin-bottom: 16px; }
          .text-base { font-size: 16px; }
          .font-bold { font-weight: bold; }
          .text-green-800 { color: #166534; }
          .text-sm { font-size: 14px; }
          .font-semibold { font-weight: 600; }
          .text-gray-700 { color: #374151; }
          .space-y-1 > * + * { margin-top: 4px; }
          .text-xs { font-size: 12px; }
          .grid { display: grid; }
          .grid-cols-1 { grid-template-columns: 1fr; }
          .md\\:grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
          .gap-4 { gap: 16px; }
          .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
          .gap-2 { gap: 8px; }
          .col-span-2 { grid-column: span 2; }
          .font-semibold { font-weight: 600; }
          .border-b { border-bottom: 1px solid #e5e7eb; }
          .pb-1 { padding-bottom: 4px; }
          .mb-2 { margin-bottom: 8px; }
          .flex { display: flex; }
          .flex-col { flex-direction: column; }
          .items-center { align-items: center; }
          .w-36 { width: 144px; }
          .h-44 { height: 176px; }
          .w-24 { width: 96px; }
          .h-32 { height: 128px; }
          .object-cover { object-fit: cover; }
          .bg-gray-100 { background-color: #f3f4f6; }
          .text-gray-400 { color: #9ca3af; }
          .text-6xl { font-size: 60px; }
          .text-4xl { font-size: 36px; }
          table { width: 100%; border-collapse: collapse; margin-top: 16px; }
          th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
          th { background-color: #f0f0f0; font-weight: bold; }
          @media print {
            .w-36 { width: 144px !important; }
            .h-44 { height: 176px !important; }
            .w-24 { width: 96px !important; }
            .h-32 { height: 128px !important; }
            img { max-width: none !important; }
            .object-cover { object-fit: cover !important; }
          }
        </style>
      </head>
      <body>
        ${printContent}
      </body>
    </html>
  `);
  printWindow.document.close();
  setTimeout(() => {
    printWindow.print();
    printWindow.close();
  }, 500);
}

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

// Fungsi untuk memuat data provinsi
function loadProvinsi() {
  return new Promise((resolve, reject) => {
    $('#propinsi').html('<option value="">Loading provinsi...</option>');
    $.get('api/wilayah.php', { action: 'provinsi' }, function(data) {
      let html = '<option value="">Pilih Provinsi</option>';
      data.forEach(item => {
        html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
      });
      $('#propinsi').html(html);
      resolve();
    }).fail(function(xhr, status, error) {
      console.error('Error loading provinsi:', error);
      $('#propinsi').html('<option value="">Error loading provinsi</option>');
      reject(error);
    });
  });
}

// Fungsi untuk memuat data kota berdasarkan provinsi
function loadKota(provinsi_id) {
  return new Promise((resolve, reject) => {
    if (!provinsi_id) {
      $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true);
      $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
      $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
      resolve();
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
      resolve();
    }).fail(function(xhr, status, error) {
      console.error('Error loading kota:', error);
      $('#kota').html('<option value="">Error loading kota</option>').prop('disabled', true);
      reject(error);
    });
  });
}

// Fungsi untuk memuat data kecamatan berdasarkan kota
function loadKecamatan(kota_id) {
  return new Promise((resolve, reject) => {
    if (!kota_id) {
      $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
      $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
      resolve();
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
      resolve();
    }).fail(function(xhr, status, error) {
      console.error('Error loading kecamatan:', error);
      $('#kecamatan').html('<option value="">Error loading kecamatan</option>').prop('disabled', true);
      reject(error);
    });
  });
}

// Fungsi untuk memuat data kelurahan berdasarkan kecamatan
function loadKelurahan(kecamatan_id) {
  return new Promise((resolve, reject) => {
    if (!kecamatan_id) {
      $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
      resolve();
      return;
    }
    
    $('#kelurahan').html('<option value="">Loading kelurahan...</option>').prop('disabled', true);
    $.get('api/wilayah.php', { action: 'kelurahan', kecamatan_id: kecamatan_id }, function(data) {
      let html = '<option value="">Pilih Kelurahan</option>';
      data.forEach(item => {
        html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
      });
      $('#kelurahan').html(html).prop('disabled', false);
      resolve();
    }).fail(function(xhr, status, error) {
      console.error('Error loading kelurahan:', error);
      $('#kelurahan').html('<option value="">Error loading kelurahan</option>').prop('disabled', true);
      reject(error);
    });
  });
}

// Event handler untuk dropdown wilayah
$('#propinsi').change(async function() {
  const provinsiId = $(this).val();
  const provinsiName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#propinsi_nama').val(provinsiName);
  await loadKota(provinsiId);
  $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
  $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
});

$('#kota').change(async function() {
  const kotaId = $(this).val();
  const kotaName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#kota_nama').val(kotaName);
  await loadKecamatan(kotaId);
  $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
});

$('#kecamatan').change(async function() {
  const kecamatanId = $(this).val();
  const kecamatanName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#kecamatan_nama').val(kecamatanName);
  await loadKelurahan(kecamatanId);
});

$('#kelurahan').change(function() {
  const kelurahanName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#kelurahan_nama').val(kelurahanName);
});

// Load provinsi saat halaman dimuat
loadProvinsi();

// Fungsi untuk mengisi dropdown RT dan RW
function loadRTDropdown() {
  let html = '<option value="">Pilih RT</option>';
  for (let i = 1; i <= 999; i++) {
    const rt = i.toString().padStart(3, '0');
    html += `<option value="${rt}">${rt}</option>`;
  }
  $('#rt').html(html);
}

function loadRWDropdown() {
  let html = '<option value="">Pilih RW</option>';
  for (let i = 1; i <= 999; i++) {
    const rw = i.toString().padStart(3, '0');
    html += `<option value="${rw}">${rw}</option>`;
  }
  $('#rw').html(html);
}

// Load RT dan RW dropdown
loadRTDropdown();
loadRWDropdown();

// Event handler untuk preview foto
$('#foto_file').change(function() {
  const file = this.files[0];
  if (file) {
    // Validasi tipe file
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
      alert('Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF');
      this.value = '';
      $('#fotoPreview').html('');
      return;
    }
    
    // Validasi ukuran (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 2MB');
      this.value = '';
      $('#fotoPreview').html('');
      return;
    }
    
    // Validasi ukuran minimum (min 10KB)
    if (file.size < 10 * 1024) {
      alert('Ukuran file terlalu kecil. Minimal 10KB');
      this.value = '';
      $('#fotoPreview').html('');
      return;
    }
    
    // Validasi dimensi gambar
    const img = new Image();
    img.onload = function() {
      const width = this.width;
      const height = this.height;
      
      // Batasi dimensi maksimal (1920x1080)
      if (width > 1920 || height > 1080) {
        alert('Dimensi gambar terlalu besar. Maksimal 1920x1080 pixel');
        $('#foto_file')[0].value = '';
        $('#fotoPreview').html('');
        return;
      }
      
      // Batasi dimensi minimal (100x100)
      if (width < 100 || height < 100) {
        alert('Dimensi gambar terlalu kecil. Minimal 100x100 pixel');
        $('#foto_file')[0].value = '';
        $('#fotoPreview').html('');
        return;
      }
      
      // Jika semua validasi berhasil, tampilkan preview
      const reader = new FileReader();
      reader.onload = function(e) {
        $('#fotoPreview').html(`
          <div class="mt-2">
            <img src="${e.target.result}" alt="Preview Foto" class="w-20 h-20 object-cover rounded border">
            <div class="text-xs text-gray-600 mt-1">
              Ukuran: ${(file.size / 1024).toFixed(1)}KB | Dimensi: ${width}x${height}px
            </div>
          </div>
        `);
      };
      reader.readAsDataURL(file);
    };
    
    img.onerror = function() {
      alert('File bukan gambar yang valid');
      $('#foto_file')[0].value = '';
      $('#fotoPreview').html('');
    };
    
    img.src = URL.createObjectURL(file);
  } else {
    $('#fotoPreview').html('');
  }
});

// Event handler alternatif untuk tombol simpan (jika form submit tidak berfungsi)
$(document).on('click', '#wargaForm button[type="submit"]', function(e) {
  console.log('Submit button clicked'); // Debug
  e.preventDefault();
  $('#wargaForm').submit();
});

// Event handlers
$(document).ready(function() {
  loadData();
  
  // Search input dan filter tambahan
  $('#searchInput, #nikkInput, #jenkelInput, #rtInput, #rwInput').on('input change', function() {
    filteredWarga = filterWarga();
    currentPage = 1;
    renderTable(filteredWarga, currentPage);
    renderPagination(filteredWarga, currentPage);
  });
  
  // Reset search
  $('#resetSearch').click(function() {
    $('#searchInput').val('');
    $('#nikkInput').val('');
    $('#jenkelInput').val('');
    $('#rtInput').val('');
    $('#rwInput').val('');
    filteredWarga = allWarga;
    currentPage = 1;
    renderTable(filteredWarga, currentPage);
    renderPagination(filteredWarga, currentPage);
  });
  
  // Print button
  $('#printBtn').click(function() {
    printWargaData();
  });
  
  // Export button
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
        
        // Header untuk export
        const header = [
          'Nama', 'NIK', 'NIK KK', 'Hubungan', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 
          'Alamat', 'RT', 'RW', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', 'Negara', 
          'Agama', 'Status', 'Pekerjaan', 'No HP'
        ];
        const rows = [header];
        warga.forEach(row => {
          const tanggalLahir = row.tgl_lahir && row.tgl_lahir !== '0000-00-00' ? formatDateForDisplay(row.tgl_lahir) : '';
          const rtFormatted = row.rt ? row.rt.toString().padStart(3, '0') : '';
          const rwFormatted = row.rw ? row.rw.toString().padStart(3, '0') : '';
          rows.push([
            row.nama || '',
            row.nik || '',
            row.nikk || '',
            row.hubungan || '',
            row.jenkel === 'L' ? 'Laki-laki' : row.jenkel === 'P' ? 'Perempuan' : '',
            row.tpt_lahir || '',
            tanggalLahir,
            row.alamat || '',
            rtFormatted,
            rwFormatted,
            row.kelurahan || '',
            row.kecamatan || '',
            row.kota || '',
            row.propinsi || '',
            row.negara || '',
            row.agama || '',
            row.status || '',
            row.pekerjaan || '',
            row.hp || ''
          ]);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(rows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Data Warga');
        
        $('#loadingText').text('Sedang mengunduh file...');
        $('#progressBar').css('width', '100%');
        $('#progressText').text('100% selesai');
        
        // Download file
        XLSX.writeFile(wb, `Data_Warga_${new Date().toISOString().split('T')[0]}.xlsx`);
        
        setTimeout(() => {
          $('#loadingModal').removeClass('modal-show').addClass('hidden');
          alert('Export berhasil!');
        }, 1000);
        
      } catch (e) {
        $('#loadingModal').removeClass('modal-show').addClass('hidden');
        alert('Error saat export: ' + e.message);
      }
    }).fail(function(xhr, status, error) {
      $('#loadingModal').removeClass('modal-show').addClass('hidden');
      alert('Error: ' + error);
    });
  });
  
  // Download template button
  $('#downloadTemplateBtn').click(function() {
    const header = [
      'Nama', 'NIK', 'NIK KK', 'Hubungan', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 
      'Alamat', 'RT', 'RW', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', 'Negara', 
      'Agama', 'Status', 'Pekerjaan', 'No HP'
    ];
    
    const templateData = [
      header,
      ['Contoh Nama', '1234567890123456', '1234567890123456', 'Kepala Keluarga', 'L', 'Jakarta', '15-08-1990', 
       'Jl. Contoh No. 123', '001', '001', 'Contoh Kelurahan', 'Contoh Kecamatan', 'Contoh Kota', 'Contoh Provinsi', 'Indonesia', 
       'Islam', 'Kawin', 'PNS', '081234567890']
    ];
    
    const ws = XLSX.utils.aoa_to_sheet(templateData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Template');
    
    XLSX.writeFile(wb, 'Template_Data_Warga.xlsx');
  });
  
  // Import Excel
  $('#importInput').change(function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    if (!file.name.match(/\.(xlsx|xls)$/)) {
      alert('File harus berformat Excel (.xlsx atau .xls)');
      return;
    }
    
    // Tampilkan loading modal
    $('#loadingModal').removeClass('hidden').addClass('modal-show');
    $('#loadingText').text('Sedang membaca file Excel...');
    $('#progressBar').css('width', '20%');
    $('#progressText').text('20% selesai');
    
    const reader = new FileReader();
    reader.onload = function(e) {
      try {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
        
        if (jsonData.length < 2) {
          $('#loadingModal').removeClass('modal-show').addClass('hidden');
          alert('File Excel kosong atau tidak memiliki data!');
          return;
        }
        
        const headers = jsonData[0];
        const rows = jsonData.slice(1);
        
        $('#loadingText').text('Sedang memvalidasi data...');
        $('#progressBar').css('width', '40%');
        $('#progressText').text('40% selesai');
        
        // Validasi data
        let validData = [];
        let errors = [];
        
        rows.forEach((row, index) => {
          if (row.length < headers.length) {
            errors.push(`Baris ${index + 2}: Data tidak lengkap`);
            return;
          }
          
          const rowData = {};
          headers.forEach((header, colIndex) => {
            rowData[header.toLowerCase().replace(/\s+/g, '_')] = row[colIndex] || '';
          });
          
          // Validasi NIK
          if (!rowData.nik || !/^\d{16}$/.test(rowData.nik)) {
            errors.push(`Baris ${index + 2}: NIK harus 16 digit angka`);
            return;
          }
          
          // Validasi NIK KK
          if (!rowData.nik_kk || !/^\d{16}$/.test(rowData.nik_kk)) {
            errors.push(`Baris ${index + 2}: NIK KK harus 16 digit angka`);
            return;
          }
          
          validData.push(rowData);
        });
        
        if (errors.length > 0) {
          $('#loadingModal').removeClass('modal-show').addClass('hidden');
          alert('Error validasi:\n' + errors.join('\n'));
          return;
        }
        
        $('#loadingText').text('Sedang menyimpan data...');
        $('#progressBar').css('width', '60%');
        $('#progressText').text('60% selesai');
        
        // Kirim data ke server
        $.post('api/warga_action.php', { 
          action: 'import_excel', 
          data: JSON.stringify(validData) 
        }, function(response) {
          $('#loadingText').text('Import selesai!');
          $('#progressBar').css('width', '100%');
          $('#progressText').text('100% selesai');
          
          setTimeout(() => {
            $('#loadingModal').removeClass('modal-show').addClass('hidden');
            
            try {
              const result = JSON.parse(response);
              let message = `Import selesai!\nBerhasil: ${result.success_count} data\nGagal: ${result.error_count} data`;
              
              if (result.errors && result.errors.length > 0) {
                message += '\n\nError:\n' + result.errors.slice(0, 5).join('\n');
                if (result.errors.length > 5) {
                  message += `\n...dan ${result.errors.length - 5} error lainnya`;
                }
              }
              
              alert(message);
            } catch (e) {
              alert('Import berhasil! ' + response);
            }
            
            loadData(); // Reload data
            $('#importInput').val(''); // Reset input file
          }, 1000);
          
        }).fail(function(xhr, status, error) {
          $('#loadingModal').removeClass('modal-show').addClass('hidden');
          alert('Error saat import: ' + error);
        });
        
      } catch (e) {
        $('#loadingModal').removeClass('modal-show').addClass('hidden');
        alert('Error membaca file: ' + e.message);
      }
    };
    
    reader.readAsArrayBuffer(file);
  });
  
  // Tambah button
  $('#tambahBtn').click(function() {
    $('#modalTitle').text('Tambah Warga');
    $('#wargaForm')[0].reset();
    $('#formAction').val('create');
    $('#negara').val('Indonesia');
    
    // Reset dan disable dropdown wilayah
    $('#propinsi').val('').prop('disabled', false);
    $('#propinsi_nama').val('');
    $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true);
    $('#kota_nama').val('');
    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
    $('#kecamatan_nama').val('');
    $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
    $('#kelurahan_nama').val('');
    
    // Reload RT dan RW dropdown
    loadRTDropdown();
    loadRWDropdown();
    
    $('#modal').removeClass('hidden').addClass('modal-show');
  });
  
  // Cancel button
  $('#cancelBtn').click(function() {
    $('#modal').removeClass('modal-show').addClass('hidden');
  });
  
  // Close modal when clicking outside
  $('#modal').click(function(e) {
    if (e.target === this) {
      $('#modal').removeClass('modal-show').addClass('hidden');
    }
  });
  
  // Form submit
  $('#wargaForm').submit(function(e) {
    e.preventDefault();
    
    // Validasi form
    if (!this.checkValidity()) {
      this.reportValidity();
      return;
    }
    
    // Ambil nama wilayah dari hidden input
    const propinsiNama = $('#propinsi_nama').val() || $('#propinsi option:selected').text();
    const kotaNama = $('#kota_nama').val() || $('#kota option:selected').text();
    const kecamatanNama = $('#kecamatan_nama').val() || $('#kecamatan option:selected').text();
    const kelurahanNama = $('#kelurahan_nama').val() || $('#kelurahan option:selected').text();
    
    // Format RT dan RW ke 3 digit
    const rt = $('#rt').val() ? $('#rt').val().toString().padStart(3, '0') : '';
    const rw = $('#rw').val() ? $('#rw').val().toString().padStart(3, '0') : '';
    
    // Buat FormData untuk menangani file upload
    const formData = new FormData();
    
    // Tambahkan semua field ke FormData
    formData.append('action', $('#formAction').val());
    formData.append('id_warga', $('#id_warga').val());
    formData.append('nama', $('#nama').val());
    formData.append('nik', $('#nik').val());
    formData.append('nikk', $('#nikk').val());
    formData.append('hubungan', $('#hubungan').val());
    formData.append('jenkel', $('#jenkel').val());
    formData.append('tpt_lahir', $('#tpt_lahir').val());
    formData.append('tgl_lahir', $('#tgl_lahir').val());
    formData.append('agama', $('#agama').val());
    formData.append('status', $('#status').val());
    formData.append('pekerjaan', $('#pekerjaan').val());
    formData.append('alamat', $('#alamat').val());
    formData.append('rt', rt);
    formData.append('rw', rw);
    formData.append('propinsi', propinsiNama);
    formData.append('kota', kotaNama);
    formData.append('kecamatan', kecamatanNama);
    formData.append('kelurahan', kelurahanNama);
    formData.append('negara', $('#negara').val());
    formData.append('hp', $('#hp').val());
    formData.append('foto', $('#foto').val());
    
    // Tambahkan file foto jika ada
    const fotoFile = $('#foto_file')[0].files[0];
    if (fotoFile) {
      formData.append('foto_file', fotoFile);
    }
    
    // Disable tombol submit dan tampilkan loading
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Menyimpan...');
    
    $.ajax({
      url: 'api/warga_action.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        submitBtn.prop('disabled', false).text(originalText);
        $('#modal').removeClass('modal-show').addClass('hidden');
        loadData();
        
        try {
          const result = JSON.parse(res);
          if (result.success) {
            alert('Data berhasil disimpan!');
          } else {
            alert('Error: ' + (result.message || 'Terjadi kesalahan'));
          }
        } catch (e) {
          if (res === 'success' || res === 'updated') {
            alert('Data berhasil disimpan!');
          } else {
            alert('Response: ' + res);
          }
        }
      },
      error: function(xhr, status, error) {
        submitBtn.prop('disabled', false).text(originalText);
        alert('Error: ' + error);
        console.error('AJAX Error:', xhr.responseText);
      }
    });
  });
  
  // Edit button
  $(document).on('click', '.editBtn', function() {
    const encodedData = $(this).data('id');
    try {
      const data = JSON.parse(decodeURIComponent(encodedData));
      
      $('#modalTitle').text('Edit Warga');
      $('#formAction').val('update');
      $('#id_warga').val(data.id_warga);
      $('#nama').val(data.nama);
      $('#nik').val(data.nik);
      $('#nikk').val(data.nikk);
      $('#hubungan').val(data.hubungan);
      $('#jenkel').val(data.jenkel);
      $('#tpt_lahir').val(data.tpt_lahir);
      
      // Format tanggal untuk date input (YYYY-MM-DD)
      if (data.tgl_lahir && data.tgl_lahir !== '0000-00-00') {
        // Jika sudah dalam format YYYY-MM-DD, gunakan langsung
        if (/^\d{4}-\d{2}-\d{2}$/.test(data.tgl_lahir)) {
          $('#tgl_lahir').val(data.tgl_lahir);
        } else {
          // Jika dalam format DD-MM-YYYY, convert ke YYYY-MM-DD
          if (/^\d{2}-\d{2}-\d{4}$/.test(data.tgl_lahir)) {
            const parts = data.tgl_lahir.split('-');
            $('#tgl_lahir').val(`${parts[2]}-${parts[1]}-${parts[0]}`);
          } else {
            // Coba parse sebagai Date object
            const date = new Date(data.tgl_lahir);
            if (!isNaN(date.getTime())) {
              const year = date.getFullYear();
              const month = (date.getMonth() + 1).toString().padStart(2, '0');
              const day = date.getDate().toString().padStart(2, '0');
              $('#tgl_lahir').val(`${year}-${month}-${day}`);
            }
          }
        }
      } else {
        $('#tgl_lahir').val('');
      }
      
      $('#agama').val(data.agama);
      $('#status').val(data.status);
      $('#pekerjaan').val(data.pekerjaan);
      $('#alamat').val(data.alamat);
      
      // Set RT dan RW dengan format 3 digit
      if (data.rt) {
        const rtFormatted = data.rt.toString().padStart(3, '0');
        $('#rt').val(rtFormatted);
      } else {
        $('#rt').val('');
      }
      
      if (data.rw) {
        const rwFormatted = data.rw.toString().padStart(3, '0');
        $('#rw').val(rwFormatted);
      } else {
        $('#rw').val('');
      }
      
      // Set foto jika ada
      if (data.foto) {
        $('#foto').val(data.foto);
        // Tampilkan preview foto jika ada
        if (data.foto && data.foto !== '') {
          $('#fotoPreview').html(`<img src="${data.foto}" alt="Foto Warga" class="w-20 h-20 object-cover rounded border">`);
        } else {
          $('#fotoPreview').html('');
        }
      } else {
        $('#foto').val('');
        $('#fotoPreview').html('');
      }
      
      // Set wilayah - perlu memuat data wilayah terlebih dahulu
      setWilayahForEdit(data);
      
      $('#negara').val(data.negara);
      $('#hp').val(data.hp);
      
      $('#modal').removeClass('hidden').addClass('modal-show');
    } catch (e) {
      alert('Error parsing data: ' + e.message);
    }
  });
  
  // Fungsi untuk set wilayah saat edit
  async function setWilayahForEdit(data) {
    try {
      // Load provinsi terlebih dahulu
      await loadProvinsi();
      
      // Cari dan set provinsi
      let provinsiFound = false;
      $('#propinsi option').each(function() {
        if ($(this).text().toLowerCase() === data.propinsi.toLowerCase()) {
          $('#propinsi').val($(this).val());
          $('#propinsi_nama').val(data.propinsi);
          provinsiFound = true;
          return false; // break loop
        }
      });
      
      if (provinsiFound && $('#propinsi').val()) {
        // Load kota
        await loadKota($('#propinsi').val());
        
        // Cari dan set kota
        let kotaFound = false;
        $('#kota option').each(function() {
          if ($(this).text().toLowerCase() === data.kota.toLowerCase()) {
            $('#kota').val($(this).val());
            $('#kota_nama').val(data.kota);
            kotaFound = true;
            return false; // break loop
          }
        });
        
        if (kotaFound && $('#kota').val()) {
          // Load kecamatan
          await loadKecamatan($('#kota').val());
          
          // Cari dan set kecamatan
          let kecamatanFound = false;
          $('#kecamatan option').each(function() {
            if ($(this).text().toLowerCase() === data.kecamatan.toLowerCase()) {
              $('#kecamatan').val($(this).val());
              $('#kecamatan_nama').val(data.kecamatan);
              kecamatanFound = true;
              return false; // break loop
            }
          });
          
          if (kecamatanFound && $('#kecamatan').val()) {
            // Load kelurahan
            await loadKelurahan($('#kecamatan').val());
            
            // Cari dan set kelurahan
            let kelurahanFound = false;
            $('#kelurahan option').each(function() {
              if ($(this).text().toLowerCase() === data.kelurahan.toLowerCase()) {
                $('#kelurahan').val($(this).val());
                $('#kelurahan_nama').val(data.kelurahan);
                kelurahanFound = true;
                return false; // break loop
              }
            });
            
            if (!kelurahanFound) {
              // Jika tidak ditemukan, set nama saja
              $('#kelurahan').val(data.kelurahan);
              $('#kelurahan_nama').val(data.kelurahan);
            }
          } else {
            // Jika tidak ditemukan, set nama saja
            $('#kecamatan').val(data.kecamatan);
            $('#kecamatan_nama').val(data.kecamatan);
          }
        } else {
          // Jika tidak ditemukan, set nama saja
          $('#kota').val(data.kota);
          $('#kota_nama').val(data.kota);
        }
      } else {
        // Jika tidak ditemukan, set nama saja
        $('#propinsi').val(data.propinsi);
        $('#propinsi_nama').val(data.propinsi);
      }
    } catch (error) {
      console.error('Error setting wilayah for edit:', error);
      // Fallback: set nama saja
      $('#propinsi').val(data.propinsi);
      $('#propinsi_nama').val(data.propinsi);
      $('#kota').val(data.kota);
      $('#kota_nama').val(data.kota);
      $('#kecamatan').val(data.kecamatan);
      $('#kecamatan_nama').val(data.kecamatan);
      $('#kelurahan').val(data.kelurahan);
      $('#kelurahan_nama').val(data.kelurahan);
    }
  }
  
  // Delete button
  $(document).on('click', '.deleteBtn', function() {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
      const id = $(this).data('id');
      $.post('api/warga_action.php', { action: 'delete', id_warga: id }, function(res) {
        if (res === 'deleted') {
          loadData();
          alert('Data berhasil dihapus!');
        } else {
          alert('Gagal menghapus data: ' + res);
        }
      }).fail(function(xhr, status, error) {
        alert('Error: ' + error);
      });
    }
  });
});
</script>

<?php include 'footer.php'; ?> 