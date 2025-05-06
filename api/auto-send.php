<?php
require 'db.php'; // koneksi database dengan PDO

// --- Proteksi akses pakai token ---
if (!isset($_GET['key']) || $_GET['key'] !== 'abc123') {
    http_response_code(403);
    exit('Access denied.');
}

// --- Format hari dan tanggal ---
$hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
];

$bulanIndo = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];

$hariEng = date('l');
$hariInd = $hariIndo[$hariEng];
$tanggal = date('j');
$bulanInd = $bulanIndo[date('F')];
$tahun = date('Y');

// --- Ambil data jaga dari database ---
$stmt = $pdo->prepare("SELECT name FROM users WHERE shift = :shift");
$stmt->execute(['shift' => $hariEng]);
$users = $stmt->fetchAll();

$pesan = "Jadwal Jaga Hari ini, $hariInd, $tanggal $bulanInd $tahun\n\n";

if ($users) {
    $no = 1;
    foreach ($users as $user) {
        $pesan .= $no++ . ". " . $user['name'] . "\n";
    }
} else {
    $pesan .= "Tidak ada petugas jaga hari ini.";
}

$pesan .= "\n\n🌟 Selamat melaksanakan tugas RT.07\n";
$pesan .= "Pesan Otomatis System";

// --- Kirim pesan ke grup WA ---
$groupId = "120363398680818900@g.us"; // ganti sesuai grup WA kamu
$url = "https://rt07.appsbee.my.id/api/send-wa-group.php"; // URL API

// Data yang dikirim ke API
$data = [
    'groupId' => [$groupId], // Membungkus groupId dengan array
    'message' => $pesan
];

// Inisialisasi cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Mengirim data sebagai JSON

// Eksekusi cURL
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Cek responsenya
if ($httpCode == 200) {
    file_put_contents("log-pengiriman.txt", "[".date('Y-m-d H:i:s')."] Pesan berhasil dikirim.\n", FILE_APPEND);
} else {
    file_put_contents("log-pengiriman.txt", "[".date('Y-m-d H:i:s')."] ERROR: " . curl_error($ch) . "\n", FILE_APPEND);
}

curl_close($ch);
?>
