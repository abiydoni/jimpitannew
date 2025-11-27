<?php
// Pastikan tidak ada output sebelum header
ob_start();

require 'db.php';
date_default_timezone_set('Asia/Jakarta');

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

try {
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
    $hariInd = isset($hariIndo[$hariEng]) ? $hariIndo[$hariEng] : $hariEng;
    $tgl = $kemarin->format('j');
    $bulanEng = $kemarin->format('F');
    $bulanInd = isset($bulanIndo[$bulanEng]) ? $bulanIndo[$bulanEng] : $bulanEng;
    $tahun = $kemarin->format('Y');

    $tanggalLengkap = "$hariInd, $tgl $bulanInd $tahun";

    // Bangun pesan WhatsApp / Telegram
    $pesan = "⏰ *Report Jimpitan Hari* $tanggalLengkap _(Semalam)_\n\n";
    $pesan .= "💰 Sebesar Rp. " . number_format($total_nominal, 0, ',', '.') . "\n\n";
    $pesan .= "📋 *Jimpitan yang kosong (kode KK) :*\n";
    $pesan .= "==========================\n";

    if ($data && count($data) > 0) {
        $no = 1;
        foreach ($data as $user) {
            if ((int)$user['jumlah_nominal'] === 0) {
                $code_id = htmlspecialchars($user['code_id'], ENT_QUOTES, 'UTF-8');
                $kk_name = htmlspecialchars($user['kk_name'], ENT_QUOTES, 'UTF-8');
                $pesan .= $no++ . ". " . $code_id . " - " . $kk_name . "\n";
            }
        }

        if ($no === 1) {
            $pesan .= "✅ Semua KK menyetor jimpitan.\n";
        }
    } else {
        $pesan .= "❌ Tidak ada data tersedia.\n";
    }
    $pesan .= "==========================\n";
    
    // Tambahkan data petugas jimpitan (scan > 0) dari tabel report
    $stmt_petugas = $pdo->prepare("
        SELECT 
            kode_u, 
            nama_u, 
            COUNT(*) as jumlah_scan
        FROM report
        WHERE jimpitan_date = CURDATE() - INTERVAL 1 DAY
        GROUP BY kode_u, nama_u
        HAVING jumlah_scan > 0
        ORDER BY jumlah_scan DESC
    ");
    $stmt_petugas->execute();
    $data_petugas = $stmt_petugas->fetchAll(PDO::FETCH_ASSOC);

    if ($data_petugas && count($data_petugas) > 0) {
        $pesan .= "👤 *Petugas Jimpitan :*\n";
        $no_petugas = 1;
        foreach ($data_petugas as $petugas) {
            $nama_u = htmlspecialchars($petugas['nama_u'], ENT_QUOTES, 'UTF-8');
            $jumlah_scan = (int)$petugas['jumlah_scan'];
            $pesan .= $no_petugas . ". {$nama_u} ({$jumlah_scan} scan)\n";
            $no_petugas++;
        }
    } else {
        $pesan .= "\n👤 Tidak ada data petugas jimpitan.\n";
    }
    $pesan .= "==========================\n";
    $pesan .= "*Info :*\n";
    $pesan .= "Mulai sekarang warga dapat mengakses aplikasi ini\n";
    $pesan .= "Silahkan klik disini : *https://rt07.appsbee.my.id*\n";
    $pesan .= "Gunakan User: warga dan Password: warga\n";
    $pesan .= "==========================\n";
    // Tambahkan penutup
    $pesan .= "🌟 Terimakasih atas perhatiannya\n";
    $pesan .= "Info lebih lanjut bisa hubungi *ADMIN*\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";

} catch (PDOException $e) {
    // Error handling untuk database
    $pesan = "❌ *Error*\n\n";
    $pesan .= "Terjadi kesalahan saat mengambil data jimpitan.\n";
    $pesan .= "Silakan coba lagi nanti.\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_jimpitan.php: " . $e->getMessage());
} catch (Exception $e) {
    // Error handling umum
    $pesan = "❌ *Error*\n\n";
    $pesan .= "Terjadi kesalahan pada sistem.\n";
    $pesan .= "Silakan coba lagi nanti.\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_jimpitan.php: " . $e->getMessage());
}

// Bersihkan output buffer dan set header
ob_end_clean();
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
echo $pesan;
exit;