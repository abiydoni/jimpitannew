<?php
require 'db.php'; // koneksi PDO

// Terjemahan hari & bulan
$hariIndo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
];

$bulanIndo = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember',
];

// Ambil hari ini dalam bahasa Inggris (untuk query) dan Indonesia (untuk tampilan)
$hariEng = date('l'); // contoh: Monday
$hariInd = $hariIndo[$hariEng]; // Senin
$tanggal = date('j');
$bulanEng = date('F');
$bulanInd = $bulanIndo[$bulanEng];
$tahun = date('Y');

// Query berdasarkan shift = hari ini dalam bhs Inggris
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
?>
