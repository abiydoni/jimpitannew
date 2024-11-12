<?php
include 'db.php';

$code_id = $_GET['code_id'];
$stmt = $pdo->prepare("DELETE FROM master_kk WHERE code_id = ?");
$stmt->execute([$code_id]);

header("Location: ../kk.php"); // Redirect setelah berhasil
?>