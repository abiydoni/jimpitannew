<?php
// Konfigurasi - GANTI dengan nilai Anda
$groupId = '120363123456789012@g.us'; // Group ID WhatsApp
$gatewayBase = 'http://localhost:8000'; // URL wagateway (tanpa endpoint)
$message = 'Test pesan - ' . date('Y-m-d H:i:s');

// Langsung kirim pesan (endpoint akan return error jika belum ready)

// 3. Kirim pesan jika sudah ready
$gatewayUrl = rtrim($gatewayBase, '/') . '/send-group-message';
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

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Output hasil
echo "=== Hasil Pengiriman ===\n";
echo "URL: $gatewayUrl\n";
echo "HTTP Code: $httpCode\n";

if ($httpCode == 0) {
    echo "❌ ERROR: Tidak bisa connect ke wagateway!\n";
    echo "CURL Error: " . ($curlError ?: 'Connection failed') . "\n";
    echo "\nKemungkinan penyebab:\n";
    echo "1. Wagateway belum running (jalankan: npm start di folder WAGATEWAY)\n";
    echo "2. URL salah (cek: $gatewayBase)\n";
    echo "3. Port berbeda (default: 8000)\n";
    echo "4. Firewall memblokir koneksi\n";
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
