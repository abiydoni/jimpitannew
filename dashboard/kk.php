<?php
session_start();
include 'api/db.php';
// Fungsi untuk menghapus data
if (isset($_GET['delete'])) {
    $code_id = $_GET['delete'];
    $sql = "DELETE FROM master_kk WHERE code_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$code_id]);

    header("Location: kk.php");
    exit();
}
include 'header.php';

// Prepare and execute the SQL statement
$sql = "SELECT * FROM master_kk";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
// Include the database connection

?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>DATA KEPALA KELUARGA</h3>
            <div class="mb-4 text-center">
                <button type="button" id="addDataBtn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" onclick="openAddNikkModal()">
                    <i class='bx bx-plus' style="font-size:24px"></i>
                </button>
                <button type="button" id="printSelectedBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class='bx bx-printer' style="font-size:24px"></i>
                </button>
            </div>
        </div>
        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-3 py-2">Nama KK</th>
                    <th class="border px-3 py-2">ID</th>
                    <th class="border px-3 py-2">No KK</th>
                    <th class="border px-3 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($data) {
                        foreach ($data as $row): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td>
                                    <a href="detailkk.php?nama=<?= urlencode($row['kk_name']) ?>" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($row["kk_name"]); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($row["code_id"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nokk"]); ?></td>
                                <td class="flex justify-center space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 font-bold py-1 px-1" data-modal-toggle="editModal" data-id="<?php echo $row['code_id']; ?>" data-name="<?php echo $row['kk_name']; ?>">
                                        <i class='bx bx-edit'></i> <!-- Ikon edit ditambahkan -->
                                    </button>
                                    <a href="kk.php?delete=<?php echo $row['code_id']; ?>" onclick="return confirm('Yakin ingin menghapus data <?php echo $row['kk_name']; ?> ?')" class="text-red-600 hover:text-red-800 font-bold py-1 px-1">
                                        <i class='bx bx-trash'></i> <!-- Ikon hapus ditambahkan -->
                                    </a>
                                    <input type="checkbox" class="print-checkbox">    
                                </td>
                            </tr>
                        <?php endforeach; 
                    } else {
                        echo '<tr><td colspan="3">No data available</td></tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
    <!-- Modal Tambah Data -->
    <!-- (Dihapus, hanya pakai modal addModalNikk) -->
    <!-- Modal Edit Data -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-3 rounded shadow-lg w-full max-w-md">
            <h2 class="text-lg font-bold mb-2">Edit Data Master KK</h2>
            <form action="api/kk_update.php" method="POST">
                <input type="hidden" name="code_id" id="edit_code_id">
                <div class="mb-2">
                    <label class="block mb-1">No KK</label>
                    <input type="text" name="nokk" id="edit_nokk" class="border rounded w-full p-1" required>
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Nama KK</label>
                    <input type="text" name="kk_name" id="edit_kk_name" class="border rounded w-full p-1" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('editModal')">Tutup</button>
                    <input type="submit" class="bg-blue-600 text-white px-3 py-1 rounded" value="Update">
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Tambah Data KK dari Warga (NIKK) -->
    <div id="addModalNikk" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-3 rounded shadow-lg w-full max-w-md">
            <h2 class="text-lg font-bold mb-2">Tambah Data KK dari Warga</h2>
            <form id="formAddNikk" action="#" method="POST">
                <div class="mb-2">
                    <label class="block mb-1">No KK (NIKK)</label>
                    <select id="nikkDropdown" name="nikk" class="border rounded w-full p-1" required></select>
                </div>
                <div class="mb-2">
                    <label class="block mb-1">No KK</label>
                    <input type="text" id="nokkAuto" name="nokk" class="border rounded w-full p-1 bg-gray-100" readonly required>
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Nama KK</label>
                    <input type="text" id="kkNameAuto" name="kk_name" class="border rounded w-full p-1 bg-gray-100" readonly required>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('addModalNikk')">Tutup</button>
                    <input type="submit" class="bg-blue-600 text-white px-3 py-1 rounded" value="Tambah">
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }

        // Script untuk mengisi data modal edit
        document.querySelectorAll('[data-modal-toggle="editModal"]').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit_code_id').value = this.getAttribute('data-id');
                document.getElementById('edit_kk_name').value = this.getAttribute('data-name');
                toggleModal('editModal');
            });
        });

        // Modal Tambah Data KK dari Warga (NIKK)
        function openAddNikkModal() {
            toggleModal('addModalNikk');
            loadNikkDropdown();
        }
        function loadNikkDropdown() {
            console.log('loadNikkDropdown called');
            fetch('api/get_nikk_group.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('nikkDropdown');
                    if ($(select).hasClass('select2-hidden-accessible')) {
                        $(select).select2('destroy');
                    }
                    select.innerHTML = '';
                    // Tambahkan option kosong di awal
                    const emptyOpt = document.createElement('option');
                    emptyOpt.value = '';
                    emptyOpt.textContent = 'Pilih No KK disini...';
                    select.appendChild(emptyOpt);
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.nikk;
                        opt.textContent = item.nikk + ' - ' + item.kk_name;
                        opt.setAttribute('data-nokk', item.nikk);
                        opt.setAttribute('data-kk_name', item.kk_name);
                        select.appendChild(opt);
                    });
                    // Reset value ke kosong
                    select.value = '';
                    document.getElementById('kkNameAuto').value = '';
                    document.getElementById('nokkAuto').value = '';
                    $(select).select2({
                        dropdownParent: $('#addModalNikk'),
                        width: '100%',
                        placeholder: 'Pilih No KK disini...',
                        matcher: function(params, data) {
                            console.log('Matcher called:', params.term, data.text, data.element ? $(data.element).attr('data-kk_name') : '');
                            if ($.trim(params.term) === '') {
                                return data;
                            }
                            if (typeof data.text === 'undefined') {
                                return null;
                            }
                            var term = params.term.toLowerCase();
                            var text = data.text.toLowerCase();
                            var kkName = '';
                            if (data.element) {
                                kkName = $(data.element).attr('data-kk_name') ? $(data.element).attr('data-kk_name').toLowerCase() : '';
                            }
                            if (text.indexOf(term) > -1 || kkName.indexOf(term) > -1) {
                                return data;
                            }
                            return null;
                        }
                    });
                    console.log('Dropdown options:', select.innerHTML);
                    console.log('Select2 status:', typeof $.fn.select2);
                });
        }
        document.addEventListener('DOMContentLoaded', function() {
            var nikkDropdown = document.getElementById('nikkDropdown');
            if (nikkDropdown) {
                nikkDropdown.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const text = selected.textContent.split(' - ');
                    document.getElementById('kkNameAuto').value = text[1] || '';
                    document.getElementById('nokkAuto').value = selected.getAttribute('data-nokk') || '';
                });
            }
            var formAddNikk = document.getElementById('formAddNikk');
            if (formAddNikk) {
                formAddNikk.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const nikk = document.getElementById('nikkDropdown').value;
                    const kk_name = document.getElementById('kkNameAuto').value;
                    const nokk = document.getElementById('nokkAuto').value;
                    fetch('api/kk_insert.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `code_id=${encodeURIComponent(nikk)}&kk_name=${encodeURIComponent(kk_name)}&nokk=${encodeURIComponent(nokk)}`
                    })
                    .then(res => res.text())
                    .then(resp => {
                        alert('Data berhasil ditambah!');
                        location.reload();
                    });
                });
            }
        });
    </script>
