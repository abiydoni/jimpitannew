<?php
// Pastikan tidak ada output sebelum header
ob_start();

require 'db.php';
date_default_timezone_set('Asia/Jakarta');

// Array bulan Indonesia
$bulanIndo = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember',
];

try {
    // Ambil tanggal hari ini (tanpa tahun)
    $today = date('m-d');

    // Query warga yang ulang tahun hari ini
    $stmt = $pdo->prepare("SELECT nama, tgl_lahir FROM tb_warga WHERE DATE_FORMAT(tgl_lahir, '%m-%d') = ?");
    $stmt->execute([$today]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bangun pesan ucapan ultah
    $pesan = "🎉 *SELAMAT ULANG TAHUN!* 🎂\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━━\n\n";

    if ($data && count($data) > 0) {
        $pesan .= "📅 *Hari ini ada yang berulang tahun:*\n\n";
        $no = 1;
        foreach ($data as $warga) {
            // Format tanggal lahir ke Indonesia (tanpa tahun)
            $tglObj = date_create($warga['tgl_lahir']);
            if ($tglObj) {
                $tgl = date_format($tglObj, 'd');
                $blnKey = date_format($tglObj, 'm');
                $bln = isset($bulanIndo[$blnKey]) ? $bulanIndo[$blnKey] : $blnKey;
                $nama = htmlspecialchars($warga['nama'], ENT_QUOTES, 'UTF-8');
                $pesan .= "$no. *{$nama}*\n";
                $pesan .= "   🎂 Lahir: $tgl $bln\n\n";
                $no++;
            }
        }
        if ($no > 1) {
            $pesan .= "━━━━━━━━━━━━━━━━━━━━\n";
            $pesan .= "🎈 *Semoga panjang umur, sehat selalu, dan bahagia!* ✨\n";
        } else {
            $pesan .= "Tidak ada warga yang berulang tahun hari ini.\n";
        }
    } else {
        $pesan .= "Tidak ada warga yang berulang tahun hari ini.\n";
    }

    $pesan .= "\n━━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "💐 *Salam hangat dari RT 07!*\n";
    $pesan .= "\n_Pesan Otomatis dari System_";

} catch (PDOException $e) {
    // Error handling untuk database
    $pesan = "❌ *Error*\n\n";
    $pesan .= "Terjadi kesalahan saat mengambil data ulang tahun.\n";
    $pesan .= "Silakan coba lagi nanti.\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_ultah.php: " . $e->getMessage());
} catch (Exception $e) {
    // Error handling umum
    $pesan = "❌ *Error*\n\n";
    $pesan .= "Terjadi kesalahan pada sistem.\n";
    $pesan .= "Silakan coba lagi nanti.\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_ultah.php: " . $e->getMessage());
}

// Bersihkan output buffer dan set header
ob_end_clean();
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
echo $pesan;
exit;