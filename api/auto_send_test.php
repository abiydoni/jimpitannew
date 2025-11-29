<?php
// Hardcode untuk testing
$token = '8582107388:AAHQtI53tspPtZZvj_eHRPKxox8QYqKEl5Y';
$chatId = 8532362380;
$message = "🧪 appsbee Test Message\n\n" . date('Y-m-d H:i:s');

// Kirim ke Telegram
$url = "https://api.telegram.org/bot{$token}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);
?>
