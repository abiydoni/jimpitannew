<?php
// Ambil konfigurasi dari database
include 'get_konfigurasi.php';

$groupId = get_konfigurasi('group_id2');
$gatewayBase = get_konfigurasi('url_group');
$filePesan = get_konfigurasi('report3');

// Ambil pesan dari file jika ada
$message = '';
if (!empty($filePesan)) {
    if (!file_exists($filePesan)) {
        $filePesan = __DIR__ . '/' . $filePesan;
    }
    if (file_exists($filePesan)) {
        include $filePesan;
        $message = isset($pesan) ? trim((string)$pesan) : '';
    }
}

// Jika pesan kosong, gunakan pesan default
if (empty($message)) {
    $message = 'Test pesan - ' . date('Y-m-d H:i:s');
}

// Validasi
if (empty($groupId)) {
    die("ERROR: Group ID kosong!\n");
}
if (empty($gatewayBase)) {
    die("ERROR: URL gateway kosong!\n");
}

// Bangun URL - jika sudah ada endpoint, jangan tambahkan lagi
$gatewayBase = rtrim($gatewayBase, '/');
if (strpos($gatewayBase, '/send-group-message') === false) {
    $gatewayUrl = $gatewayBase . '/send-group-message';
} else {
    $gatewayUrl = $gatewayBase;
}
$data = [
    'id' => $groupId,
    'message' => $message
];

$ch = curl_init($gatewayUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
// Jangan force IPv4, biarkan CURL pilih sendiri
// curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
$curlErrno = curl_errno($ch);
curl_close($ch);

// Output hasil
echo "=== Hasil Pengiriman ===\n";
echo "URL: $gatewayUrl\n";
echo "HTTP Code: $httpCode\n";

if ($httpCode == 0) {
    echo "❌ ERROR: Tidak bisa connect ke wagateway!\n";
    echo "CURL Error: " . ($curlError ?: 'Connection failed') . "\n";
    echo "CURL Errno: $curlErrno\n";
    echo "\nKemungkinan penyebab:\n";
    echo "1. Wagateway belum running (jalankan: npm start di folder WAGATEWAY)\n";
    echo "2. URL salah (cek: $gatewayBase)\n";
    echo "3. Port berbeda (default: 8000)\n";
    echo "4. PHP CURL tidak bisa akses localhost (coba jalankan test_curl.php untuk diagnosa)\n";
    echo "5. Firewall/antivirus memblokir koneksi PHP\n";
} else {
    echo "Response: $result\n";
    
    if ($httpCode == 200) {
        $response = json_decode($result, true);
        if (isset($response['status']) && $response['status']) {
            echo "✅ SUCCESS: Pesan berhasil dikirim!\n";
        } else {
            echo "⚠️  WARNING: HTTP 200 tapi status false\n";
        }
    } else {
        $response = json_decode($result, true);
        $errorMsg = isset($response['message']) ? $response['message'] : 'Unknown error';
        echo "❌ ERROR: $errorMsg\n";
    }
}
?>
