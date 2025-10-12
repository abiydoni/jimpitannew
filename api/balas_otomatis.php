<?php
// Terima webhook dari WA Gateway baru dan balas otomatis lewat endpoint /message/send-text

// Cek metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

// Ambil input dari webhook gateway (format sesuai src/webhooks/message.ts)
$payload = json_decode(file_get_contents("php://input"), true);
$fromJid = $payload['from'] ?? '';
$pesanMasuk = strtolower(trim($payload['message'] ?? ''));

if (!$fromJid || $pesanMasuk === '') {
    echo json_encode(['error' => 'from/message kosong']);
    exit;
}

// Khusus bot private: abaikan pesan dari grup
if (substr($fromJid, -5) === '@g.us') {
    $log = "[" . date("Y-m-d H:i:s") . "] Ignored group: $fromJid | In: $pesanMasuk\n";
    file_put_contents("log-balas-wa.txt", $log, FILE_APPEND);
    echo json_encode(['status' => 'ignored', 'reason' => 'group_message']);
    exit;
}

// Koneksi database & konfigurasi
require 'db.php';
require 'get_konfigurasi.php';
$sessionId = get_konfigurasi('session_id');
$gatewayBase = get_konfigurasi('url_group');
$gatewayKey = get_konfigurasi('gateway_key'); // opsional

// Normalisasi endpoint
$apiUrl = rtrim($gatewayBase, '/');
if (stripos($apiUrl, '/message/send-text') === false) {
    $apiUrl .= '/message/send-text';
}

// ===== Logika Bot Berbasis tb_botmenu ===== (sama seperti proyek bot-whatsapp)
function get_menu(PDO $pdo, $parentId = null) {
    if ($parentId === null) {
        $stmt = $pdo->prepare("SELECT keyword, description FROM tb_botmenu WHERE parent_id IS NULL ORDER BY keyword ASC");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("SELECT keyword, description FROM tb_botmenu WHERE parent_id = :pid ORDER BY keyword ASC");
        $stmt->execute([':pid' => $parentId]);
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) return null;
    $text = "📋 *Menu*\n";
    foreach ($rows as $row) {
        $text .= $row['keyword'] . ". " . $row['description'] . "\n";
    }
    $text .= "\nBalas dengan nomor yang diinginkan (contoh: 2 atau 31).";
    return $text;
}

function fetch_url_text($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($http >= 200 && $http < 300 && $resp) return trim($resp);
    return null;
}

$pesanBalasan = "Maaf, perintah tidak dikenali. Ketik *menu* untuk melihat daftar.";

if ($pesanMasuk === 'menu' || $pesanMasuk === 'help') {
    $menu = get_menu($pdo, null);
    if ($menu) $pesanBalasan = $menu;
} else if (preg_match('/^\d{1,3}$/', $pesanMasuk)) {
    $stmt = $pdo->prepare("SELECT id, url FROM tb_botmenu WHERE keyword = :kw LIMIT 1");
    $stmt->execute([':kw' => (int)$pesanMasuk]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($item) {
        if (!empty($item['url']) && stripos($item['url'], 'Masih dalam pengembangan') === false) {
            $content = fetch_url_text($item['url']);
            $pesanBalasan = ($content !== null && $content !== '') ? $content : "Maaf, data tidak tersedia untuk saat ini.";
        } else {
            $submenu = get_menu($pdo, (int)$item['id']);
            $pesanBalasan = $submenu ?: "Belum ada data/menu untuk pilihan tersebut.";
        }
    }
}

// Tentukan tujuan balasan
$isGroup = false;
$to = '';
if (substr($fromJid, -5) === '@g.us') {
    $isGroup = true;
    $to = $fromJid; // balas ke grup yang sama
} else {
    $digits = preg_replace('/\D+/', '', $fromJid);
    if (!$digits) {
        echo json_encode(['error' => 'Nomor tujuan tidak valid']);
        exit;
    }
    $to = $digits; // format 62...
}

// Kirim ke WA Gateway
$body = [
    'session' => $sessionId,
    'to' => $to,
    'text' => $pesanBalasan,
    'is_group' => $isGroup,
];

$headers = ['Content-Type: application/json'];
if (!empty($gatewayKey)) {
    $headers[] = 'key: ' . $gatewayKey;
}

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log
$log = "[" . date("Y-m-d H:i:s") . "] From: $fromJid | In: $pesanMasuk | Reply: $pesanBalasan | Status: " . ($httpCode == 200 ? "SUKSES" : "GAGAL ($httpCode)") . "\n";
file_put_contents("log-balas-wa.txt", $log, FILE_APPEND);

echo json_encode(['status' => ($httpCode == 200 ? 'success' : 'fail'), 'message' => $pesanBalasan]);
exit;
