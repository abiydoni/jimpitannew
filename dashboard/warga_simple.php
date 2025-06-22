<?php
session_start();
include 'header.php';
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Data Warga (Simple Version)</h3>
            <div class="mb-4 text-center">
                <button id="tambahBtn" class="bg-blue-500 hover:bg-blue-700 text-white p-2 rounded text-sm" title="Tambah Warga">
                    <i class='bx bx-plus text-lg'></i>
                </button>
                <button id="printBtn" class="bg-purple-500 hover:bg-purple-700 text-white p-2 rounded text-sm ml-1" title="Print Data">
                    <i class='bx bx-printer text-lg'></i>
                </button>
            </div>
        </div>
        
        <div id="table-container">
            <table id="wargaTable" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-3">No</th>
                        <th class="py-2 px-3">NIK</th>
                        <th class="py-2 px-3">Nama</th>
                        <th class="py-2 px-3">Jenis Kelamin</th>
                        <th class="py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataBody">
                    <tr><td colspan="5" class="text-center text-gray-500">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Document ready - Simple version');
    
    // Load data
    function loadData() {
        console.log('Loading data...');
        $.post('api/warga_action.php', { action: 'read' }, function(data) {
            console.log('Data received:', data);
            try {
                const warga = JSON.parse(data);
                console.log('Parsed data:', warga);
                
                let html = '';
                if (warga && warga.length > 0) {
                    warga.forEach((row, index) => {
                        html += `<tr class="border-b hover:bg-gray-50">
                            <td class="px-3 py-1 text-center">${index + 1}</td>
                            <td class="px-3 py-1">${row.nik || '-'}</td>
                            <td class="px-3 py-1">${row.nama || '-'}</td>
                            <td class="px-3 py-1 text-center">${row.jenkel || '-'}</td>
                            <td class="px-3 py-1 text-center">
                                <button class="editBtn p-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs" title="Edit">
                                    <i class='bx bx-edit'></i>
                                </button>
                                <button class="deleteBtn p-1 bg-red-500 text-white rounded ml-1 hover:bg-red-600 text-xs" data-id="${row.id_warga}" title="Hapus">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-gray-500">Tidak ada data</td></tr>';
                }
                
                $('#dataBody').html(html);
                console.log('Table updated');
            } catch (e) {
                console.error('Error parsing data:', e);
                $('#dataBody').html('<tr><td colspan="5" class="text-center text-red-500">Error: ' + e.message + '</td></tr>');
            }
        }).fail(function(xhr, status, error) {
            console.error('AJAX failed:', error);
            $('#dataBody').html('<tr><td colspan="5" class="text-center text-red-500">Error: ' + error + '</td></tr>');
        });
    }
    
    // Event handlers
    $('#tambahBtn').click(function() {
        console.log('Tambah button clicked');
        alert('Tambah Warga clicked!');
    });
    
    $('#printBtn').click(function() {
        console.log('Print button clicked');
        alert('Print Data clicked!');
    });
    
    $(document).on('click', '.editBtn', function() {
        console.log('Edit button clicked');
        alert('Edit clicked!');
    });
    
    $(document).on('click', '.deleteBtn', function() {
        console.log('Delete button clicked');
        const id = $(this).data('id');
        if (confirm('Hapus data ini?')) {
            alert('Delete ID: ' + id);
        }
    });
    
    // Load data on page load
    loadData();
});
</script>

<?php include 'footer.php'; ?> 