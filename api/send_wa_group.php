<?php
// Debug mode: tampilkan error langsung
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

$groupList = $_POST['groupId'] ?? [];
if (!is_array($groupList)) {
    $groupList = [$groupList];
}

$pesangroup = $_POST['message'] ?? '';
$pesangroup = trim($pesangroup);

if (empty($groupList) || !$pesangroup) {
    echo json_encode(['error' => 'Group dan pesan wajib diisi']);
    exit;
}

$url = "https://wapi.appsbee.my.id/send-group-message";
$sessionId = '91e37fbd895dedf2587d3f506ce1718e';

$logAll = "";
$successCount = 0;
$errorCount = 0;

$results = []; // Menampung hasil tiap request

foreach ($groupList as $group) {
    $group = trim($group);
    if (!$group) continue;

    $data = [
        'groupId' => $group,
        'message' => $pesangroup
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "x-session-id: $sessionId"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $status = ($httpCode == 200) ? "SUKSES" : "GAGAL";
    if ($status === "SUKSES") {
        $successCount++;
    } else {
        $errorCount++;
    }

    $logEntry = "[" . date("Y-m-d H:i:s") . "] Group: $group | Status: $status ($httpCode)\n";
    $logEntry .= "[DATA] " . json_encode($data) . "\n";
    $logEntry .= "[RESPONSE] " . $response . "\n";
    if ($curlError) {
        $logEntry .= "[CURL ERROR] $curlError\n";
    }
    $logEntry .= str_repeat("-", 60) . "\n";

    $logAll .= $logEntry;

    $results[] = [
        'group' => $group,
        'status' => $status,
        'http_code' => $httpCode,
        'curl_error' => $curlError,
        'response' => $response,
    ];
}

file_put_contents("log-kirim-wa.txt", $logAll, FILE_APPEND);

// Tampilkan hasil debug di browser
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'successCount' => $successCount,
    'errorCount' => $errorCount,
    'results' => $results,
]);
exit;
