<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Gunakan metode POST']);
    exit;
}

$groupList = $_POST['groupId'] ?? []; // ← ini diperbaiki
$pesangroup = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

if (empty($groupList) || !$pesangroup) {
    echo json_encode(['error' => 'Group dan pesan wajib diisi']);
    exit;
}

$url = "https://wapi.appsbee.my.id/send-group-message";
$logAll = "";
$successCount = 0;
$errorCount = 0;

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
        'x-session-id: 91e37fbd895dedf2587d3f506ce1718e'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Logging tambahan untuk debug
    if (curl_errno($ch)) {
        $logAll .= "[" . date("Y-m-d H:i:s") . "] CURL Error: " . curl_error($ch) . "\n";
    } else {
        $logAll .= "[" . date("Y-m-d H:i:s") . "] Group: $group | Status: " . ($httpCode == 200 ? "SUKSES" : "GAGAL") . " ($httpCode)\n";
        $logAll .= "[DATA] " . json_encode($data) . "\n";
        $logAll .= "[RESPONSE] " . $response . "\n";
    }

    curl_close($ch);

    if ($httpCode == 200) {
        $successCount++;
    } else {
        $errorCount++;
    }
}

file_put_contents("log-kirim-wa.txt", $logAll, FILE_APPEND);

if ($successCount > 0 && $errorCount == 0) {
    header('Location: pesan_group.php?status=success&jumlah=' . $successCount);
} elseif ($successCount > 0) {
    header('Location: pesan_group.php?status=partial&berhasil=' . $successCount . '&gagal=' . $errorCount);
} else {
    header('Location: pesan_group.php?status=error');
}
exit;
?>
