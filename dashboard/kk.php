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

if (isset($_GET['cek_code_id'])) {
    header('Content-Type: application/json');
    $code_id = $_GET['cek_code_id'] ?? '';
    if (!$code_id) {
        echo json_encode(['exists' => false]);
        exit;
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM master_kk WHERE code_id = ?');
    $stmt->execute([$code_id]);
    $exists = $stmt->fetchColumn() > 0;
    echo json_encode(['exists' => $exists]);
    exit;
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

// Rekap: tb_warga.nikk yang tidak ada di master_kk.nokk
$warga_nikk_belum_kk = [];
try {
    $sql_belum = "SELECT DISTINCT nikk FROM tb_warga WHERE nikk IS NOT NULL AND nikk != '' AND nikk NOT IN (SELECT nokk FROM master_kk WHERE nokk IS NOT NULL AND nokk != '')";
    $stmt_belum = $pdo->query($sql_belum);
    $nikk_list = $stmt_belum->fetchAll(PDO::FETCH_COLUMN);
    // Ambil nama KK dari tb_warga (yang hubungan Kepala Keluarga)
    if ($nikk_list) {
        $in = str_repeat('?,', count($nikk_list) - 1) . '?';
        $stmt_nama = $pdo->prepare("SELECT nikk, nama FROM tb_warga WHERE hubungan='Kepala Keluarga' AND nikk IN ($in)");
        $stmt_nama->execute($nikk_list);
        $warga_nikk_belum_kk = $stmt_nama->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $warga_nikk_belum_kk = [];
}
$jumlah_nikk_belum = count($warga_nikk_belum_kk);
?>
<div class="mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded">
  <b>Jumlah NIKK warga yang belum terdaftar di master KK:</b> <?= $jumlah_nikk_belum ?>
  <?php if ($jumlah_nikk_belum > 0): ?>
    <ul class="list-disc ml-6 mt-1 text-xs">
      <?php foreach ($warga_nikk_belum_kk as $item): ?>
        <li><?= htmlspecialchars($item['nama']) ?> (<?= htmlspecialchars($item['nikk']) ?>)</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

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
                                    <button class="text-blue-600 hover:text-blue-800 font-bold py-1 px-1" data-modal-toggle="editModal" data-id="<?php echo $row['code_id']; ?>" data-nikk="<?php echo $row['nokk']; ?>" data-name="<?php echo $row['kk_name']; ?>">
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
            <form action="api/kk_update.php" method="POST" x-data="kkDropdownSearch()" x-init="init()">
                <div class="mb-2 grid grid-cols-2 gap-3">
                    <input type="text" name="code_id" id="edit_code_id" class="border rounded w-full p-1 bg-gray-100" readonly required>
                    <input type="text" name="kk_name" id="edit_kk_name" class="border rounded w-full p-1 bg-gray-100" readonly required>
                </div>
                <div class="mb-2 relative">
                    <label class="block mb-1">No KK (NIKK) / Nama KK</label>
                    <input x-model="search" @focus="open = true" @input="open = true" type="text" placeholder="Cari No KK atau Nama KK..." class="w-full border rounded p-1" autocomplete="off">
                    <ul x-show="open && filteredOptions.length > 0" @click.away="open = false" class="absolute bg-white border w-full mt-1 rounded max-h-48 overflow-auto z-10">
                        <template x-for="kk in filteredOptions" :key="kk.nikk">
                            <li @click="selectOption(kk)" class="px-2 py-1 hover:bg-blue-500 hover:text-white cursor-pointer" x-text="kk.nikk + ' - ' + kk.kk_name"></li>
                        </template>
                    </ul>
                    <input type="hidden" id="edit_nikkDropdown" name="nikk" :value="selectedOption ? selectedOption.nikk : ''">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">No KK</label>
                    <input type="text" name="nokk" id="edit_nokk" class="border rounded w-full p-1 bg-gray-100" readonly required x-model="selectedOption ? selectedOption.nikk : ''">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Nama KK</label>
                    <input type="text" name="kk_name" id="edit_kk_name2" class="border rounded w-full p-1 bg-gray-100" readonly required x-model="selectedOption ? selectedOption.kk_name : ''">
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('editModal')">Tutup</button>
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Tambah Data KK dari Warga (NIKK) -->
    <div id="addModalNikk" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-3 rounded shadow-lg w-full max-w-md">
            <h2 class="text-lg font-bold mb-2">Tambah Data KK dari Warga</h2>
            <form id="formAddNikk" action="#" method="POST" x-data="kkDropdownSearch()" x-init="init()">
                <div class="mb-2">
                    <label class="block mb-1">Code ID</label>
                    <input type="text" id="add_code_id" name="code_id" class="border rounded w-full p-1" required placeholder="Contoh: RT0700001">
                </div>
                <div class="mb-2 relative">
                    <label class="block mb-1">No KK (NIKK) / Nama KK</label>
                    <input x-model="search" @focus="open = true" @input="open = true" type="text" placeholder="Cari No KK atau Nama KK..." class="w-full border rounded p-1" autocomplete="off">
                    <ul x-show="open && filteredOptions.length > 0" @click.away="open = false" class="absolute bg-white border w-full mt-1 rounded max-h-48 overflow-auto z-10">
                        <template x-for="kk in filteredOptions" :key="kk.nikk">
                            <li @click="selectOption(kk)" class="px-2 py-1 hover:bg-blue-500 hover:text-white cursor-pointer" x-text="kk.nikk + ' - ' + kk.kk_name"></li>
                        </template>
                    </ul>
                    <input type="hidden" id="nikkDropdown" name="nikk" :value="selectedOption ? selectedOption.nikk : ''">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">No KK</label>
                    <input type="text" id="nokkAuto" name="nokk" class="border rounded w-full p-1 bg-gray-100" readonly required x-model="selectedOption ? selectedOption.nikk : ''">
                </div>
                <div class="mb-2">
                    <label class="block mb-1">Nama KK</label>
                    <input type="text" id="kkNameAuto" name="kk_name" class="border rounded w-full p-1 bg-gray-100" readonly required x-model="selectedOption ? selectedOption.kk_name : ''">
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('addModalNikk')">Tutup</button>
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Tambah</button>
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
                const code_id = this.getAttribute('data-id');
                const nikk = this.getAttribute('data-nikk');
                const kk_name = this.getAttribute('data-name');
                document.getElementById('edit_code_id').value = code_id;
                document.getElementById('edit_kk_name').value = kk_name;
                // Set Alpine.js selectedOption di modal edit (tanpa code_id)
                const modal = document.getElementById('editModal');
                if (modal && modal.__x) {
                    modal.__x.$data.selectedOption = { nikk, kk_name };
                    modal.__x.$data.search = nikk + ' - ' + kk_name;
                }
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
                    const code_id = document.getElementById('add_code_id').value;
                    const nikk = document.getElementById('nikkDropdown').value;
                    const kk_name = document.getElementById('kkNameAuto').value;
                    const nokk = document.getElementById('nokkAuto').value;
                    fetch('api/kk_insert.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `code_id=${encodeURIComponent(code_id)}&kk_name=${encodeURIComponent(kk_name)}&nokk=${encodeURIComponent(nokk)}`
                    })
                    .then(res => res.text())
                    .then(resp => {
                        if (resp.includes('berhasih') || resp.includes('berhasil')) {
                            showToast('Data berhasil ditambah!', 'success');
                            setTimeout(() => location.reload(), 1200);
                        } else if (resp.includes('Code ID sudah ada')) {
                            showToast('Code ID sudah ada!', 'error');
                        } else {
                            showToast('Gagal menambah data!', 'error');
                        }
                    });
                });
            }
            // Handler submit form edit KK (editModal)
            var formEditKK = document.querySelector('#editModal form');
            if (formEditKK) {
                formEditKK.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const code_id = document.getElementById('edit_code_id').value;
                    const nikk = document.getElementById('edit_nikkDropdown').value;
                    const kk_name = document.getElementById('edit_kk_name2').value;
                    const nokk = document.getElementById('edit_nokk').value;
                    fetch('api/kk_update.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `code_id=${encodeURIComponent(code_id)}&kk_name=${encodeURIComponent(kk_name)}&nokk=${encodeURIComponent(nokk)}&nikk=${encodeURIComponent(nikk)}`
                    })
                    .then(res => res.text())
                    .then(resp => {
                        if (resp.includes('berhasil')) {
                            showToast('Data berhasil diupdate!', 'success');
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            showToast('Gagal update data!', 'error');
                        }
                    });
                });
            }
        });
        function kkDropdownSearch() {
            return {
                search: '',
                open: false,
                options: [],
                selectedOption: null,
                get filteredOptions() {
                    if (!Array.isArray(this.options)) return [];
                    if (!this.search) return this.options;
                    const term = this.search.toLowerCase();
                    return this.options.filter(kk =>
                        kk.nikk.toLowerCase().includes(term) ||
                        kk.kk_name.toLowerCase().includes(term)
                    );
                },
                selectOption(kk) {
                    this.selectedOption = kk;
                    this.search = kk.nikk + ' - ' + kk.kk_name;
                    this.open = false;
                },
                async init() {
                    const res = await fetch('api/get_nikk_group.php');
                    this.options = await res.json();
                    console.log('NIKK options loaded:', this.options);
                }
            }
        }
        // Validasi realtime code_id unik pada modal tambah KK
        function cekCodeIdUnik() {
            const codeIdInput = document.getElementById('add_code_id');
            const codeId = codeIdInput.value.trim();
            if (!codeId) return;
            fetch('kk.php?cek_code_id=' + encodeURIComponent(codeId))
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        alert('Code ID sudah ada, silakan gunakan yang lain!');
                        codeIdInput.value = '';
                        codeIdInput.focus();
                    }
                });
        }
        document.addEventListener('DOMContentLoaded', function() {
            const codeIdInput = document.getElementById('add_code_id');
            if (codeIdInput) {
                codeIdInput.addEventListener('blur', cekCodeIdUnik);
            }
        });

        // Tambahkan import SweetAlert2 jika belum ada
        if (!window.Swal) {
            var script = document.createElement('script');
            script.src = 'js/sweetalert2.all.min.js';
            document.head.appendChild(script);
        }
        // Fungsi toast SweetAlert2
        function showToast(msg, icon = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: msg,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }

        // Pada hapus data (link delete)
        document.querySelectorAll('a[href^="kk.php?delete="]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Yakin ingin menghapus data?')) {
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                fetch(this.href)
                    .then(res => res.text())
                    .then(resp => {
                        if (resp.includes('Location: kk.php')) {
                            showToast('Data berhasil dihapus!', 'success');
                            setTimeout(() => location.reload(), 1200);
                        } else {
                            showToast('Gagal menghapus data!', 'error');
                        }
                    });
            });
        });
    </script>
