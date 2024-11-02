<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit;
}

// Check if user is admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php'); // Redirect to unauthorized page
    exit;
}
// Include the database connection
include 'api/db.php';

// Prepare the SQL statement to select only today's shift
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
");

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <!-- My CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <title>Report</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Urut Tanggal Terbaru</title>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-square-rounded'></i>
            <span class="text">Jimpitan</span>
        </a>
        <ul class="side-menu top">
            <li><a href="index.php"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
            <li><a href="jadwal.php"><i class='bx bxs-group'></i><span class="text">Jadwal Jaga</span></a></li>
            <li><a href="kk.php"><i class='bx bxs-group'></i><span class="text">KK</span></a></li>
            <li class="active"><a href="#"><i class='bx bxs-report'></i><span class="text">Report</span></a></li>
            <li><a href="keuangan.php"><i class='bx bxs-wallet'></i><span class="text">Keuangan</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="setting.php"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
            <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu' ></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" id="search-input" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
					<!-- <button type="button" class="clear-btn"><i class='bx bx-reset' ></i></button> -->
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Jimpitan - RT07 Salatiga</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Report</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="index.php">Home</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Report</h3>
                            <input type="text" id="monthPicker" name="month-year" class="custom-select" placeholder="Pilih Bulan & Tahun">
                            <button type="button" id="reportBtn" class="btn-download">
                                <i class='bx bxs-file-export'></i> Unduh
                            </button>
                    </div>
                    <div class="m-4"> <!-- Margin di sekitar tabel -->
                    <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden">
                        <thead class="bg-gray-200">
                            <tr>
                                <th style="text-align: Left;">Nama KK</th> <!-- Kolom pertama rata kiri -->
                                <th style="text-align: center;">Code</th>
                                <th style="text-align: center;" id="sort-date">Tanggal</th>
                                <th style="text-align: center;">Nominal</th>
                                <th style="text-align: center;">Input By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($data) {
                                    foreach ($data as $row): ?>
                                        <tr class="border-b hover:bg-gray-100">
                                            <td><?php echo htmlspecialchars($row["kk_name"]); ?></td> <!-- Rata kiri -->
                                            <td><?php echo htmlspecialchars($row["report_id"]); ?></td> <!-- Rata tengah -->
                                            <td><?php echo htmlspecialchars($row["jimpitan_date"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["nominal"]); ?></td>
                                            <td><?php echo htmlspecialchars($row["collector"]); ?></td>
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
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT --> 
    <script src="js/monthSelectPlugin.js"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script src="js/script.js"></script>
    <script src="js/report.js"></script>
    <script src="js/export.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.1/exceljs.min.js"></script>


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
        })
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

</body>
</html>