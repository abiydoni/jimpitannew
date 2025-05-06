<?php
session_start();

include 'db.php';
include 'ambil_data_jaga.php';

$groupId = "120363398680818900@g.us";
$message = $pesan; // Jangan di-escape jika ingin kirim pesan asli (tanpa htmlspecialchars)
include 'send-wa-group.php';
?>
