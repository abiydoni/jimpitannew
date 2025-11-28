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
    // Ambil hari dan tanggal hari ini
    $hariEng = date('l'); // Monday
    $hariInd = isset($hariIndo[$hariEng]) ? $hariIndo[$hariEng] : $hariEng; // Senin
    $tanggal = date('j');
    $bulanEng = date('F');
    $bulanInd = isset($bulanIndo[$bulanEng]) ? $bulanIndo[$bulanEng] : $bulanEng;
    $tahun = date('Y');

    // Ambil data dari tabel users
    $stmt = $pdo->prepare("SELECT name FROM users WHERE shift = :shift");
    $stmt->execute(['shift' => $hariEng]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pesan = "⏰ *JADWAL JAGA HARI INI*\n";
    $pesan .= "━━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "📅 *$hariInd, $tanggal $bulanInd $tahun*\n\n";

    if ($users && count($users) > 0) {
        $pesan .= "👥 *Daftar Petugas Jaga:*\n";
        $no = 1;
        foreach ($users as $user) {
            $nama = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
            $pesan .= "$no. $nama\n";
            $no++;
        }
    } else {
        $pesan .= "❌ Tidak ada petugas jaga hari ini.\n";
    }

    // Tambahkan penutup
    $pesan .= "\n━━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "🌟 *Selamat melaksanakan tugas*\n";
    $pesan .= "🏡 RT.07 RW.01\n\n";
    $pesan .= "🕸️ *Link Scan:*\n";
    $pesan .= "https://rt07.appsbee.my.id\n\n";
    $pesan .= "⚠️ *PENTING - WAJIB SCAN QR*\n";
    $pesan .= "Dihimbau kepada petugas jimpitan:\n";
    $pesan .= "• *WAJIB SCAN QR CODE*\n";
    $pesan .= "• Jumlah uang yang disetor *HARUS SAMA* dengan jumlah yang di *SCAN*\n";
    $pesan .= "• _Tidak boleh lebih dan tidak boleh kurang_\n";
    $pesan .= "\n━━━━━━━━━━━━━━━━━━━━\n";
    $pesan .= "_Pesan Otomatis dari System_";

} catch (PDOException $e) {
    // Error handling untuk database
    $pesan = "❌ *Error*\n\n";
    $pesan .= "Terjadi kesalahan saat mengambil data jadwal jaga.\n";
    $pesan .= "Silakan coba lagi nanti.\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_jaga.php: " . $e->getMessage());
} catch (Exception $e) {
    // Error handling umum
    $pesan = "❌ *Error*\n\n";
    $pesan .= "Terjadi kesalahan pada sistem.\n";
    $pesan .= "Silakan coba lagi nanti.\n\n";
    $pesan .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_jaga.php: " . $e->getMessage());
}

// Bersihkan output buffer dan set header
ob_end_clean();
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
echo $pesan;
exit;