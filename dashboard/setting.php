<?php
session_start();
include 'header.php';

// Periksa apakah pengguna sudah masuk
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Alihkan ke halaman login
    exit;
}

    if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php'); // Alihkan ke halaman tidak diizinkan
    exit;
}
// Sertakan koneksi database
include 'api/db.php';

// Ambil semua konfigurasi
$stmt = $pdo->query("SELECT * FROM tb_konfigurasi ORDER BY nama ASC");
$konfigurasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['value'] as $nama => $value) {
        $stmt = $pdo->prepare("UPDATE tb_konfigurasi SET value = :value WHERE nama = :nama");
        $stmt->execute([':value' => $value, ':nama' => $nama]);
    }
    echo "<div class='bg-green-100 text-green-800 p-2 mb-4'>Konfigurasi berhasil diperbarui.</div>";
    header("Refresh:1"); // Refresh otomatis
}
?>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h1 class="text-2xl font-bold mb-4">🛠️ Edit Konfigurasi WA Otomatis</h1>
        </div>
        <form method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <?php foreach ($konfigurasi as $item): ?>
                    <div class="mb-2">
                        <label class="block font-medium text-gray-700 mb-0.5"><?= htmlspecialchars($item['nama']) ?></label>
                        <input
                            type="text"
                            name="value[<?= htmlspecialchars($item['nama']) ?>]"
                            value="<?= htmlspecialchars($item['value']) ?>"
                            class="w-full rounded border border-gray-300 px-2 py-1 focus:ring focus:ring-blue-200 focus:outline-none text-xs"
                            required
                        >
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-3 text-right">
                <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded hover:bg-blue-700 text-xs">
                    💾 Update
                </button>
            </div>
        </form>

        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden" style="width:100%">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">Nama</th>
                    <th class="border px-2 py-1">Value</th>
                    <th class="border px-2 py-1">Keterangan</th>
                    <th class="border px-2 py-1">Terakhir Update</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db.php';
                $stmt = $pdo->query("SELECT * FROM tb_konfigurasi ORDER BY nama");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr>
                    <td class="border px-2 py-1 text-gray-700"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border px-2 py-1" ondblclick="editCell(this, 'value', <?= $row['id'] ?>)">
                        <?= htmlspecialchars($row['value']) ?>
                    </td>
                    <td class="border px-2 py-1" ondblclick="editCell(this, 'keterangan', <?= $row['id'] ?>)">
                        <?= htmlspecialchars($row['keterangan']) ?>
                    </td>
                    <td class="border px-2 py-1 text-gray-500 text-xs"><?= $row['updated_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>
<?php include 'footer.php'; ?>

<script>
function editCell(cell, field, id) {
    const oldValue = cell.textContent.trim();
    const input = document.createElement("input");
    input.type = "text";
    input.value = oldValue;
    input.className = "w-full px-1 text-xs border";

    input.onblur = () => {
        const newValue = input.value.trim();
        if (newValue !== oldValue) {
            fetch("api/update_konfigurasi.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${id}&field=${field}&value=${encodeURIComponent(newValue)}`
            }).then(res => res.text()).then(response => {
                cell.textContent = newValue;
                console.log("Updated:", response);
            }).catch(() => {
                alert("Gagal menyimpan");
                cell.textContent = oldValue;
            });
        } else {
            cell.textContent = oldValue;
        }
    };

    cell.textContent = "";
    cell.appendChild(input);
    input.focus();
}
</script>
