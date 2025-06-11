<?php
// api/wilayah.php
header('Content-Type: application/json');
$type = $_GET['type'] ?? '';
$id   = $_GET['id'] ?? '';

$base = 'https://wilayah.id/api';
switch ($type) {
    case 'provinces':
        $url = "$base/provinces.json"; break;
    case 'regencies':
        $url = "$base/regencies/$id.json"; break;
    case 'districts':
        $url = "$base/districts/$id.json"; break;
    case 'villages':
        $url = "$base/villages/$id.json"; break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type']);
        exit;
}

echo file_get_contents($url);
