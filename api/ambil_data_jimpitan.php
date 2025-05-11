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
    WHERE report.jimpitan_date = CURDATE() - INTERVAL 1 DAY
    ORDER BY report.scan_time DESC
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal
$total_nominal = array_sum(array_column($data, 'nominal'));

// Bangun pesan WhatsApp / Telegram
$pesan = "⏰ *Report Jimpitan Hari :* $hariInd, $tanggal $bulanInd $tahun _(Semalam)_\n\n";
$pesan .= "💰 Sebesar Rp. " . number_format($total_nominal, 0, ',', '.') . "\n";
$pesan .= "📋 Jimpitan yang kosong:\n";

if ($data) {
    $no = 1;
    foreach ($data as $user) {
        $pesan .= $no++ . "️. " . $user['kk_name'] . "\n";
    }
} else {
    $pesan .= "❌ Semua sudah memberikan jimpitan";
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
