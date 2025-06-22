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
  console.log('Document ready - loading data...');
  loadData();
  
  // Search input
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
    alert('Tambah Warga - Fitur ini akan diimplementasikan');
  });
  
  // Edit button
  $(document).on('click', '.editBtn', function() {
    alert('Edit Warga - Fitur ini akan diimplementasikan');
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
</script>

<?php include 'footer.php'; ?> 