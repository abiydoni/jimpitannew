<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mulai sesi
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek apakah pengguna adalah admin
if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php');
    exit;
}

// Include koneksi database
include 'db.php';

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $code_id = $_POST['code_id'];
    // Cek apakah code_id sudah ada di database
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM master_kk WHERE code_id = ?");
    $stmt_check->execute([$code_id]);
    $exists = $stmt_check->fetchColumn();

    if ($exists > 0) {
        session_start();
        $_SESSION['swal'] = ['msg' => 'Code ID sudah ada', 'icon' => 'error'];
        header('Location: ../kk.php');
        exit;
    }
    
    $kk_name = $_POST['kk_name'];
    $nokk = $_POST['nokk'];

    // Siapkan dan eksekusi pernyataan SQL untuk menyimpan data
    $stmt = $pdo->prepare("INSERT INTO master_kk (code_id, kk_name, nokk) VALUES (?, ?, ?)");
    $stmt->execute([$code_id, $kk_name, $nokk]);
    session_start();
    $_SESSION['swal'] = ['msg' => 'Data berhasil ditambahkan!', 'icon' => 'success'];
    header('Location: ../kk.php');
    exit;
}
?>