<?php
session_start();
// Include the database connection
include 'api/db.php';

// Set default tanggal ke hari ini jika belum ada filter tanggal
$filterDate = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : date('Y-m-d', strtotime('-1 day'));

$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE DATE(jimpitan_date) = :date
    ORDER BY report.jimpitan_date DESC
");
$stmt->execute([
    ':date' => $filterDate
]);

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format untuk ditampilkan di datePicker
$displayDate = '';
if (strtotime($filterDate)) {
    $displayDate = date('d F Y', strtotime($filterDate));
}
include 'header.php';
?>

<style>
.flatpickr-monthSelect-months {
    display: flex !important;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
    max-width: 320px;
    min-width: 280px;
}
</style>

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
            value="<?= htmlspecialchars($filterDate) ?>">

            <button type="button" id="refreshBtn" class="btn-refresh" onclick="window.location.href='report.php';">
                <i class='bx bx-refresh'></i> Reset
            </button>
            <input type="text" id="monthPicker" name="month-year" class="custom-select" placeholder="Pilih Bulan & Tahun">
            <button type="button" id="reportBtn" class="btn-download">
                <i class='bx bxs-file-export'></i> Unduh
            </button>
        </div>
        <div id="table-container"> <!-- Tambahkan div untuk menampung tabel -->
            <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                <thead class="bg-gray-200">
                    <tr>
                        <th style="text-align: center;" id="sort-date">Tanggal</th>
                        <th style="text-align: Left;">Nama KK</th>
                        <th style="text-align: center;">Code</th>
                        <th style="text-align: center;">Nominal</th>
                        <th style="text-align: center;">Input By</th>
                        <th style="text-align: center;">Kode User</th>
                        <th style="text-align: center;">Scan Time</th>
                    </tr>
                </thead>
                <tbody id="table-body"> <!-- Tambahkan ID untuk tbody -->
                    <?php
                        if ($data) {
                            foreach ($data as $row): ?>
                                <tr class="border-b hover:bg-gray-100">
                                    <td><?php echo htmlspecialchars($row["jimpitan_date"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["kk_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["report_id"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["nominal"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["collector"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["kode_u"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["scan_time"]); ?></td>
                                </tr>
                            <?php endforeach; 
                        } else {
                            echo '<tr><td colspan="5" class="px-6 py-4 text-center">No data available</td></tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
    <!-- <script>
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
    </script> -->
<script>
    flatpickr("#datePicker", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        onChange: function(selectedDates, dateStr, instance) {
            window.location.href = `report.php?date=${dateStr}`;
        }
    });
</script>
<script>
    // Month Picker aktif
    flatpickr("#monthPicker", {
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "F Y",
                altFormat: "F Y",
            })
        ],
        onChange: function(selectedDates, dateStr, instance) {
            console.log("Bulan dan tahun yang dipilih:", dateStr);
            // Arahkan ke URL berdasarkan bulan (opsional)
            // window.location.href = `report.php?month=${dateStr}`;
        }
    });
</script>
