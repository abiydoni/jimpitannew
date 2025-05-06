<?php
require 'db.php'; // memanggil koneksi PDO

// Hari dalam bahasa Indonesia
$hariIndo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
];

// Bulan dalam bahasa Indonesia
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

$hariEng = date('l');
$hariIni = $hariIndo[$hariEng];
$tanggal = date('j');
$bulan = $bulanIndo[date('F')];
$tahun = date('Y');

// Query dengan PDO
$stmt = $pdo->prepare("SELECT name FROM users WHERE shift = :shift");
$stmt->execute(['shift' => $hariIni]);
$users = $stmt->fetchAll();

$pesan = "Jadwal Jaga Hari ini, $hariIni, $tanggal $bulan $tahun\n\n";

if ($users) {
    $no = 1;
    foreach ($users as $user) {
        $pesan .= $no++ . ". " . $user['name'] . "\n";
    }
} else {
    $pesan .= "Tidak ada petugas jaga hari ini.";
}
?>
