<?php
session_start();
include 'header.php';
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
include 'api/db.php';

// Prepare the SQL statement to select only today's shift
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
                    <div id="table-container"> <!-- Tambahkan div untuk menampung tabel -->
                        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th style="text-align: Left;">Nama KK</th>
                                    <th style="text-align: center;">Code</th>
                                    <th style="text-align: center;" id="sort-date">Tanggal</th>
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
                                                <td><?php echo htmlspecialchars($row["kk_name"]); ?></td>
                                                <td><?php echo htmlspecialchars($row["report_id"]); ?></td>
                                                <td><?php echo htmlspecialchars($row["jimpitan_date"]); ?></td>
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
    </script>
    
<!-- <script>
    // Fungsi untuk mengurutkan tabel berdasarkan kolom tanggal
    const tableBody = document.getElementById('table-body');
    const sortDateButton = document.getElementById('sort-date');
    let ascending = false; // Urutan awal: terbaru ke terlama

    sortDateButton.addEventListener('click', () => {
      const rows = Array.from(tableBody.querySelectorAll('tr'));

      rows.sort((a, b) => {
        const dateA = new Date(a.cells[2].innerText);
        const dateB = new Date(b.cells[2].innerText);
        return ascending ? dateA - dateB : dateB - dateA;
      });

      ascending = !ascending; // Balik urutan setiap klik
      rows.forEach(row => tableBody.appendChild(row)); // Re-attach rows yang sudah diurutkan

      // Update simbol panah untuk indikasi sorting
    //   sortDateButton.querySelector('span').innerHTML = ascending ? '&#9660;' : '&#9650;';
    });
  </script> -->
