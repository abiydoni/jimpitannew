<?php
header('Content-Type: application/json');
include 'db.php'; // Pastikan untuk menyertakan koneksi database

// Mendapatkan data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

$reff = $data['reff'];
$debet = $data['debet'];
$tanggal = $data['tanggal'];

// Validasi input
if (empty($reff) || empty($debet) || empty($tanggal)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}

// Menyiapkan query untuk memasukkan data ke tabel kas_umum
$query = "INSERT INTO kas_umum (reff, debet, tanggal) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sis", $reff, $debet, $tanggal);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Pemasukan berhasil ditambahkan.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan pemasukan.']);
}

$stmt->close();
$conn->close();
?>