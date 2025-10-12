<?php
session_start();
include '../db.php';

$nikk = $_POST['nikk'] ?? '';
$kode_tarif = $_POST['kode_tarif'] ?? '';
$periode = $_POST['periode'] ?? '';

if (empty($nikk) || empty($kode_tarif) || empty($periode)) {
    echo '<p class="text-red-600">Data tidak lengkap</p>';
    exit;
}

// Parse periode
if (strpos($periode, '-') !== false) {
    [$bulan, $tahun] = explode('-', $periode);
    if ($bulan === 'Seumur Hidup') {
        $bulan = 'Selamanya';
    }
} else {
    $tahun = $periode;
    $bulan = null;
}

try {
    // Ambil data histori pembayaran
    if ($bulan) {
        $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? ORDER BY tgl_bayar DESC");
        $stmt->execute([$nikk, $kode_tarif, $bulan, $tahun]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan = 'Tahunan' ORDER BY tgl_bayar DESC");
        $stmt->execute([$nikk, $kode_tarif, $tahun]);
    }
    
    $pembayaran_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($pembayaran_list)) {
        echo '<p class="text-gray-600">Tidak ada data pembayaran</p>';
        exit;
    }
    
    // Tampilkan tabel histori
    echo '<div class="overflow-x-auto">';
    echo '<table class="min-w-full bg-white border rounded shadow text-xs">';
    echo '<thead class="bg-gray-200">';
    echo '<tr>';
    echo '<th class="px-3 py-2 border text-left">No</th>';
    echo '<th class="px-3 py-2 border text-left">Tanggal Bayar</th>';
    echo '<th class="px-3 py-2 border text-left">Jumlah Bayar</th>';
    echo '<th class="px-3 py-2 border text-left">Status</th>';
    echo '<th class="px-3 py-2 border text-center">Aksi</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_bayar = 0;
    foreach ($pembayaran_list as $index => $pembayaran) {
        $total_bayar += intval($pembayaran['jml_bayar']);
        $nomor = $index + 1;
        
        echo '<tr class="hover:bg-gray-100">';
        echo '<td class="px-3 py-2 border">' . $nomor . '</td>';
        echo '<td class="px-3 py-2 border">' . date('d/m/Y H:i', strtotime($pembayaran['tgl_bayar'])) . '</td>';
        echo '<td class="px-3 py-2 border">Rp' . number_format($pembayaran['jml_bayar'], 0, ',', '.') . '</td>';
        echo '<td class="px-3 py-2 border">' . htmlspecialchars($pembayaran['status']) . '</td>';
        echo '<td class="px-3 py-2 border text-center">';
        echo '<button onclick="hapusPembayaran(\'' . addslashes($pembayaran['id_iuran']) . '\')" class="text-red-600 hover:text-red-800 font-bold py-1 px-1">';
        echo '<i class="bx bx-trash"></i>';
        echo '</button>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    echo '<div class="mt-4 p-3 bg-blue-50 rounded">';
    echo '<p class="font-semibold">Total Pembayaran: Rp' . number_format($total_bayar, 0, ',', '.') . '</p>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<p class="text-red-600">Error: ' . $e->getMessage() . '</p>';
}
?> 