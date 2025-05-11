<?php
include 'db.php';

// Set header JSON
header('Content-Type: application/json');

// Terjemahan hari dan bulan ke Bahasa Indonesia
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

// Ambil hari dan tanggal HARI INI
$hariEng = date('l');
$hariInd = $hariIndo[$hariEng];
$tanggal = date('j');
$bulanEng = date('F');
$bulanInd = $bulanIndo[$bulanEng];
$tahun = date('Y');

// Ambil data laporan untuk HARI KEMARIN
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.*
    FROM report
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE() - INTERVAL 2 DAY
    ORDER BY report.scan_time DESC
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal
$total_nominal = array_sum(array_column($data, 'nominal'));

// Bangun pesan WhatsApp / Telegram
$pesan = "⏰ *Report Jimpitan Hari :* $hariInd, $tanggal $bulanInd $tahun _(Semalam)_\n\n";
$pesan .= "💰 Sebesar Rp. " . number_format($total_nominal, 0, ',', '.') . "\n\n";
$pesan .= "📋 *Jimpitan yang kosong :*\n";
$pesan .= "==========================\n";

$no = 1;
$adaKosong = false;
foreach ($data as $user) {
    if ($user['nominal'] == 0) {
        $pesan .= $no++ . ". " . $user['kk_name'] . "\n";
        $adaKosong = true;
    }
}
if (!$adaKosong) {
    $pesan .= "✅ Semua KK menyetor jimpitan.\n";
}

// Tambahkan penutup
$pesan .= "\n🌟 Diharapkan kedepannya bisa diperhatikan\n";
$pesan .= "_- Pesan Otomatis dari System -_";

// Kembalikan juga sebagai JSON untuk debugging (opsional)
echo json_encode([
    'data' => $data,
    'total_nominal' => $total_nominal,
    'pesan' => $pesan
]);
?>
