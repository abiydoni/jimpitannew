<?php
session_start();
setlocale(LC_TIME, 'id_ID.utf8');

include 'header.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

if (!in_array($_SESSION['user']['role'], ['pengurus', 'admin', 's_admin'])) {
    header('Location: ../login.php');
    exit;
}

include 'api/db.php';

if (isset($_GET['delete'])) {
    $kode = $_GET['delete'];
    $sql = "DELETE FROM tb_warga WHERE kode=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode]);

    header("Location: warga.php");
    exit();
}

$sql = "SELECT * FROM tb_warga";
$stmt = $pdo->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Konten utama -->
<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Data Warga</h3>
            <div class="mb-4 text-center">
                <button type="button" onclick="bukaModalWarga()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class='bx bx-plus' style="font-size:24px"></i>
                </button>
            </div>
        </div>

        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>NIKK</th>
                    <th>Jenkel</th>
                    <th>TTL</th>
                    <th>RT</th>
                    <th>RW</th>
                    <th>No.HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($data as $row): ?>
                <tr class="border-b hover:bg-gray-100">
                    <td><?= htmlspecialchars($row['kode']) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['nik']) ?></td>
                    <td><?= htmlspecialchars($row['nikk']) ?></td>
                    <td><?= htmlspecialchars($row['jenkel']) ?></td>
                    <td><?= htmlspecialchars($row['tpt_lahir'] . ', ' . strftime('%e %B %Y', strtotime($row['tgl_lahir']))) ?></td>
                    <td><?= htmlspecialchars($row['rt']) ?></td>
                    <td><?= htmlspecialchars($row['rw']) ?></td>
                    <td><?= htmlspecialchars($row['hp']) ?></td>
                    <td class="flex justify-center space-x-2">
                        <button onclick="editWarga('<?= $row['kode'] ?>')" class="text-blue-600 hover:text-blue-400 font-bold py-1 px-1">
                            <i class='bx bx-edit'></i>
                        </button>
                        <a href="warga.php?delete=<?= $row['kode'] ?>" onclick="return confirm('Hapus data <?= $row['nama'] ?>?')" class="text-red-600 hover:text-red-400 font-bold py-1 px-1">
                            <i class='bx bx-trash'></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Warga -->
<?php include 'api/modal_warga.php'; ?>

<?php include 'footer.php'; ?>

<!-- Script wilayah dan form -->
<script src="js/warga.js?<?= time() ?>"></script>