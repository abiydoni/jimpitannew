<?php
// Pastikan tidak ada output sebelum header
ob_start();

require 'db.php'; // koneksi PDO

try {
    $stmt = $pdo->query("SELECT code_id, kk_name FROM master_kk ORDER BY kk_name ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $text = "📋 *DATA KEPALA KELUARGA*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "🏡 Randuares RT.07 RW.01\n\n";
    
    if ($data && count($data) > 0) {
        $text .= "👥 *Daftar Kepala Keluarga:*\n\n";
        $no = 1;
        foreach ($data as $row) {
            $code_id = htmlspecialchars($row['code_id'], ENT_QUOTES, 'UTF-8');
            $kk_name = htmlspecialchars($row['kk_name'], ENT_QUOTES, 'UTF-8');
            $text .= "$no. *$code_id* - $kk_name\n";
            $no++;
        }
        $text .= "\n━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "📊 Total: " . count($data) . " KK\n";
    } else {
        $text .= "❌ Tidak ada data tersedia.\n";
    }
    
    $text .= "\n_Pesan Otomatis dari System_";
} catch (PDOException $e) {
    // Error handling untuk database
    $text = "❌ *Error*\n\n";
    $text .= "Terjadi kesalahan saat mengambil data KK.\n";
    $text .= "Silakan coba lagi nanti.\n\n";
    $text .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_kk.php: " . $e->getMessage());
} catch (Exception $e) {
    // Error handling umum
    $text = "❌ *Error*\n\n";
    $text .= "Terjadi kesalahan pada sistem.\n";
    $text .= "Silakan coba lagi nanti.\n\n";
    $text .= "_- Pesan Otomatis dari System -_";
    error_log("Error in ambil_data_kk.php: " . $e->getMessage());
}

// Bersihkan output buffer dan set header
ob_end_clean();
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
echo $text;
exit;
?>
