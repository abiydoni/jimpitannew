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

// Ambil data KK yang nominal-nya 0 pada hari kemarin
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.nominal
    FROM master_kk
    LEFT JOIN report ON report.report_id = master_kk.code_id 
        AND report.jimpitan_date = CURDATE() - INTERVAL 1 DAY
    WHERE report.nominal = 0
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total nominal tetap dari data report kemarin
$stmtTotal = $pdo->prepare("
    SELECT SUM(nominal) as total_nominal 
    FROM report 
    WHERE jimpitan_date = CURDATE() - INTERVAL 1 DAY
");
$stmtTotal->execute();
$total_nominal = $stmtTotal->fetchColumn();

// Bangun pesan WhatsApp / Telegram
$pesan = "⏰ *Report Jimpitan Hari :* $hariInd, $tanggal $bulanInd $tahun _(Semalam)_\n\n";
$pesan .= "💰 Sebesar Rp. " . number_format($total_nominal, 0, ',', '.') . "\n\n";
$pesan .= "📋 *Jimpitan yang kosong :*\n";
$pesan .= "==========================\n";

if ($data) {
    $no = 1;
    foreach ($data as $user) {
        $pesan .= $no++ . ". " . $user['kk_name'] . "\n";
    }
} else {
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
