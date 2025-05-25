<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_id = $_POST['report_id'] ?? '';
    $jimpitan_date = $_POST['jimpitan_date'] ?? '';
    $collector = 'system';
    $kode_u = 'system';
    $nama_u = 'system';

    // Ambil tarif dari tabel
    $stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001' LIMIT 1");
    $stmt_tarif->execute();
    $nominal = $stmt_tarif->fetchColumn();

    if (!$report_id || !$jimpitan_date || !$nominal) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    // Cek duplikat
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM report WHERE report_id = ? AND jimpitan_date = ? AND collector = 'system'");
    $stmt_check->execute([$report_id, $jimpitan_date]);
    if ($stmt_check->fetchColumn() > 0) {
        echo json_encode(['status' => 'duplicate', 'message' => 'Data sudah ada untuk tanggal tersebut.']);
        exit;
    }

    // Simpan data
    $stmt_insert = $pdo->prepare("INSERT INTO report (report_id, jimpitan_date, nominal, collector, kode_u, nama_u) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt_insert->execute([$report_id, $jimpitan_date, $nominal, $collector, $kode_u, $nama_u])) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
    }
}
?>
