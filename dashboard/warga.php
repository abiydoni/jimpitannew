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
        <div class="mb-2 flex justify-between items-center">
          <input type="text" id="searchInput" class="border px-2 py-1 rounded text-xs w-64" placeholder="Cari nama/NIK/Alamat/Tgl Lahir (DD-MM-YYYY)...">
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
                        <input type="text" name="tgl_lahir" id="tgl_lahir" class="w-full border px-2 py-0.5 rounded text-sm form-input" placeholder="DD-MM-YYYY" required>
                        <small class="text-gray-500 text-xs">Format: DD-MM-YYYY (contoh: 15-08-1990)</small>
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
                        <input type="text" name="propinsi" id="propinsi" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Kota/Kabupaten *</label>
                        <input type="text" name="kota" id="kota" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Kecamatan *</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Kelurahan *</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="w-full border px-2 py-0.5 rounded text-sm form-input" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">Negara *</label>
                        <input type="text" name="negara" id="negara" class="w-full border px-2 py-0.5 rounded text-sm form-input" value="Indonesia" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-0.5">No HP</label>
                        <input type="text" name="hp" id="hp" class="w-full border px-2 py-0.5 rounded text-sm form-input">
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
        <td class="px-3 py-1 w-40 text-left">${row.nik || '-'}</td>
        <td class="px-3 py-1 w-40 text-left">${row.nikk || '-'}</td>
        <td class="px-3 py-1 w-56 text-left">${row.nama || '-'}</td>
        <td class="px-3 py-1 w-32 text-center">${row.jenkel || '-'}</td>
        <td class="px-3 py-1 w-36 text-left">${tanggalLahir}</td>
        <td class="px-3 py-1 w-32 text-center">${row.rt || '-'}/${row.rw || '-'}</td>
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
function filterWarga(keyword) {
  keyword = keyword.toLowerCase();
  
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
    
    return namaMatch || nikMatch || alamatMatch || tglLahirMatch;
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
        <td>${row.rt || '-'}/${row.rw || '-'}</td>
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

// Event handlers
$(document).ready(function() {
  console.log('Document ready - Loading data...');
  loadData();
  
  // Search input dengan pencarian tanggal lahir yang diperbaiki
  $('#searchInput').on('input', function() {
    const keyword = $(this).val();
    filteredWarga = filterWarga(keyword);
    currentPage = 1;
    renderTable(filteredWarga, currentPage);
    renderPagination(filteredWarga, currentPage);
  });
  
  // Reset search
  $('#resetSearch').click(function() {
    $('#searchInput').val('');
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
    alert('Export Excel - Fitur ini akan diimplementasikan');
  });
  
  // Download template button
  $('#downloadTemplateBtn').click(function() {
    alert('Download Template - Fitur ini akan diimplementasikan');
  });
  
  // Tambah button
  $('#tambahBtn').click(function() {
    $('#modalTitle').text('Tambah Warga');
    $('#wargaForm')[0].reset();
    $('#formAction').val('create');
    $('#negara').val('Indonesia');
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
    
    const formData = new FormData(this);
    const formDataObj = {};
    formData.forEach((value, key) => {
      formDataObj[key] = value;
    });
    
    $.post('api/warga_action.php', formDataObj, function(res) {
      $('#modal').removeClass('modal-show').addClass('hidden');
      loadData();
      if (res === 'success' || res === 'updated') {
        alert('Data berhasil disimpan!');
      } else {
        alert('Response: ' + res);
      }
    }).fail(function(xhr, status, error) {
      alert('Error: ' + error);
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
      $('#tgl_lahir').val(formatDateForDisplay(data.tgl_lahir));
      $('#agama').val(data.agama);
      $('#status').val(data.status);
      $('#pekerjaan').val(data.pekerjaan);
      $('#alamat').val(data.alamat);
      $('#rt').val(data.rt);
      $('#rw').val(data.rw);
      $('#propinsi').val(data.propinsi);
      $('#kota').val(data.kota);
      $('#kecamatan').val(data.kecamatan);
      $('#kelurahan').val(data.kelurahan);
      $('#negara').val(data.negara);
      $('#hp').val(data.hp);
      
      $('#modal').removeClass('hidden').addClass('modal-show');
    } catch (e) {
      alert('Error parsing data: ' + e.message);
    }
  });
  
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
  
  $.post('api/warga_action.php', { action: 'get_by_nik', nik: nik }, function(data) {
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
              <div class="col-span-2">: ${warga.rt || '-'}/${warga.rw || '-'}</div>
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
      <div class="border-2 border-gray-300 rounded-lg p-4 bg-white">
        <div class="text-center border-b-2 border-gray-300 pb-2 mb-4">
          <h4 class="text-base font-bold text-green-800">KARTU KELUARGA</h4>
          <h4 class="text-sm font-semibold text-gray-700">NIK Kepala Keluarga : ${kepalaKK.nik || '-'}</h4>
        </div>
        
        <div class="mb-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="space-y-1">
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Nama Kepala Keluarga</div>
                <div class="col-span-2">: ${kepalaKK.nama || '-'}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Alamat</div>
                <div class="col-span-2">: ${kepalaKK.alamat || '-'}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">RT/RW</div>
                <div class="col-span-2">: ${kepalaKK.rt || '-'}/${kepalaKK.rw || '-'}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Desa/Kelurahan</div>
                <div class="col-span-2">: ${kepalaKK.kelurahan || '-'}</div>
              </div>
            </div>
            <div class="space-y-1">
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Kecamatan</div>
                <div class="col-span-2">: ${kepalaKK.kecamatan || '-'}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Kabupaten/Kota</div>
                <div class="col-span-2">: ${kepalaKK.kota || '-'}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Provinsi</div>
                <div class="col-span-2">: ${kepalaKK.propinsi || '-'}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="font-semibold">Kode Pos</div>
                <div class="col-span-2">: -</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Daftar Anggota KK -->
        <div class="order">
            <h4 class="text-base font-semibold mb-2 border-b pb-1">Daftar Anggota Keluarga</h4>
            <table class="min-w-full border-collapse border border-gray-200 text-xs">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-center">No</th>
                        <th class="border px-2 py-1 text-left">NIK</th>
                        <th class="border px-2 py-1 text-left">Nama</th>
                        <th class="border px-2 py-1 text-center">Jenis Kelamin</th>
                        <th class="border px-2 py-1 text-left">Tempat Lahir</th>
                        <th class="border px-2 py-1 text-center">Tanggal Lahir</th>
                        <th class="border px-2 py-1 text-left">Agama</th>
                        <th class="border px-2 py-1 text-left">Status</th>
                        <th class="border px-2 py-1 text-left">Pekerjaan</th>
                        <th class="border px-2 py-1 text-left">Hubungan</th>
                    </tr>
                </thead>
                <tbody>
                    ${anggotaHTML}
                </tbody>
            </table>
        </div>
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
          table { width: 100%; border-collapse: collapse; margin-top: 16px; }
          th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
          th { background-color: #f0f0f0; font-weight: bold; }
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
          table { width: 100%; border-collapse: collapse; margin-top: 16px; }
          th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
          th { background-color: #f0f0f0; font-weight: bold; }
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
  $('#propinsi').html('<option value="">Loading provinsi...</option>').prop('disabled', true);
  $.get('api/wilayah.php', { action: 'provinsi' }, function(data) {
    let html = '<option value="">Pilih Provinsi</option>';
    data.forEach(item => {
      html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
    });
    $('#propinsi').html(html).prop('disabled', false);
  }).fail(function(xhr, status, error) {
    console.error('Error loading provinsi:', error);
    $('#propinsi').html('<option value="">Error loading provinsi</option>').prop('disabled', true);
  });
}

// Fungsi untuk memuat data kota berdasarkan provinsi
function loadKota(provinsi_id) {
  if (!provinsi_id) {
    $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true);
    return;
  }
  
  $('#kota').html('<option value="">Loading kota...</option>').prop('disabled', true);
  $.get('api/wilayah.php', { action: 'kota', provinsi_id: provinsi_id }, function(data) {
    let html = '<option value="">Pilih Kota/Kabupaten</option>';
    data.forEach(item => {
      html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
    });
    $('#kota').html(html).prop('disabled', false);
  }).fail(function(xhr, status, error) {
    console.error('Error loading kota:', error);
    $('#kota').html('<option value="">Error loading kota</option>').prop('disabled', true);
  });
}

// Fungsi untuk memuat data kecamatan berdasarkan kota
function loadKecamatan(kota_id) {
  if (!kota_id) {
    $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
    return;
  }
  
  $('#kecamatan').html('<option value="">Loading kecamatan...</option>').prop('disabled', true);
  $.get('api/wilayah.php', { action: 'kecamatan', kota_id: kota_id }, function(data) {
    let html = '<option value="">Pilih Kecamatan</option>';
    data.forEach(item => {
      html += `<option value="${item.id}" data-name="${item.name}">${item.name}</option>`;
    });
    $('#kecamatan').html(html).prop('disabled', false);
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

// Event handler untuk dropdown wilayah
$('#propinsi').change(function() {
  const provinsiId = $(this).val();
  const provinsiName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#propinsi_nama').val(provinsiName);
  loadKota(provinsiId);
  $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
  $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
});

$('#kota').change(function() {
  const kotaId = $(this).val();
  const kotaName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#kota_nama').val(kotaName);
  loadKecamatan(kotaId);
  $('#kelurahan').html('<option value="">Pilih Kelurahan</option>').prop('disabled', true);
});

$('#kecamatan').change(function() {
  const kecamatanId = $(this).val();
  const kecamatanName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#kecamatan_nama').val(kecamatanName);
  loadKelurahan(kecamatanId);
});

$('#kelurahan').change(function() {
  const kelurahanName = $(this).find('option:selected').data('name') || $(this).find('option:selected').text();
  $('#kelurahan_nama').val(kelurahanName);
});

// Load provinsi saat halaman dimuat
loadProvinsi();
</script>

<?php include 'footer.php'; ?> 