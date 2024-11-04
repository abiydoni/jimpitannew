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
include 'db.php';
// Prepare and execute the SQL statement
$stmt = $pdo->prepare("SELECT * FROM users"); // Update 'your_table'
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Validasi input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_code = filter_input(INPUT_POST, 'id_code', FILTER_VALIDATE_INT);
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $shift = filter_input(INPUT_POST, 'shift', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    // Pastikan semua input valid
    if ($id_code === false || empty($user_name) || empty($name) || empty($password) || empty($shift) || empty($role)) {
        // Tangani kesalahan validasi
        echo "Input tidak valid!";
        exit;
    }

    // Menyisipkan data ke database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (id_code, user_name, name, password, shift, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_code, $user_name, $name, password_hash($password, PASSWORD_DEFAULT), $shift, $role]);
    } catch (PDOException $e) {
        echo "Kesalahan: " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>

    <!-- My CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>KK</title>
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
            <li class="active"><a href="jadwal.php"><i class='bx bxs-group'></i><span class="text">Jadwal Jaga</span></a></li>
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
                            <a href="#">Users</a>
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
                        <h3 class="text-lg font-bold text-gray-800">Input Data Users</h3> <!-- Mengubah ukuran dan ketebalan teks -->
                    </div>
                </div>

                <div class="order">
                    <div class="head">
                        <h3 class="text-lg font-bold text-gray-800">Input Data Users</h3> <!-- Mengubah ukuran dan ketebalan teks -->
                    </div>
                    <div>
                        <form action="insert.php" method="POST" class="space-y-4">
                            <div class="bg-white p-4 rounded-lg shadow-md"> <!-- Menambahkan latar belakang dan bayangan -->
                                <label class="block text-sm font-medium text-gray-700">ID Code:</label>
                                <input type="number" name="id_code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-md"> <!-- Menambahkan latar belakang dan bayangan -->
                                <label class="block text-sm font-medium text-gray-700">Username:</label>
                                <input type="text" name="user_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-md"> <!-- Menambahkan latar belakang dan bayangan -->
                                <label class="block text-sm font-medium text-gray-700">Name:</label>
                                <input type="text" name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-md"> <!-- Menambahkan latar belakang dan bayangan -->
                                <label class="block text-sm font-medium text-gray-700">Password:</label>
                                <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-md"> <!-- Menambahkan latar belakang dan bayangan -->
                                <label class="block text-sm font-medium text-gray-700">Shift:</label>
                                <input type="text" name="shift" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-md"> <!-- Menambahkan latar belakang dan bayangan -->
                                <label class="block text-sm font-medium text-gray-700">Role:</label>
                                <input type="text" name="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500" required>
                            </div>
                            <button type="submit" class="mt-4 bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">Submit</button> <!-- Menambahkan transisi -->
                        </form>                    
                    </div>
                </div>
            </div>        
        </main>
        <!-- MAIN -->
    </section>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

    <script src="../js/script.js"></script>

    <script>
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
</body>
</html>