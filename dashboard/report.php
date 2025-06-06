<?php
session_start();
include 'header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek role user
if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php');
    exit;
}

include 'api/db.php';

// Tangani filter tanggal dan filter bulan (prioritas tanggal jika keduanya ada)
$filterDate = null;
$filterMonth = null;
$whereClause = '';
$params = [];

if (isset($_GET['date']) && $_GET['date'] !== '') {
    $filterDate = $_GET['date'];
    $whereClause = "WHERE DATE(jimpitan_date) = :date";
    $params[':date'] = $filterDate;
} elseif (isset($_GET['month']) && $_GET['month'] !== '') {
    $filterMonth = $_GET['month']; // format: YYYY-MM
    $whereClause = "WHERE DATE_FORMAT(jimpitan_date, '%Y-%m') = :month";
    $params[':month'] = $filterMonth;
} else {
    // default filter kemarin
    $filterDate = date('Y-m-d', strtotime('-1 day'));
    $whereClause = "WHERE DATE(jimpitan_date) = :date";
    $params[':date'] = $filterDate;
}

// Query data dengan filter yang sudah ditentukan
$sql = "
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    $whereClause
    ORDER BY report.jimpitan_date DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Mulai konten halaman -->
<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>LAPORAN JIMPITAN</h3>

            <input 
                type="text" 
                id="datePicker" 
                name="date" 
                class="custom-select" 
                placeholder="Pilih Tanggal" 
                value="<?= htmlspecialchars($filterDate ?? '') ?>"
                autocomplete="off"
            >

            <a href="report.php" class="btn-clear-filter">Reset Filter</a>

            <button type="button" id="refreshBtn" class="btn-refresh" onclick="window.location.href='report.php';">
                <i class='bx bx-refresh'></i> Refresh
            </button>

            <input 
                type="text" 
                id="monthPicker" 
                name="month-year" 
                class="custom-select" 
                placeholder="Pilih Bulan & Tahun"
                autocomplete="off"
                value="<?= htmlspecialchars($filterMonth ?? '') ?>"
            >

            <button type="button" id="reportBtn" class="btn-download">
                <i class='bx bxs-file-export'></i> Unduh
            </button>
        </div>

        <div id="table-container">
            <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th style="text-align: left;">Nama KK</th>
                        <th style="text-align: center;">Code</th>
                        <th style="text-align: center;">Tanggal</th>
                        <th style="text-align: center;">Nominal</th>
                        <th style="text-align: center;">Input By</th>
                        <th style="text-align: center;">Kode User</th>
                        <th style="text-align: center;">Scan Time</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php if ($data): ?>
                        <?php foreach ($data as $row): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td><?= htmlspecialchars($row["kk_name"]) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row["report_id"]) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row["jimpitan_date"]) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row["nominal"]) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row["collector"]) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row["kode_u"]) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row["scan_time"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data tersedia untuk filter ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Flatpickr untuk tanggal (date picker)
    flatpickr("#datePicker", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        allowInput: true,
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                // Redirect ke filter tanggal
                window.location.href = `report.php?date=${dateStr}`;
            }
        }
    });

    // Flatpickr untuk bulan & tahun (month picker)
    flatpickr("#monthPicker", {
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y",
            })
        ],
        allowInput: true,
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                // Redirect ke filter bulan
                window.location.href = `report.php?month=${dateStr}`;
            }
        }
    });

    // Inisialisasi DataTables dengan destroy jika sudah ada
    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }
        $('#example').DataTable({
            order: [[ 2, 'desc' ]],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                emptyTable: "Tidak ada data tersedia",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    }

    $(document).ready(function() {
        initDataTable();

        // Tombol refresh langsung reload halaman tanpa parameter apapun
        $('#refreshBtn').on('click', function() {
            window.location.href = 'report.php';
        });

        // Tombol "Unduh" (contoh: alert, kamu bisa ganti sesuai kebutuhan)
        $('#reportBtn').on('click', function() {
            alert('Fitur unduh belum diimplementasikan.');
        });
    });
</script>
