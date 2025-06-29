<?php
session_start();
include 'api/db.php';

// Ambil dan kelompokkan data berdasarkan `group`
$stmt = $pdo->query("SELECT * FROM tb_konfigurasi ORDER BY `group`, nama ASC");
$konfigurasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan array berdasarkan group
$grouped = [];
foreach ($konfigurasi as $item) {
    $grouped[$item['group']][] = $item;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['value'] as $nama => $value) {
        $stmt = $pdo->prepare("UPDATE tb_konfigurasi SET value = :value WHERE nama = :nama");
        $stmt->execute([':value' => $value, ':nama' => $nama]);
    }
    session_start();
    $_SESSION['swal'] = ['msg' => 'Konfigurasi berhasil diperbarui.', 'icon' => 'success'];
    header("Location: setting.php");
    exit;
}

include 'header.php';
// Tambahkan script untuk SweetAlert2 toast jika ada notifikasi dari session
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['swal'])) {
  $msg = $_SESSION['swal']['msg'];
  $icon = $_SESSION['swal']['icon'];
  echo "<script>
    if (!window.Swal) {
      var script = document.createElement('script');
      script.src = 'js/sweetalert2.all.min.js';
      document.head.appendChild(script);
    }
    function showToast(msg, icon = 'success') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: msg,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });
    }
    document.addEventListener('DOMContentLoaded', function() {
      showToast('{$msg}', '{$icon}');
    });
  </script>";
  unset($_SESSION['swal']);
}
?>

<div class="table-data px-2">
    <div class="order">
        <div class="head">
            <h1 class="text-xl font-semibold mb-3">🛠️ Setting Konfigurasi Sistem</h1>
        </div>

        <form method="POST">
            <div class="space-y-3 text-xs">
                <?php foreach ($grouped as $group_id => $items): ?>
                    <fieldset class="border border-gray-300 rounded-lg p-2">
                        <legend class="text-xs font-semibold text-gray-700 px-1">🗂️ <?= $group_id ?></legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-1">
                            <?php foreach ($items as $item): ?>
                                <div class="mb-1">
                                    <label class="block text-gray-700 text-[11px] mb-0.5 font-medium">
                                        <?= htmlspecialchars($item['nama']) ?>
                                    </label>
                                    <input
                                        type="text"
                                        name="value[<?= htmlspecialchars($item['nama']) ?>]"
                                        value="<?= htmlspecialchars($item['value']) ?>"
                                        class="w-full rounded-lg border border-gray-300 px-1.5 py-0.5 focus:ring focus:ring-blue-200 focus:outline-none text-[11px]"
                                        required
                                    >
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php endforeach; ?>
            </div>

            <div class="mt-3 text-right">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 text-xs">
                    💾 Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
