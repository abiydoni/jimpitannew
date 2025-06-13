<?php
include 'db.php';

$id = $_POST['id'] ?? 0;
$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

$allowed = ['value', 'keterangan'];
if (!in_array($field, $allowed)) {
    http_response_code(400);
    exit('Field tidak valid.');
}

$stmt = $pdo->prepare("UPDATE tb_konfigurasi_bot SET $field = :value WHERE id = :id");
$stmt->execute(['value' => $value, 'id' => $id]);
echo 'Berhasil diupdate';
