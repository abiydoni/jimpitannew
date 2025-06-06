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

$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    ORDER BY report.jimpitan_date DESC
");

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Mulai konten halaman -->
<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>LAPORAN JIMPITAN</h3>
            <button type="button" id="refreshBtn" class="btn-refresh" onclick="window.location.href='report.php';">
                <i class='bx bx-refresh'></i> Refresh
            </button>
            <input type="text" id="monthPicker" name="month-year" class="custom-select" placeholder="Pilih Bulan & Tahun">
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
        flatpickr("#monthPicker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true, // Gunakan nama bulan singkat (Jan, Feb, Mar, dll.)
                    dateFormat: "F Y", // Format untuk nilai yang dikembalikan
                    altFormat: "F Y", // Format untuk tampilan input
                })
            ],
            onChange: function(selectedDates, dateStr, instance) {
                console.log("Bulan dan tahun yang dipilih:", dateStr);
            }
        });

        const searchButton = document.querySelector('#content nav form .form-input button');
        const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
        const searchForm = document.querySelector('#content nav form');

        searchButton.addEventListener('click', function (e) {
            if(window.innerWidth < 576) {
                e.preventDefault();
                searchForm.classList.toggle('show');
                if(searchForm.classList.contains('show')) {
                    searchButtonIcon.classList.replace('bx-search', 'bx-x');
                } else {
                    searchButtonIcon.classList.replace('bx-x', 'bx-search');
                }
            }
        })

        if(window.innerWidth < 768) {
            sidebar.classList.add('hide');
        } else if(window.innerWidth > 576) {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
            searchForm.classList.remove('show');
        }

        window.addEventListener('resize', function () {
            if(this.innerWidth > 576) {
                searchButtonIcon.classList.replace('bx-x', 'bx-search');
                searchForm.classList.remove('show');
            }
        });
    </script>
