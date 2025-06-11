<?php
require 'db.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM tb_warga WHERE id_warga = ?");
$stmt->execute([$id]);
$r = $stmt->fetch();

echo "<h2>Biodata Warga</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><td>Nama</td><td>{$r['nama']}</td></tr>";
echo "<tr><td>NIK</td><td>{$r['nik']}</td></tr>";
echo "<tr><td>Alamat</td><td>{$r['alamat']}, RT {$r['rt']}/RW {$r['rw']}, {$r['kelurahan']}, {$r['kecamatan']}</td></tr>";
echo "<tr><td>Kota</td><td>{$r['kota']}, {$r['propinsi']}</td></tr>";
echo "<tr><td>HP</td><td>{$r['hp']}</td></tr>";
echo "<tr><td>TTL</td><td>{$r['tpt_lahir']}, {$r['tgl_lahir']}</td></tr>";
echo "<tr><td>Agama</td><td>{$r['agama']}</td></tr>";
echo "<tr><td>Status</td><td>{$r['status']}</td></tr>";
echo "<tr><td>Pekerjaan</td><td>{$r['pekerjaan']}</td></tr>";
echo "</table>";
