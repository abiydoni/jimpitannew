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

// Get the current day of the week (e.g., "Sunday", "Monday", etc.)
$currentDay = date('l'); // 'l' gives full textual representation of the day

// Prepare the SQL statement to select only today's shift
$stmt = $pdo->prepare("
    SELECT name, shift 
    FROM users 
    WHERE shift = :currentDay
");

// Bind the parameter
$stmt->bindParam(':currentDay', $currentDay);

// Execute the SQL statement
$stmt->execute();

// Fetch all results
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'api/get_info.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- My CSS -->
    <link rel="stylesheet" href="css/style.css">

    <title>Jimpitan - RT07 Salatiga</title>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-square-rounded'></i>
            <span class="text">Jimpitan</span>
        </a>
        <ul class="side-menu top">
            <li class="active"><a href="#"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
            <li><a href="jadwal.php"><i class='bx bxs-group'></i><span class="text">Jadwal Jaga</span></a></li>
            <li><a href="kk.php"><i class='bx bxs-group'></i><span class="text">KK</span></a></li>
            <li><a href="report.php"><i class='bx bxs-report'></i><span class="text">Report</span></a></li>
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
                    <!-- <input type="search" id="search-input" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search' ></i></button> -->
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
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
            

            <ul class="box-info">
                <li>
                    <i class='bx bxs-group bx-lg' ></i>
                    <span class="text">
                        <h3 id="totalPeserta"><?php echo $totalKK; ?> KK</h3>
                        <a href="kk.php">Kepala Keluarga</a>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-badge-check bx-lg' ></i>
                    <span class="text">
                        <h3 id="totalSaldo">
                            <?php 
                                function formatRupiah($angka) {
                                    return "Rp " . number_format($angka, 0, ',', '.');
                                }

                                // Contoh penggunaan
                                $saldo = $totalSaldo;
                                echo formatRupiah($saldo); // Output: Rp 1.500.000
                            ?>
                        </h3>
                        <a href="keuangan.php">Saldo KAS</a>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-info-circle bx-lg'></i>
                    <span class="text">
                        <h3 id="totalUncheck"><?php echo $totalUsers; ?> Orang</h3>
                        <a href="jadwal.php">Users JAGA</a>
                    </span>
                </li>
            </ul>

            <ul class="box-info">
                <li>
                    <div class="table-data">
                        <div class="order">
                            <div class="head">
                                <h3>Jaga Malam Hari ini</h3>
                            </div>
                                <?php
                                    // Mengatur locale ke bahasa Indonesia
                                    setlocale(LC_TIME, 'id_ID.UTF-8'); // Untuk sistem berbasis Unix/Linux
                                    // setlocale(LC_TIME, 'ind'); // Untuk Windows

                                    // Mengambil tanggal sekarang
                                    $tanggal_sekarang = strftime("%A, %d %B %Y");

                                    echo "<p>$tanggal_sekarang</p>";
                                ?>

                            <table id="checkin-table" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="border border-gray-300 px-4 py-2 text-left">NAMA</th>
                                        <th class="border border-gray-300 px-4 py-2 text-left">SHIFT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    if ($data) {
                                        foreach ($data as $row): ?>
                                            <tr class="border-b hover:bg-gray-100">
                                                <td><?php echo htmlspecialchars($row["name"]); ?></td>
                                                <td><?php echo htmlspecialchars($row["shift"]); ?></td>
                                            </tr>
                                        <?php endforeach;
                                    } else {
                                        echo '<tr><td colspan="2" class="text-center">No data available</td></tr>';
                                    }
                                ?>
                                </tbody>
                            </table>                        
                        </div>
                    </div>

                </li>
                <li>
                <canvas id="myChart" class="w-full max-w-md mx-auto bg-white p-4 rounded-lg shadow"></canvas>
                </li>
                </ul>

                    <!-- <div class="todo">
                        <div class="head">
                            <h3>Jaga Malam</h3>
                        </div>
                                <ul id="medal-list" class="todo-list">
                            <li class="first">
                            </li>
                        <li class="second">
                        </li>
                    <li class="third">
                    </li>
                        <li class="fourth">
                        </li>
                            <li class="fifth">
                            </li>
                            <li class="sixth">
                            </li>
                                </ul>
                            </div> -->
                    </main>
            <!-- MAIN -->
        </section>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="js/script.js"></script>
    <script src="js/grafik.js"></script>

</body>
</html>