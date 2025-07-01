<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$bulan_filter = isset($_GET['bulan']) ? intval($_GET['bulan']) : intval(date('n')); // 1-12 untuk bulan
$kode_tarif = isset($_GET['kode_tarif']) ? $_GET['kode_tarif'] : null;
$nikk = isset($_GET['nikk']) ? $_GET['nikk'] : null;

// Array nama bulan
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Array warna icon untuk box iuran (warna yang sesuai dengan gradient)
$warna_icon = [
    'text-blue-700',
    'text-green-700',
    'text-purple-700',
    'text-orange-700',
    'text-pink-700',
    'text-indigo-700',
    'text-teal-700',
    'text-yellow-700',
    'text-red-700',
    'text-cyan-700',
    'text-emerald-700',
    'text-violet-700',
    'text-amber-700',
    'text-lime-700',
    'text-rose-700',
    'text-sky-700'
];

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
        // Cek metode tarif
        $stmt_metode = $pdo->prepare("SELECT metode FROM tb_tarif WHERE kode_tarif = ?");
        $stmt_metode->execute([$kode_tarif_bayar]);
        $metode_tarif = $stmt_metode->fetchColumn();
        if ($metode_tarif == '3') {
            // Untuk seumur hidup, bulan diisi 'Selamanya', tahun tetap tahun yang dipilih
            $tahun_bayar = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
            $bulan = 'Selamanya';
        } else if (strpos($periode, '-') !== false) {
            [$bulan, $tahun_bayar] = explode('-', $periode);
        } else {
            $tahun_bayar = $periode;
            $bulan = 'Tahunan';
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
    }
    // Hapus sistem batal lama untuk menghindari konflik dengan sistem hapus AJAX
}

// Buat array bulanan dan tahunan berdasarkan metode
$bulanan = [];
$tahunan = [];
$seumurhidup = [];
foreach ($tarif as $t) {
    if ($t['metode'] == '1') {
        $bulanan[] = $t['kode_tarif'];
    } elseif ($t['metode'] == '2') {
        $tahunan[] = $t['kode_tarif'];
    } elseif ($t['metode'] == '3') {
        $seumurhidup[] = $t['kode_tarif'];
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
    // Untuk tarif tahunan: periode = tahun saja (bulan = "Tahunan")
    if ($p['bulan'] && !empty($p['bulan']) && $p['bulan'] != 'NULL' && $p['bulan'] != 'Tahunan') {
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
            $periode = ($p['bulan'] && !empty($p['bulan']) && $p['bulan'] != 'NULL' && $p['bulan'] != 'Tahunan') ? $p['bulan'].'-'.$p['tahun'] : $p['tahun'];
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
        echo "<p>Metode tarif: " . $tarif_map[$kode_tarif]['metode'] . " (" . ($tarif_map[$kode_tarif]['metode'] == '1' ? 'Bulanan' : ($tarif_map[$kode_tarif]['metode'] == '2' ? 'Tahunan' : 'Seumur Hidup')) . ")</p>";
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

// Fungsi untuk menghitung total setoran per bulan dan tahun
function hitungTotalSetoran($pdo, $kode_tarif, $bulan, $tahun) {
    // Cek apakah tarif bulanan atau tahunan
    $stmt = $pdo->prepare("SELECT metode FROM tb_tarif WHERE kode_tarif = ?");
    $stmt->execute([$kode_tarif]);
    $metode = $stmt->fetchColumn();
    
    if ($metode == '1') {
        // Tarif bulanan - hitung berdasarkan tgl_bayar di bulan tertentu dan bulan bukan "Tahunan"
        $stmt = $pdo->prepare("SELECT SUM(jml_bayar) as total FROM tb_iuran WHERE kode_tarif = ? AND MONTH(tgl_bayar) = ? AND YEAR(tgl_bayar) = ? AND bulan != 'Tahunan'");
        $stmt->execute([$kode_tarif, $bulan, $tahun]);
    } else {
        // Tarif tahunan - hitung berdasarkan tgl_bayar di bulan tertentu dan bulan = "Tahunan"
        $stmt = $pdo->prepare("SELECT SUM(jml_bayar) as total FROM tb_iuran WHERE kode_tarif = ? AND MONTH(tgl_bayar) = ? AND YEAR(tgl_bayar) = ? AND bulan = 'Tahunan'");
        $stmt->execute([$kode_tarif, $bulan, $tahun]);
    }
    
    $total = $stmt->fetchColumn();
    return $total ? intval($total) : 0;
}

// Hitung total setoran hanya jika ada jenis iuran yang dipilih
$total_setoran_per_iuran = [];
if ($kode_tarif) {
    $total_setoran_per_iuran[$kode_tarif] = hitungTotalSetoran($pdo, $kode_tarif, $bulan_filter, $tahun);
}

// Icon untuk tiap jenis iuran (tanpa Jimpitan)
// Menggunakan field icon dari database tb_tarif

// Jika metode=0 di URL, redirect ke halaman utama iuran.php
if ($kode_tarif) {
  // Ambil metode dari database
  $stmt = $pdo->prepare("SELECT metode FROM tb_tarif WHERE kode_tarif = ?");
  $stmt->execute([$kode_tarif]);
  $metode = $stmt->fetchColumn();
  
  if ($metode === '0') {
      header('Location: iuran.php');
      exit;
  }
}
?>

<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Iuran Warga</h1>
    <form method="GET" class="flex items-center gap-2">
      <?php if($kode_tarif): ?>
        <input type="hidden" name="kode_tarif" value="<?= htmlspecialchars($kode_tarif) ?>">
      <?php endif; ?>
      <label for="bulan" class="font-semibold">Bulan:</label>
      <select name="bulan" id="bulan" class="border rounded p-1" onchange="this.form.submit()">
        <?php for($b = 1; $b <= 12; $b++): ?>
          <option value="<?= $b ?>" <?= $b==$bulan_filter?'selected':'' ?>><?= $nama_bulan[$b] ?></option>
        <?php endfor; ?>
      </select>
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
      <?php 
      $index = 0;
      foreach($tarif as $t): 
        $warna_icon_class = $warna_icon[$index % count($warna_icon)];
        $index++;
      ?>
        <a href="?kode_tarif=<?= urlencode($t['kode_tarif']) ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan_filter ?>" class="box-modern-link">
          <div class="box-modern-iuran">
            <div class="flex items-center justify-between w-full">
              <div class="flex items-center space-x-4">
                <div class="text-4xl <?= $warna_icon_class ?> icon-container"><i class="bx <?= htmlspecialchars($t['icon']) ?>"></i></div>
                <div class="text-content">
                  <div class="text-lg font-bold text-gray-800"><?= htmlspecialchars($t['nama_tarif']) ?></div>
                  <div class="text-gray-700"><?= number_format($t['tarif'],0,',','.') ?><?php
                    if($t['metode'] == '1') echo '/bulan';
                    elseif($t['metode'] == '2') echo '/tahun';
                    elseif($t['metode'] == '3') echo '/seumur hidup';
                  ?></div>
                </div>
              </div>
              <div class="text-2xl text-gray-400 arrow-icon">
                <i class="bx bx-chevron-right"></i>
              </div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php elseif(!$nikk): ?>
    <!-- Tabel Rekap per KK untuk Jenis Iuran Terpilih -->
    <div class="mb-4">
      <a href="iuran.php?tahun=<?= $tahun ?>&bulan=<?= $bulan_filter ?>" class="text-blue-600 hover:underline">&larr; Kembali ke menu iuran</a>
    </div>

    <!-- Tampilkan total setoran untuk jenis iuran yang dipilih -->
    <?php 
    $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
    $is_tahunan = $tarif_map[$kode_tarif]['metode'] == '2';
    $is_seumurhidup = $tarif_map[$kode_tarif]['metode'] == '3';
    $total_setoran_terpilih = $total_setoran_per_iuran[$kode_tarif];
    // Hitung total setoran bulanan khusus untuk seumur hidup
    if ($is_seumurhidup) {
        $stmt_bulan = $pdo->prepare("SELECT SUM(jml_bayar) as total FROM tb_iuran WHERE kode_tarif = ? AND bulan = 'Selamanya' AND tahun = ? AND MONTH(tgl_bayar) = ?");
        $stmt_bulan->execute([$kode_tarif, $tahun, $bulan_filter]);
        $total_setoran_terpilih = intval($stmt_bulan->fetchColumn());
    }
    // Hitung total setoran tahunan untuk tahun yang dipilih
    $total_setoran_tahunan = 0;
    if ($is_bulanan) {
        // Jika tarif bulanan, hitung total pembayaran tahunan di tahun yang dipilih
        $stmt_tahunan = $pdo->prepare("SELECT SUM(jml_bayar) as total FROM tb_iuran WHERE kode_tarif = ? AND YEAR(tgl_bayar) = ? AND bulan != 'Tahunan'");
        $stmt_tahunan->execute([$kode_tarif, $tahun]);
        $total_setoran_tahunan = intval($stmt_tahunan->fetchColumn());
    } elseif ($is_tahunan) {
        // Untuk tahunan, hanya tahun yang dipilih
        $stmt_tahunan = $pdo->prepare("SELECT SUM(jml_bayar) as total FROM tb_iuran WHERE kode_tarif = ? AND tahun = ? AND bulan = 'Tahunan'");
        $stmt_tahunan->execute([$kode_tarif, $tahun]);
        $total_setoran_tahunan = intval($stmt_tahunan->fetchColumn());
    } elseif ($is_seumurhidup) {
        // Untuk seumur hidup, total setoran = seluruh pembayaran sepanjang masa
        $stmt_seumur = $pdo->prepare("SELECT SUM(jml_bayar) as total FROM tb_iuran WHERE kode_tarif = ?");
        $stmt_seumur->execute([$kode_tarif]);
        $total_setoran_tahunan = intval($stmt_seumur->fetchColumn());
    }
    ?>
    <div class="mb-6">
      <h2 class="text-lg font-semibold mb-3">
        Total Setoran <?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Box Total Setoran Bulanan -->
        <div class="bg-white border rounded-lg p-6 shadow-sm cursor-pointer" onclick="showPembayarBulanan()">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-600">Total Setoran Bulanan</div>
              <div class="text-2xl font-bold text-blue-600"><?= number_format($total_setoran_terpilih, 0, ',', '.') ?></div>
              <div class="text-sm text-gray-500">
                <?php if($is_bulanan): ?>
                  Pembayaran di bulan <?= $nama_bulan[$bulan_filter] ?> <?= $tahun ?>
                <?php elseif($is_tahunan): ?>
                  Pembayaran tahunan <?= $tahun ?>
                <?php elseif($is_seumurhidup): ?>
                  Pembayaran seumur hidup
                <?php endif; ?>
              </div>
            </div>
            <div class="text-4xl"><i class="bx <?= htmlspecialchars($tarif_map[$kode_tarif]['icon']) ?>"></i></div>
          </div>
        </div>

        <!-- Box Total Setoran Tahunan / Seumur Hidup -->
        <div class="bg-white border rounded-lg p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm font-medium text-gray-600">
                <?php if($is_seumurhidup): ?>
                  Total Setoran
                <?php else: ?>
                  Total Setoran Tahunan
                <?php endif; ?>
              </div>
              <div class="text-2xl font-bold text-green-600"><?= number_format($total_setoran_tahunan, 0, ',', '.') ?></div>
              <div class="text-sm text-gray-500">
                <?php if($is_seumurhidup): ?>
                  Semua pembayaran seumur hidup
                <?php else: ?>
                  Pembayaran tahun <?= $tahun ?>
                <?php endif; ?>
              </div>
            </div>
            <div class="text-4xl"><i class="bx <?= htmlspecialchars($tarif_map[$kode_tarif]['icon']) ?>"></i></div>
          </div>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto">
      <!-- Search Box untuk Tabel Rekap KK -->
      <div class="mb-4">
        <div class="relative">
          <input type="text" id="searchRekap" placeholder="Cari nama KK..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <i class="bx bx-search text-gray-400"></i>
          </div>
        </div>
      </div>
      
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm" id="tableRekap">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">No KK</th>
            <th class="px-2 py-1 border">Nama KK</th>
            <th class="px-2 py-1 border text-right">Total Tagihan</th>
            <th class="px-2 py-1 border text-right">Sudah Bayar</th>
            <th class="px-2 py-1 border text-right">Sisa Hutang</th>
            <th class="px-2 py-1 border">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
          $is_tahunan = $tarif_map[$kode_tarif]['metode'] == '2';
          $is_seumurhidup = $tarif_map[$kode_tarif]['metode'] == '3';
          $periode_list = $is_bulanan ? [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
          ] : ($is_tahunan ? [$tahun] : ['Seumur Hidup']);
          foreach($kk as $w):
            $total_tagihan = 0;
            $total_bayar = 0;
            if($is_seumurhidup) {
              $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
              $total_tagihan = $tarif_nom;
              // Total bayar: seluruh pembayaran sepanjang masa
              $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ?");
              $stmt_total->execute([$w['nikk'], $kode_tarif]);
              $total_bayar = intval($stmt_total->fetchColumn());
              $sisa = $tarif_nom - $total_bayar;
              $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
            } else {
              foreach($periode_list as $periode) {
                $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
                $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
                $total_tagihan += $tarif_nom;
                if (isset($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key])) {
                  foreach ($pembayaran_map[$w['nikk']][$kode_tarif][$periode_key] as $p) {
                    $total_bayar += intval($p['jml_bayar']);
                  }
                }
              }
              // Ambil total bayar langsung dari database untuk memastikan akurasi
              if ($is_bulanan) {
                  $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan IS NOT NULL AND bulan != '' AND bulan != 'Tahunan'");
                  $stmt_total->execute([$w['nikk'], $kode_tarif, $tahun]);
              } else if ($is_tahunan) {
                  $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan = 'Tahunan'");
                  $stmt_total->execute([$w['nikk'], $kode_tarif, $tahun]);
              }
              $total_bayar_db = intval($stmt_total->fetchColumn());
              if ($total_bayar_db > 0) {
                  $total_bayar = $total_bayar_db;
              }
              $sisa = $total_tagihan - $total_bayar;
              $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
            }
            $warna_status = '';
            if ($status == 'Lunas') {
                $warna_status = 'text-green-600';
            } elseif ($total_bayar > 0) {
                $warna_status = 'text-orange-600';
            } else {
                $warna_status = 'text-red-600';
            }
          ?>
          <tr class="hover:bg-gray-100">
            <td class="px-2 py-1 border"><?= htmlspecialchars($w['nikk']) ?></td>
            <td class="px-2 py-1 border">
              <a href="?kode_tarif=<?= urlencode($kode_tarif) ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan_filter ?>&nikk=<?= urlencode($w['nikk']) ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                <?= htmlspecialchars($w['nama']) ?>
              </a>
            </td>
            <td class="px-2 py-1 border text-right"><?= number_format($total_tagihan,0,',','.') ?></td>
            <td class="px-2 py-1 border text-right"><?= number_format($total_bayar,0,',','.') ?></td>
            <td class="px-2 py-1 border text-right"><?= number_format(max($sisa,0),0,',','.') ?></td>
            <td class="px-2 py-1 border font-semibold <?= $warna_status ?>" style="<?= $status=='Lunas'?'color: #059669;':($total_bayar > 0 ? 'color: #ea580c;' : 'color: #dc2626;') ?>"><?= $status ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <!-- Tabel Detail per Bulan/Tahun untuk KK Terpilih -->
    <div class="mb-4 flex items-center gap-2">
      <a href="?kode_tarif=<?= urlencode($kode_tarif) ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan_filter ?>" class="text-blue-600 hover:underline">&larr; Kembali ke rekap KK</a>
      <span class="font-semibold">|
        <?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?> -
        <?= htmlspecialchars($nikk) ?>
        (<?= htmlspecialchars($pdo->query("SELECT nama FROM tb_warga WHERE nikk='$nikk' AND hubungan='Kepala Keluarga' LIMIT 1")->fetchColumn()) ?>)
      </span>
    </div>
    <div class="overflow-x-auto">
      <!-- Search Box untuk Tabel Detail -->
      <div class="mb-4">
        <div class="relative">
          <input type="text" id="searchDetail" placeholder="Cari periode..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <i class="bx bx-search text-gray-400"></i>
          </div>
        </div>
      </div>
      
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm" id="tableDetail">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">Periode</th>
            <th class="px-2 py-1 border text-right">Tarif</th>
            <th class="px-2 py-1 border text-right">Sudah Bayar</th>
            <th class="px-2 py-1 border text-right">Sisa Hutang</th>
            <th class="px-2 py-1 border">Status</th>
            <th class="px-2 py-1 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $is_bulanan = $tarif_map[$kode_tarif]['metode'] == '1';
          $is_tahunan = $tarif_map[$kode_tarif]['metode'] == '2';
          $is_seumurhidup = $tarif_map[$kode_tarif]['metode'] == '3';
          $periode_list = $is_bulanan ? [
            'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'
          ] : ($is_tahunan ? [$tahun] : ['Seumur Hidup']);
          foreach($periode_list as $periode) {
            $periode_key = $is_bulanan ? $periode.'-'.$tahun : $tahun;
            $tarif_nom = intval($tarif_map[$kode_tarif]['tarif']);
            if($is_seumurhidup) {
              // Total bayar: seluruh pembayaran sepanjang masa
              $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ?");
              $stmt_total->execute([$nikk, $kode_tarif]);
              $total_bayar = intval($stmt_total->fetchColumn());
              $sisa = $tarif_nom - $total_bayar;
              $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
            } else {
              $total_bayar = 0;
              if (isset($pembayaran_map[$nikk][$kode_tarif][$periode_key])) {
                foreach ($pembayaran_map[$nikk][$kode_tarif][$periode_key] as $p) {
                  $total_bayar += intval($p['jml_bayar']);
                }
              }
              if ($is_bulanan) {
                  $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan = ?");
                  $stmt_total->execute([$nikk, $kode_tarif, $tahun, $periode]);
              } else if ($is_tahunan) {
                  $stmt_total = $pdo->prepare("SELECT SUM(jml_bayar) as total_bayar FROM tb_iuran WHERE nikk = ? AND kode_tarif = ? AND tahun = ? AND bulan = 'Tahunan'");
                  $stmt_total->execute([$nikk, $kode_tarif, $tahun]);
              }
              $total_bayar_db = intval($stmt_total->fetchColumn());
              if ($total_bayar_db > 0) {
                  $total_bayar = $total_bayar_db;
              }
              $sisa = $tarif_nom - $total_bayar;
              $status = $sisa <= 0 ? 'Lunas' : 'Belum Lunas';
            }
            $warna_status = '';
            if ($status == 'Lunas') {
                $warna_status = 'text-green-600';
            } elseif ($total_bayar > 0) {
                $warna_status = 'text-orange-600';
            } else {
                $warna_status = 'text-red-600';
            }
          ?>
          <tr class="hover:bg-gray-100">
            <td class="px-2 py-1 border"><?= $is_bulanan ? $periode.' '.$tahun : $tahun ?></td>
            <td class="px-2 py-1 border text-right"><?= number_format($tarif_nom,0,',','.') ?></td>
            <td class="px-2 py-1 border text-right"><?= number_format($total_bayar,0,',','.') ?></td>
            <td class="px-2 py-1 border text-right"><?= number_format(max($sisa,0),0,',','.') ?></td>
            <td class="px-2 py-1 border font-semibold <?= $warna_status ?>" style="<?= $status=='Lunas'?'color: #059669;':($total_bayar > 0 ? 'color: #ea580c;' : 'color: #dc2626;') ?>"><?= $status ?></td>
            <td class="px-2 py-1 border">
              <?php if($status=='Belum Lunas'): ?>
                <div class="flex space-x-1">
                  <button class="bg-blue-600 text-white p-1 rounded text-xs hover:bg-blue-700" title="Bayar" onclick="openBayarModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>',<?= $sisa ?>)">
                    <i class="bx bx-money"></i>
                  </button>
                  <?php if($total_bayar > 0): ?>
                    <button class="bg-red-600 text-white p-1 rounded text-xs hover:bg-red-700" title="Histori" onclick="openHistoriModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>')">
                      <i class="bx bx-history"></i>
                    </button>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <?php if($total_bayar > 0): ?>
                  <button class="bg-red-600 text-white p-1 rounded text-xs hover:bg-red-700" title="Histori" onclick="openHistoriModal('<?= $nikk ?>','<?= $kode_tarif ?>','<?= $is_bulanan ? $periode.'-'.$tahun : $tahun ?>','<?= htmlspecialchars($tarif_map[$kode_tarif]['nama_tarif']) ?>')">
                    <i class="bx bx-history"></i>
                  </button>
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

<!-- Modal Daftar Pembayar Bulanan -->
<div id="modalPembayarBulanan" class="fixed inset-0 flex items-center justify-center z-50 hidden">
  <div class="bg-white p-4 rounded shadow-lg w-full max-w-2xl max-h-[80vh] overflow-y-auto">
    <h2 class="text-lg font-bold mb-2">Daftar KK Pembayar Bulan <span id="modalPembayarBulanText"></span></h2>
    <div class="mb-4">
      <table class="min-w-full bg-white border rounded shadow text-xs md:text-sm" id="tablePembayarBulanan">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-2 py-1 border">No</th>
            <th class="px-2 py-1 border">NIKK</th>
            <th class="px-2 py-1 border">Nama KK</th>
            <th class="px-2 py-1 border text-right">Jumlah Bayar</th>
            <th class="px-2 py-1 border">Tanggal Bayar</th>
          </tr>
        </thead>
        <tbody id="tbodyPembayarBulanan">
        </tbody>
      </table>
    </div>
    <div class="flex justify-end mt-4">
      <button type="button" class="bg-gray-500 text-white px-3 py-1 rounded" onclick="toggleModal('modalPembayarBulanan')">Tutup</button>
    </div>
  </div>
</div>

<?php if ($notif): ?>
<script>
  Swal.fire({
    icon: "<?= $notif['type'] ?>",
    title: "<?= $notif['type'] === 'success' ? 'Sukses' : 'Gagal' ?>",
    text: "<?= addslashes($notif['msg']) ?>",
    timer: 1500,
    timerProgressBar: true,
    showConfirmButton: false,
    position: 'top-end',
    toast: true
  });
</script>
<?php endif; ?>

<script>
// Mencegah form submit yang tidak diinginkan
document.addEventListener('DOMContentLoaded', function() {
    // Mencegah form pembayaran ter-submit secara otomatis
    const formBayar = document.getElementById('formBayar');
    if (formBayar) {
        formBayar.addEventListener('submit', function(e) {
            // Pastikan form hanya di-submit ketika tombol "Simpan" diklik
            const submitButton = e.submitter;
            if (!submitButton || submitButton.textContent !== 'Simpan') {
                e.preventDefault();
                return false;
            }
        });
    }
});

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
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    });
}

function hapusPembayaran(id_iuran) {
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
            console.log('Menghapus pembayaran dengan ID:', id_iuran);
            
            const formData = new FormData();
            formData.append('id_iuran', id_iuran);
            
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
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    }).then(() => {
                        // Tutup modal histori terlebih dahulu
                        toggleModal('historiModal');
                        // Refresh halaman dengan cara yang lebih aman
                        setTimeout(() => {
                            // Gunakan window.location.replace untuk menghindari history stack
                            window.location.replace(window.location.href);
                        }, 500);
                    });
                } else {
                    // Tampilkan pesan error khusus untuk validasi bulan
                    if (data.error_type === 'month_mismatch') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Dapat Dihapus!',
                            text: data.message,
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        // Tampilkan informasi debug yang lebih detail untuk error lain
                        let debugMessage = 'Gagal menghapus pembayaran: ' + data.message;
                        if (data.debug) {
                            debugMessage += '\n\nDebug Info:';
                            debugMessage += '\n- ID Iuran: ' + data.debug.id_iuran;
                            debugMessage += '\n- Count Before: ' + data.debug.count_before;
                            debugMessage += '\n- Count After: ' + (data.debug.count_after ?? 'N/A');
                            debugMessage += '\n- Rows Deleted: ' + (data.debug.rows_deleted ?? 'N/A');
                            if (data.debug.data_to_delete) {
                                debugMessage += '\n- Data Found: ' + JSON.stringify(data.debug.data_to_delete);
                            }
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: debugMessage,
                            timer: 1500,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            position: 'center'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghapus pembayaran: ' + error.message,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            });
        }
    });
}

// Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search function untuk tabel rekap KK
    const searchRekap = document.getElementById('searchRekap');
    const tableRekap = document.getElementById('tableRekap');
    
    if (searchRekap && tableRekap) {
        searchRekap.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tableRekap.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const namaKK = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (namaKK.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Search function untuk tabel detail
    const searchDetail = document.getElementById('searchDetail');
    const tableDetail = document.getElementById('tableDetail');
    
    if (searchDetail && tableDetail) {
        searchDetail.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tableDetail.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const periode = row.querySelector('td:first-child').textContent.toLowerCase();
                const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                
                if (periode.includes(searchTerm) || status.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});

// Clear search when page loads
window.addEventListener('load', function() {
    const searchRekap = document.getElementById('searchRekap');
    const searchDetail = document.getElementById('searchDetail');
    
    if (searchRekap) searchRekap.value = '';
    if (searchDetail) searchDetail.value = '';
});

// Pastikan fungsi ini di scope global
function showPembayarBulanan() {
    // Ambil data dari PHP (tanpa file baru, gunakan data yang sudah diambil di halaman)
    const data = <?php
        $listPembayar = [];
        if ($kode_tarif) {
            if ($is_bulanan) {
                $stmt = $pdo->prepare("SELECT i.nikk, w.nama, i.jml_bayar, i.tgl_bayar FROM tb_iuran i JOIN tb_warga w ON i.nikk=w.nikk WHERE i.kode_tarif=? AND i.bulan=? AND i.tahun=?");
                $stmt->execute([$kode_tarif, $nama_bulan[$bulan_filter], $tahun]);
                $listPembayar = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else if ($is_tahunan) {
                $stmt = $pdo->prepare("SELECT i.nikk, w.nama, i.jml_bayar, i.tgl_bayar FROM tb_iuran i JOIN tb_warga w ON i.nikk=w.nikk WHERE i.kode_tarif=? AND i.bulan='Tahunan' AND i.tahun=?");
                $stmt->execute([$kode_tarif, $tahun]);
                $listPembayar = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else if ($is_seumurhidup) {
                $stmt = $pdo->prepare("SELECT i.nikk, w.nama, i.jml_bayar, i.tgl_bayar FROM tb_iuran i JOIN tb_warga w ON i.nikk=w.nikk WHERE i.kode_tarif=? AND i.bulan='Selamanya' AND i.tahun=? AND MONTH(i.tgl_bayar)=?");
                $stmt->execute([$kode_tarif, $tahun, $bulan_filter]);
                $listPembayar = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        echo '<pre>';
        print_r($listPembayar);
        echo '</pre>';
        echo json_encode($listPembayar);
    ?>;
    // Isi tabel
    const tbody = document.getElementById('tbodyPembayarBulanan');
    tbody.innerHTML = '';
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada pembayaran</td></tr>';
    } else {
        data.forEach((row, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class='px-2 py-1 border'>${idx+1}</td>`+
                `<td class='px-2 py-1 border'>${row.nikk}</td>`+
                `<td class='px-2 py-1 border'>${row.nama}</td>`+
                `<td class='px-2 py-1 border text-right'>${number_format(row.jml_bayar,0,',','.')}</td>`+
                `<td class='px-2 py-1 border'>${row.tgl_bayar}</td>`;
            tbody.appendChild(tr);
        });
    }
    document.getElementById('modalPembayarBulanText').textContent = `<?= $nama_bulan[$bulan_filter] ?> <?= $tahun ?>`;
    toggleModal('modalPembayarBulanan');
}
</script>

<style>
/* Modern Box Styling untuk Iuran */
.box-modern-link {
  display: block;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s ease;
}

.box-modern-link:hover {
  text-decoration: none;
  color: inherit;
  transform: translateY(-4px);
}

.box-modern-iuran {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  padding: 1.25rem 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  position: relative;
  min-height: 80px;
  display: flex;
  align-items: center;
}

.box-modern-iuran:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border-color: #cbd5e1;
}

/* Icon Container Styling */
.icon-container {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  padding: 12px;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.box-modern-iuran:hover .icon-container {
  transform: scale(1.05);
  box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3);
}

/* Text Content Styling */
.text-content {
  flex: 1;
  min-width: 0;
}

.text-content .text-lg {
  font-size: 1.125rem;
  font-weight: 700;
  margin: 0;
  color: #1e293b;
  transition: color 0.3s ease;
  line-height: 1.2;
}

.text-content .text-gray-700 {
  font-size: 0.875rem;
  color: #64748b;
  margin-top: 0.25rem;
  transition: color 0.3s ease;
  line-height: 1.2;
}

/* Arrow Icon Styling */
.arrow-icon {
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.box-modern-iuran:hover .arrow-icon {
  transform: translateX(4px);
  color: #6b7280;
}

/* Hover Effects untuk Text */
.box-modern-link:hover .text-lg {
  color: #0f172a;
}

.box-modern-link:hover .text-gray-700 {
  color: #475569;
}

/* Responsive Design */
@media (max-width: 768px) {
  .box-modern-iuran {
    padding: 1rem 1.25rem;
    min-height: 70px;
  }
  
  .icon-container {
    padding: 10px;
    font-size: 1.5rem;
  }
  
  .text-content .text-lg {
    font-size: 1rem;
  }
  
  .text-content .text-gray-700 {
    font-size: 0.8rem;
  }
  
  .arrow-icon {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .box-modern-iuran {
    padding: 0.8rem 1rem;
    min-height: 60px;
  }
  
  .icon-container {
    padding: 8px;
    font-size: 1.25rem;
  }
  
  .text-content .text-lg {
    font-size: 0.9rem;
  }
  
  .text-content .text-gray-700 {
    font-size: 0.75rem;
  }
  
  .arrow-icon {
    font-size: 1.25rem;
  }
  
  .flex.items-center.space-x-4 {
    gap: 0.75rem;
  }
}

/* Grid Layout Improvements */
.grid {
  display: grid;
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .sm\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 768px) {
  .md\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

/* Animation untuk loading */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.box-modern-iuran {
  animation: fadeInUp 0.6s ease-out;
}

/* Stagger animation untuk multiple boxes */
.box-modern-iuran:nth-child(1) { animation-delay: 0.1s; }
.box-modern-iuran:nth-child(2) { animation-delay: 0.2s; }
.box-modern-iuran:nth-child(3) { animation-delay: 0.3s; }
.box-modern-iuran:nth-child(4) { animation-delay: 0.4s; }
.box-modern-iuran:nth-child(5) { animation-delay: 0.5s; }
.box-modern-iuran:nth-child(6) { animation-delay: 0.6s; }
</style>

<?php include 'footer.php'; ?> 