<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];
    $jimpitan_date = $_POST['jimpitan_date'];
    $collector = 'system';
    $kode_u = 'system';
    $nama_u = 'system';

    // Ambil nominal dari tarif
    $stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001' LIMIT 1");
    $stmt_tarif->execute();
    $nominal = $stmt_tarif->fetchColumn();

    // Cek apakah data dengan report_id dan jimpitan_date sudah ada
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM report WHERE report_id = ? AND jimpitan_date = ?");
    $stmt_check->execute([$report_id, $jimpitan_date]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        // Jika sudah ada, bisa redirect dengan pesan error, atau langsung ke halaman input dengan pesan
        // Contoh: simpan pesan di session dan redirect
        $_SESSION['error'] = "Data untuk KK ini pada tanggal yang dipilih sudah ada.";
        header("Location: jimpitan_manual.php?date=" . urlencode($jimpitan_date));
        exit;
    }

    // Jika belum ada, insert data baru
    $stmt_insert = $pdo->prepare("INSERT INTO report (report_id, jimpitan_date, nominal, collector, kode_u, nama_u) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->execute([$report_id, $jimpitan_date, $nominal, $collector, $kode_u, $nama_u]);
    $_SESSION['success'] = "Data berhasil disimpan!";
    header("Location: jimpitan_manual.php?date=" . urlencode($jimpitan_date));
    exit;
} else {
    header('Location: jimpitan_manual.php');
    exit;
}