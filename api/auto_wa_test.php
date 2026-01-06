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

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output hasil
echo "=== Hasil Pengiriman ===\n";
echo "HTTP Code: $httpCode\n";
echo "Response: $result\n";
?>
