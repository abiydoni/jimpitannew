<?php
session_start();

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

// Periksa apakah pengguna adalah admin atau s_admin
    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
// Sertakan koneksi database
include 'api/db.php';
// Ambil role dari session
$role = $_SESSION['user']['role'];

// Ambil semua menu yang mengizinkan role ini
$stmt = $pdo->prepare("SELECT * FROM tb_dashboard_menu WHERE FIND_IN_SET(?, role) ORDER BY urutan");
$stmt->execute([$role]);
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dapatkan halaman aktif
$currentPage = basename($_SERVER['PHP_SELF']);

// Ambil nama dan logo dari tb_profil
$profil_nama = 'Dashboard';
$profil_logo = '';
$stmtProfil = $pdo->query("SELECT nama, logo FROM tb_profil LIMIT 1");
if ($rowProfil = $stmtProfil->fetch(PDO::FETCH_ASSOC)) {
    $profil_nama = $rowProfil['nama'];
    $profil_logo = $rowProfil['logo'];
    // Jika logo tidak mengandung '/' (hanya nama file), tambahkan path ../assets/image/
    if ($profil_logo && strpos($profil_logo, '/') === false) {
        $profil_logo = '../assets/image/' . $profil_logo;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profil_nama) ?></title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Modal CSS -->
    <link rel="stylesheet" href="css/modal.css">
    <!-- Modal Fix CSS - Load last to override everything -->
    <link rel="stylesheet" href="css/modal-fix.css">
    <!-- sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <!-- jQuery harus di atas Select2, dan hanya satu kali -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.6/dist/datepicker.min.js"></script>
    <!-- XLSX Library untuk Export/Import Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand flex items-center gap-2">
            <?php if ($profil_logo): ?>
                <img src="<?= htmlspecialchars($profil_logo) ?>" alt="Logo" class="w-8 h-8 object-contain rounded" />
            <?php else: ?>
                <i class='bx bx-square-rounded'></i>
            <?php endif; ?>
            <span class="text">appsBee</span>
        </a>
        <ul class="side-menu top">
            <?php foreach ($menuItems as $item): ?>
                <?php $isActive = (basename($item['url']) === $currentPage) ? 'active' : ''; ?>
                <li class="<?= $isActive ?>">
                    <a href="<?= htmlspecialchars($item['url']) ?>">
                        <i class='bx <?= htmlspecialchars($item['icon']) ?> text-2xl'></i>
                        <span class="text"><?= htmlspecialchars($item['title']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu' ></i>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
        </nav>
        <!-- NAVBAR -->
        <main>
