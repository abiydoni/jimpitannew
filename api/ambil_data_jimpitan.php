<?php
require 'db.php';

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
// $hariEng = date('l');
// $hariInd = $hariIndo[$hariEng];
// $tanggal = date('j');
// $bulanEng = date('F');
// $bulanInd = $bulanIndo[$bulanEng];
// $tahun = date('Y');

// Ambil data KK yang nominal-nya 0 pada hari kemarin
$stmt = $pdo->prepare("
SELECT 
m.code_id, 
m.kk_name, 
COALESCE(SUM(r.nominal), 0) AS jumlah_nominal
FROM master_kk m
LEFT JOIN report r ON m.code_id = r.report_id 
AND r.jimpitan_date = CURDATE() - INTERVAL 1 DAY 
GROUP BY m.code_id, m.kk_name
ORDER BY m.code_id ASC;
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_nominal = array_sum(array_column($data, 'jumlah_nominal'));

$kemarin = new DateTime('yesterday');
$tanggal = $kemarin->format('Y-m-d');
$hariEng = $kemarin->format('l');
$hariInd = $hariIndo[$hariEng];
$tgl = $kemarin->format('j');
$bulanEng = $kemarin->format('F');
$bulanInd = $bulanIndo[$bulanEng];
$tahun = $kemarin->format('Y');

$tanggalLengkap = "$hariInd, $tgl $bulanInd $tahun";

// Bangun pesan WhatsApp / Telegram
$pesan = "⏰ *Report Jimpitan Hari* $tanggalLengkap _(Semalam)_\n\n";
$pesan .= "💰 Sebesar Rp. " . number_format($total_nominal, 0, ',', '.') . "\n\n";
$pesan .= "📋 *Jimpitan yang kosong (kode KK) :*\n";
$pesan .= "==========================\n";

if ($data) {
    $no = 1;
    foreach ($data as $user) {
        if ((int)$user['jumlah_nominal'] === 0) {
            // $pesan .= $no++ . ". " . $user['code_id'] . "\n";

            // $kk_name = $user['kk_name'];
            // $kk_anonim = strtoupper(substr($kk_name, 0, 1)) . '•••••' . strtolower(substr($kk_name, -1));
            // $pesan .= $no++ . ". " . $user['code_id'] . " - " . $kk_anonim . "\n";

            $pesan .= $no++ . ". " . $user['code_id'] . " - " . $user['kk_name'] . "\n";

        }
    }

    if ($no === 1) {
        $pesan .= "✅ Semua KK menyetor jimpitan.\n";
    }
} else {
    $pesan .= "❌ Tidak ada data tersedia.\n";
}

// Tambahkan penutup
$pesan .= "\n🌟 Terimakasih atas perhatiannya\n";
$pesan .= "Info lebih lanjut bisa hubungi *ADMIN*\n\n";
$pesan .= "_- Pesan Otomatis dari System -_";
?>
