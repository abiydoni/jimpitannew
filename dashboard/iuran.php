<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$kode_tarif = isset($_GET['kode_tarif']) ? $_GET['kode_tarif'] : null;
$nikk = isset($_GET['nikk']) ? $_GET['nikk'] : null;

// Ambil data tarif terlebih dahulu
$tarif = $pdo->query("SELECT * FROM tb_tarif ORDER BY kode_tarif")->fetchAll(PDO::FETCH_ASSOC);

// Filter tarif, hilangkan Jimpitan (TR001)
$tarif = array_filter($tarif, function($t) { return $t['kode_tarif'] !== 'TR001'; });
$tarif_map = [];
foreach ($tarif as $t) {
    $tarif_map[$t['kode_tarif']] = $t;
}

// Proses pembayaran
$notif = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    if ($_POST['aksi'] === 'bayar') {
        $nikk_bayar = $_POST['nikk'];
        $kode_tarif_bayar = $_POST['kode_tarif'];
        $periode = $_POST['periode'];
        $jumlah_bayar = intval($_POST['jumlah']); // input user, untuk jml_bayar
        if (strpos($periode, '-') !== false) {
            [$bulan, $tahun_bayar] = explode('-', $periode);
        } else {
            $tahun_bayar = $periode;
            // Untuk tahunan, bulan diisi null
            $bulan = null;
        }
        // Ambil tarif tagihan dari tb_tarif
        $jumlah_tagihan = isset($tarif_map[$kode_tarif_bayar]) ? intval($tarif_map[$kode_tarif_bayar]['tarif']) : 0;
        try {
            $stmt = $pdo->prepare("INSERT INTO tb_iuran (nikk, kode_tarif, bulan, tahun, jumlah, jml_bayar, status, tgl_bayar) VALUES (?, ?, ?, ?, ?, ?, 'Cicil', NOW())");
            $stmt->execute([$nikk_bayar, $kode_tarif_bayar, $bulan, $tahun_bayar, $jumlah_tagihan, $jumlah_bayar]);
            $notif = ['type' => 'success', 'msg' => 'Pembayaran berhasil disimpan!'];
        } catch (Exception $e) {
            $notif = ['type' => 'error', 'msg' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()];
        }
    } elseif ($_POST['aksi'] === 'batal') {
        $nikk_batal = $_POST['nikk'];
        $kode_tarif_batal = $_POST['kode_tarif'];
        $periode_batal = $_POST['periode'];
        $jumlah_batal = intval($_POST['jumlah_batal']); // Jumlah yang akan dibatalkan
        
        if (strpos($periode_batal, '-') !== false) {
            [$bulan_batal, $tahun_batal] = explode('-', $periode_batal);
        } else {
            $tahun_batal = $periode_batal;
            $bulan_batal = null;
        }
        
        try {
            // Ambil semua pembayaran untuk KK, tarif, dan periode ini, urutkan dari yang terbaru
            if ($bulan_batal) {
                $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? ORDER BY tgl_bayar DESC");
                $stmt->execute([$nikk_batal, $kode_tarif_batal, $bulan_batal, $tahun_batal]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND (bulan IS NULL OR bulan = '') ORDER BY tgl_bayar DESC");
                $stmt->execute([$nikk_batal, $kode_tarif_batal, $tahun_batal]);
            }
            
            $pembayaran_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $sisa_batal = $jumlah_batal;
            $dihapus = 0;
            $riwayat_batal = [];
            
            // Hapus pembayaran mulai dari yang terbaru (terakhir)
            foreach ($pembayaran_list as $index => $pembayaran) {
                if ($sisa_batal <= 0) break;
                
                $jumlah_pembayaran = intval($pembayaran['jml_bayar']);
                $tgl_pembayaran = $pembayaran['tgl_bayar'];
                $nomor_pembayaran = count($pembayaran_list) - $index; // Nomor pembayaran (3, 2, 1)
                
                if ($jumlah_pembayaran <= $sisa_batal) {
                    // Hapus seluruh pembayaran ini menggunakan kombinasi field
                    if ($bulan_batal) {
                        $stmt_hapus = $pdo->prepare("DELETE FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
                        $stmt_hapus->execute([$nikk_batal, $kode_tarif_batal, $bulan_batal, $tahun_batal, $jumlah_pembayaran, $tgl_pembayaran]);
                    } else {
                        $stmt_hapus = $pdo->prepare("DELETE FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND (bulan IS NULL OR bulan = '') AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
                        $stmt_hapus->execute([$nikk_batal, $kode_tarif_batal, $tahun_batal, $jumlah_pembayaran, $tgl_pembayaran]);
                    }
                    $sisa_batal -= $jumlah_pembayaran;
                    $dihapus += $jumlah_pembayaran;
                    $riwayat_batal[] = "Pembayaran ke-$nomor_pembayaran (Rp" . number_format($jumlah_pembayaran, 0, ',', '.') . ") - Dihapus";
                } else {
                    // Kurangi jumlah pembayaran ini menggunakan kombinasi field
                    $sisa_pembayaran = $jumlah_pembayaran - $sisa_batal;
                    if ($bulan_batal) {
                        $stmt_update = $pdo->prepare("UPDATE tb_iuran SET jml_bayar = ? WHERE nikk = ? AND kode_tarif = ? AND bulan = ? AND tahun = ? AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
                        $stmt_update->execute([$sisa_pembayaran, $nikk_batal, $kode_tarif_batal, $bulan_batal, $tahun_batal, $jumlah_pembayaran, $tgl_pembayaran]);
                    } else {
                        $stmt_update = $pdo->prepare("UPDATE tb_iuran SET jml_bayar = ? WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND (bulan IS NULL OR bulan = '') AND jml_bayar = ? AND tgl_bayar = ? LIMIT 1");
                        $stmt_update->execute([$sisa_pembayaran, $nikk_batal, $kode_tarif_batal, $tahun_batal, $jumlah_pembayaran, $tgl_pembayaran]);
                    }
                    $dihapus += $sisa_batal;
                    $riwayat_batal[] = "Pembayaran ke-$nomor_pembayaran (Rp" . number_format($sisa_batal, 0, ',', '.') . " dari Rp" . number_format($jumlah_pembayaran, 0, ',', '.') . ") - Dikurangi";
                    $sisa_batal = 0;
                }
            }
            
            $pesan_riwayat = implode(", ", $riwayat_batal);
            $notif = ['type' => 'success', 'msg' => 'Berhasil membatalkan pembayaran sebesar Rp' . number_format($dihapus, 0, ',', '.') . '. ' . $pesan_riwayat];
        } catch (Exception $e) {
            $notif = ['type' => 'error', 'msg' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()];
        }
    }
}

// Buat array bulanan dan tahunan berdasarkan metode
$bulanan = [];
$tahunan = [];
foreach ($tarif as $t) {
    if ($t['metode'] == '1') {
        $bulanan[] = $t['kode_tarif'];
    } elseif ($t['metode'] == '2') {
        $tahunan[] = $t['kode_tarif'];
    }
}

// Ambil semua KK
$kk = $pdo->query("SELECT nikk, nama FROM tb_warga WHERE hubungan='Kepala Keluarga' ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua pembayaran tahun ini
$pembayaran = $pdo->query("SELECT * FROM tb_iuran WHERE tahun='$tahun'")->fetchAll(PDO::FETCH_ASSOC);

// Debug: Query khusus untuk tarif tahunan
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<div style='background: #e0ffe0; padding: 10px; margin: 10px; border: 1px solid #00cc00;'>";
    echo "<h3>Query Debug:</h3>";
    echo "<p>Tahun yang dipilih: $tahun</p>";
    
    // Cek tarif tahunan
    $tahunan_tarif = $pdo->query("SELECT kode_tarif FROM tb_tarif WHERE metode = '2'")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tarif tahunan: " . implode(', ', $tahunan_tarif) . "</p>";
    
    // Cek semua data pembayaran untuk tahun ini
    echo "<p>Total pembayaran tahun $tahun: " . count($pembayaran) . " records</p>";
    
    // Tampilkan SQL yang digunakan
    echo "<p>SQL yang digunakan: SELECT * FROM tb_iuran WHERE tahun='$tahun'</p>";
    
    // Cek data pembayaran untuk tarif tahunan
    echo "<h4>Data pembayaran untuk tarif tahunan:</h4>";
    foreach ($tahunan_tarif as $kode) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tb_iuran WHERE kode_tarif = ? AND tahun = ?");
        $stmt->execute([$kode, $tahun]);
        $count = $stmt->fetchColumn();
        echo "<p>Pembayaran untuk $kode tahun $tahun: $count records</p>";
        
        if ($count > 0) {
            $stmt = $pdo->prepare("SELECT * FROM tb_iuran WHERE kode_tarif = ? AND tahun = ?");
            $stmt->execute([$kode, $tahun]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>Detail pembayaran $kode:</p>";
            foreach ($data as $d) {
                echo "<p>  - NIKK: {$d['nikk']}, Bulan: " . ($d['bulan'] ?: 'NULL') . ", Jumlah: {$d['jml_bayar']}</p>";
            }
        }
    }
    
    // Cek apakah ada data pembayaran untuk tarif tahunan di tahun lain
    echo "<h4>Cek data tarif tahunan di tahun lain:</h4>";
    foreach ($tahunan_tarif as $kode) {
        $stmt = $pdo->prepare("SELECT DISTINCT tahun FROM tb_iuran WHERE kode_tarif = ? ORDER BY tahun");
        $stmt->execute([$kode]);
        $tahun_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Tarif $kode ada di tahun: " . implode(', ', $tahun_list) . "</p>";
    }
    
    // Tampilkan SQL untuk cek manual
    echo "<h4>SQL untuk cek manual:</h4>";
    echo "<p>Untuk cek data tarif tahunan di tahun $tahun:</p>";
    echo "<code>SELECT * FROM tb_iuran WHERE kode_tarif IN (SELECT kode_tarif FROM tb_tarif WHERE metode = '2') AND tahun = '$tahun';</code>";
    
    echo "</div>";
}

$pembayaran_map = [];
foreach ($pembayaran as $p) {
    // Untuk tarif bulanan: periode = bulan-tahun
    // Untuk tarif tahunan: periode = tahun saja (bulan bisa null atau kosong)
    if ($p['bulan'] && !empty($p['bulan']) && $p['bulan'] != 'NULL') {
        $periode = $p['bulan'].'-'.$p['tahun'];
    } else {
        $periode = $p['tahun'];
    }
    $pembayaran_map[$p['nikk']][$p['kode_tarif']][$periode][] = $p;
}

// Debug: Tampilkan hasil mapping
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<div style='background: #e0e0ff; padding: 10px; margin: 10px; border: 1px solid #0000cc;'>";
    echo "<h3>Payment Map Result:</h3>";
    
    // Cek tarif tahunan khusus
    $tahunan_tarif = $pdo->query("SELECT kode_tarif FROM tb_tarif WHERE metode = '2'")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tarif tahunan: " . implode(', ', $tahunan_tarif) . "</p>";
    
    // Debug mapping untuk tarif tahunan
    echo "<h4>Debug Mapping untuk Tarif Tahunan:</h4>";
    foreach ($pembayaran as $p) {
        if (in_array($p['kode_tarif'], $tahunan_tarif)) {
            $periode = ($p['bulan'] && !empty($p['bulan']) && $p['bulan'] != 'NULL') ? $p['bulan'].'-'.$p['tahun'] : $p['tahun'];
            echo "<p>Data: NIKK={$p['nikk']}, Kode={$p['kode_tarif']}, Bulan=" . ($p['bulan'] ?: 'NULL') . ", Tahun={$p['tahun']}, Periode=$periode, Jumlah={$p['jml_bayar']}</p>";
        }
    }
    
    foreach ($pembayaran_map as $nikk => $tarif_data) {
        echo "<p>KK: $nikk</p>";
        foreach ($tarif_data as $kode => $periode_data) {
            echo "<p>  Tarif: $kode</p>";
            foreach ($periode_data as $periode => $pembayaran_list) {
                echo "<p>    Periode: $periode (" . count($pembayaran_list) . " pembayaran)</p>";
                $total = 0;
                foreach ($pembayaran_list as $p) {
                    $total += intval($p['jml_bayar']);
                    echo "<p>      - jml_bayar: {$p['jml_bayar']}, bulan: " . ($p['bulan'] ?: 'NULL') . "</p>";
                }
                echo "<p>      Total untuk periode $periode: $total</p>";
            }
        }
    }
    
    // Cek khusus untuk tarif tahunan
    if ($kode_tarif && in_array($kode_tarif, $tahunan_tarif)) {
        echo "<h4>Debug Tarif Tahunan $kode_tarif:</h4>";
        foreach ($pembayaran_map as $nikk => $tarif_data) {
            if (isset($tarif_data[$kode_tarif])) {
                echo "<p>KK $nikk memiliki pembayaran untuk $kode_tarif:</p>";
                foreach ($tarif_data[$kode_tarif] as $periode => $pembayaran_list) {
                    echo "<p>  Periode $periode: " . count($pembayaran_list) . " pembayaran</p>";
                    $total = 0;
                    foreach ($pembayaran_list as $p) {
                        $total += intval($p['jml_bayar']);
                        echo "<p>    jml_bayar: {$p['jml_bayar']}</p>";
                    }
                    echo "<p>  Total: $total</p>";
                }
            } else {
                echo "<p>KK $nikk TIDAK memiliki pembayaran untuk $kode_tarif</p>";
            }
        }
    }
    
    echo "</div>";
}

// Debug: Tampilkan informasi pembayaran jika diperlukan
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
    echo "<h3>Debug Info:</h3>";
    echo "<p>Total pembayaran: " . count($pembayaran) . "</p>";
    echo "<p>Tahun yang dipilih: $tahun</p>";
    if ($kode_tarif) {
        echo "<p>Kode tarif: $kode_tarif</p>";
        echo "<p>Metode tarif: " . $tarif_map[$kode_tarif]['metode'] . " (" . ($tarif_map[$kode_tarif]['metode'] == '1' ? 'Bulanan' : 'Tahunan') . ")</p>";
    }
    
    // Periksa struktur tabel
    try {
        $stmt = $pdo->query("DESCRIBE tb_iuran");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Struktur tabel tb_iuran:</p><ul>";
        foreach ($columns as $col) {
            echo "<li>{$col['Field']} - {$col['Type']}</li>";
        }
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p>Error checking table structure: " . $e->getMessage() . "</p>";
    }
    
    // Tampilkan beberapa data pembayaran
    if (count($pembayaran) > 0) {
        echo "<p>Sample pembayaran data:</p>";
        echo "<pre>" . print_r(array_slice($pembayaran, 0, 3), true) . "</pre>";
    }
    
    // Tampilkan pembayaran untuk tarif tertentu
    if ($kode_tarif) {
        echo "<p>Pembayaran untuk tarif $kode_tarif:</p>";
        if (isset($pembayaran_map)) {
            foreach ($pembayaran_map as $nikk_kk => $tarif_data) {
                if (isset($tarif_data[$kode_tarif])) {
                    echo "<p>KK $nikk_kk:</p>";
                    foreach ($tarif_data[$kode_tarif] as $periode => $pembayaran_list) {
                        echo "<p>  Periode $periode: " . count($pembayaran_list) . " pembayaran</p>";
                        foreach ($pembayaran_list as $p) {
                            echo "<p>    - jml_bayar: " . $p['jml_bayar'] . ", bulan: " . ($p['bulan'] ?: 'NULL') . ", tahun: " . $p['tahun'] . "</p>";
                        }
                    }
                }
            }
        }
    }
    
    echo "<p>Pembayaran map: " . print_r($pembayaran_map, true) . "</p>";
    echo "</div>";
}

// Debug: Tampilkan semua data pembayaran mentah
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "<div style='background: #ffe0e0; padding: 10px; margin: 10px; border: 1px solid #cc0000;'>";
    echo "<h3>Raw Payment Data:</h3>";
    echo "<p>Total records: " . count($pembayaran) . "</p>";
    foreach ($pembayaran as $p) {
        echo "<p>NIKK: {$p['nikk']}, Kode: {$p['kode_tarif']}, Bulan: " . ($p['bulan'] ?: 'NULL') . ", Tahun: {$p['tahun']}, Jumlah: {$p['jml_bayar']}</p>";
    }
    echo "</div>";
}

$tahun_opsi = range(date('Y')-2, date('Y')+2);

// Icon untuk tiap jenis iuran (tanpa Jimpitan)
$icon_map = [
    'TR002' => '🏠', // Wajib
    'TR003' => '🤝', // Sosial
    'TR004' => '🎉', // 17an
    'TR005' => '🌾', // Merti Du
    'TR006' => '💵', // Kas
];

// Jika kode_tarif=TR001 di URL, redirect ke halaman utama iuran.php
if ($kode_tarif === 'TR001') {
    header('Location: iuran.php');
    exit;
}
?>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Iuran Warga</h1>
    <form method="GET" class="flex items-center gap-2">
      <?php if($kode_tarif): ?>
        <input type="hidden" name="kode_tarif" value="<?= htmlspecialchars($kode_tarif) ?>">
      <?php endif; ?>
      <label for="tahun" class="font-semibold">Tahun:</label>
      <select name="tahun" id="tahun" class="border rounded p-1" onchange="this.form.submit()">
        <?php foreach($tahun_opsi as $th): ?>
          <option value="<?= $th ?>" <?= $th==$tahun?'selected':'' ?>><?= $th ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <?php if(!$kode_tarif): ?>
    <!-- Pilihan Jenis Iuran: Menu Box Besar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-8">
      <?php foreach($tarif as $t): ?>
        <a href="?kode_tarif=<?= urlencode($t['kode_tarif']) ?>&tahun=<?= $tahun ?>" class="block bg-blue-50 border border-blue-200 rounded-lg shadow hover:shadow-lg hover:bg-blue-100 transition p-6 text-center cursor-pointer">
          <div class="text-5xl mb-2"><?= $icon_map[$t['kode_tarif']] ?? '💳' ?></div>
          <div class="text-lg font-bold mb-1"><?= htmlspecialchars($t['nama_tarif']) ?></div>
          <div class="text-gray-600">Rp<?= number_format($t['tarif'],0,',','.') ?><?= $t['metode'] == '1' ? '/bulan' : '/tahun' ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php elseif(!$nikk): ?>
    <!-- Tabel Rekap per KK untuk Jenis Iuran Terpilih -->
    <div class="mb-4">
      <a href="iuran.php?tahun=<?= $tahun ?>" class="text-blue-600 hover:underline">&larr; Kembali ke menu iuran</a>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">No KK</th>
            <th class="px-2 py-1 border">Nama KK</th>
            <th class="px-2 py-1 border">Total Tagihan</th>
            <th class="px-2 py-1 border">Sudah Bayar</th>
            <th class="px-2 py-1 border">Sisa Hutang</th>
            <th class="px-2 py-1 border">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
          $periode_list = $is_bulanan ? [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
          ] : [$tahun];
          foreach($kk as $w):
            $total_tagihan = 0;
            $total_bayar = 0;
            foreach($periode_list as $periode) {
              $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
              $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
              $total_tagihan += $tarif_nom;
              
              // Debug: Tampilkan perhitungan untuk setiap periode
              if (isset($_GET['debug']) && $_GET['debug'] == '1') {
                echo "<div style='background: #ffffe0; padding: 5px; margin: 2px; border: 1px solid #cccc00; font-size: 10px;'>";
                echo "Perhitungan: KK={$w['nikk']}, Periode=$periode, PeriodeKey=$periode_key, Tarif=$tarif_nom<br>";
                echo "Tahun yang dipilih: $tahun<br>";
                echo "Pembayaran map check: " . (isset($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key]) ? 'ADA' : 'TIDAK ADA') . "<br>";
                if (isset($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key])) {
                  echo "Jumlah pembayaran: " . count($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key]) . "<br>";
                  foreach ($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key] as $p) {
                    echo "  - jml_bayar: {$p['jml_bayar']}, tahun: {$p['tahun']}<br>";
                  }
                }
                echo "</div>";
              }
              
              if (isset($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key])) {
                foreach ($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key] as $p) {
                  $total_bayar += intval($p['jml_bayar']);
                }
              }
            }
            
            // Ambil total bayar langsung dari database untuk memastikan akurasi
            $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ?");
            $stmt_total->execute([$w['nikk'], $kode_tarif, $tahun]);
            $total_bayar_db = intval($stmt_total->fetchColumn());
            
            // Gunakan total dari database jika lebih besar dari 0
            if ($total_bayar_db > 0) {
                $total_bayar = $total_bayar_db;
            }
            
            $sisa = $total_tagihan - $total_bayar;
            $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
          ?>
          <tr class="hover:bg-gray-100">
            <td class="px-2 py-1 border"><?= htmlspecialchars($w['nikk']) ?></td>
            <td class="px-2 py-1 border">
              <a href="?kode_tarif=<?= urlencode($kode_tarif) ?>&tahun=<?= $tahun ?>&nikk=<?= urlencode($w['nikk']) ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                <?= htmlspecialchars($w['nama']) ?>
              </a>
            </td>
            <td class="px-2 py-1 border">Rp<?= number_format($total_tagihan,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($total_bayar,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format(max($sisa,0),0,',','.') ?></td>
            <td class="px-2 py-1 border font-semibold <?= $status=='Lunas'?'text-green-600':($total_bayar > 0 ? 'text-orange-600' : 'text-red-600') ?>"><?= $status ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <!-- Tabel Detail per Bulan/Tahun untuk KK Terpilih -->
    <div class="mb-4 flex items-center gap-2">
      <a href="?kode_tarif=<?= urlencode($kode_tarif) ?>&tahun=<?= $tahun ?>" class="text-blue-600 hover:underline">&larr; Kembali ke rekap KK</a>
      <span class="font-semibold">|
        <?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?> -
        <?= htmlspecialchars($nikk) ?>
        (<?= htmlspecialchars($pdo->query("SELECT nama FROM tb_warga WHERE nikk='$nikk' AND hubungan='Kepala Keluarga' LIMIT 1")->fetchColumn()) ?>)
      </span>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">Periode</th>
            <th class="px-2 py-1 border">Tarif</th>
            <th class="px-2 py-1 border">Sudah Bayar</th>
            <th class="px-2 py-1 border">Sisa Hutang</th>
            <th class="px-2 py-1 border">Status</th>
            <th class="px-2 py-1 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
          $periode_list = $is_bulanan ? [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
          ] : [$tahun];
          foreach($periode_list as $periode) {
            $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
            $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
            $total_bayar = 0;
            if (isset($pembayaran_map[$nikk][$kode_tarif][$periode_key])) {
              foreach ($pembayaran_map[$nikk][$kode_tarif][$periode_key] as $p) {
                $total_bayar += intval($p['jml_bayar']);
              }
            }
            
            // Untuk tarif tahunan, ambil total bayar untuk tahun tersebut
            // Untuk tarif bulanan, ambil total bayar untuk periode tersebut
            if (!$is_bulanan) {
                // Ambil total bayar langsung dari database untuk tarif tahunan
                $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ?");
                $stmt_total->execute([$nikk, $kode_tarif, $tahun]);
                $total_bayar_db = intval($stmt_total->fetchColumn());
                
                // Gunakan total dari database jika lebih besar dari 0
                if ($total_bayar_db > 0) {
                    $total_bayar = $total_bayar_db;
                }
            }
            
            $sisa = $tarif_nom - $total_bayar;
            $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
          ?>
          <tr class="hover:bg-gray-100">
            <td class="px-2 py-1 border"><?= $is_bulanan ? $periode.' '.$tahun : $tahun ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($tarif_nom,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format($total_bayar,0,',','.') ?></td>
            <td class="px-2 py-1 border">Rp<?= number_format(max($sisa,0),0,',','.') ?></td>
            <td class="px-2 py-1 border font-semibold <?= $status=='Lunas'?'text-green-600':($total_bayar > 0 ? 'text-orange-600' : 'text-red-600') ?>"><?= $status ?></td>
            <td class="px-2 py-1 border">
              <?php if($status=='Belum Lunas'): ?>
                <div class="flex space-x-1">
                  <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs" onclick="openBayarModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>',<?= $sisa ?>)">Bayar</button>
                  <?php if($total_bayar > 0): ?>
                    <button class="bg-red-600 text-white px-2 py-1 rounded text-xs" onclick="openHistoriModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>')">Histori</button>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <?php if($total_bayar > 0): ?>
                  <button class="bg-red-600 text-white px-2 py-1 rounded text-xs" onclick="openHistoriModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>')">Histori</button>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Bayar -->
<div id="bayarModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
  <div class="bg-white p-4 rounded shadow-lg w-full max-w-sm">
    <h2 class="text-lg font-bold mb-2">Pembayaran Iuran</h2>
    <form method="POST" id="formBayar">
      <input type="hidden" name="aksi" value="bayar">
      <input type="hidden" name="nikk" id="modalNikk">
      <input type="hidden" name="kode_tarif" id="modalKodeTarif">
      <input type="hidden" name="periode" id="modalPeriode">
      <div class="mb-2">
        <label class="block mb-1">Nama Iuran</label>
        <input type="text" id="modalNamaTarif" class="w-full border rounded p-1 bg-gray-100" readonly>
      </div>
      <div class="mb-2">
        <label class="block mb-1">Jumlah Bayar (Rp)</label>
        <input type="number" name="jumlah" id="modalJumlah" class="w-full border rounded p-1" min="1" required>
      </div>
      <div class="flex justify-end">
        <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded mr-2" onclick="toggleModal('bayarModal')">Tutup</button>
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Histori Pembayaran -->
<div id="historiModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
  <div class="bg-white p-4 rounded shadow-lg w-full max-w-4xl max-h-[80vh] overflow-y-auto">
    <h2 class="text-lg font-bold mb-2">Histori Pembayaran</h2>
    <div class="mb-4">
      <p class="font-semibold" id="historiNamaTarif"></p>
      <p class="text-sm text-gray-600" id="historiPeriodeText"></p>
    </div>
    <div id="historiTableContainer">
      <!-- Tabel histori akan diisi melalui AJAX -->
    </div>
    <div class="flex justify-end mt-4">
      <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded" onclick="toggleModal('historiModal')">Tutup</button>
    </div>
  </div>
</div>

<?php if ($notif): ?>
<script>
  Swal.fire({
    icon: "<?= $notif['type'] ?>",
    title: "<?= $notif['type'] === 'success' ? 'Sukses' : 'Gagal' ?>",
    text: "<?= addslashes($notif['msg']) ?>",
    timer: 3000,
    timerProgressBar: true,
    showConfirmButton: false,
    position: 'top-end',
    toast: true
  });
</script>
<?php endif; ?>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if ((sep.length > 0)) {
        var i = s[0].length;
        if (i % 3 !== 0) {
            i = 3 - i % 3;
        }
        s[0] = s[0].padStart(s[0].length + i, '0');
        s[0] = s[0].match(/.{3}/g).join(sep);
    }
    if ((prec > 0) && (s[1] || dec.length > 1)) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function openBayarModal(nikk, kode_tarif, periode, nama_tarif, sisa) {
    document.getElementById('modalNikk').value = nikk;
    document.getElementById('modalKodeTarif').value = kode_tarif;
    document.getElementById('modalPeriode').value = periode;
    document.getElementById('modalNamaTarif').value = nama_tarif;
    document.getElementById('modalJumlah').value = sisa;
    document.getElementById('modalJumlah').max = sisa;
    toggleModal('bayarModal');
}

function openHistoriModal(nikk, kode_tarif, periode, nama_tarif) {
    document.getElementById('historiNamaTarif').textContent = nama_tarif;
    document.getElementById('historiPeriodeText').textContent = 'Periode: ' + periode;
    
    // Ambil data histori pembayaran
    fetch('api/get_histori_pembayaran.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nikk=' + encodeURIComponent(nikk) + '&kode_tarif=' + encodeURIComponent(kode_tarif) + '&periode=' + encodeURIComponent(periode)
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('historiTableContainer').innerHTML = data;
        toggleModal('historiModal');
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal mengambil data histori pembayaran',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    });
}

function hapusPembayaran(nikk, kode_tarif, bulan, tahun, jml_bayar, tgl_bayar) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus pembayaran ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Menghapus pembayaran:', {nikk, kode_tarif, bulan, tahun, jml_bayar, tgl_bayar});
            
            const formData = new FormData();
            formData.append('nikk', nikk);
            formData.append('kode_tarif', kode_tarif);
            formData.append('bulan', bulan);
            formData.append('tahun', tahun);
            formData.append('jml_bayar', jml_bayar);
            formData.append('tgl_bayar', tgl_bayar);
            
            fetch('api/hapus_pembayaran.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pembayaran berhasil dihapus',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    }).then(() => {
                        location.reload(); // Reload halaman untuk memperbarui data
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menghapus pembayaran: ' + data.message,
                        timer: 4000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghapus pembayaran',
                    timer: 4000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            });
        }
    });
}
</script>

<?php include 'footer.php'; ?> 