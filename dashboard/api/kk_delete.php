<?php
include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM master_kk WHERE code_id = ?");
$stmt->execute([$id]);

header("Location: ../index.php"); // Redirect setelah berhasil
?>