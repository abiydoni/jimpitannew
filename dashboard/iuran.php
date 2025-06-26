<?php include 'header.php'; ?>
<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Daftar Tarif</h3>
            <div class="mb-4 text-center">
                <button type="button" id="openModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class='bx bx-plus' style="font-size:24px"></i> <!-- Ikon untuk tambah data -->
                </button>
            </div>
        </div>
        <table id="example" class="min-w-full border-collapse border border-gray-200 shadow-lg rounded-lg overflow-hidden text-xs" style="width:100%">
            <thead>
            <tr>
                <th>No KK</th>
                <th>Nama KK</th>
                <th>Tahun</th>
                <th>Jenis Iuran</th>
                <th>Total Bayar</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data as $row): ?>
                <tr>
                <td><?= htmlspecialchars($row['nokk']) ?></td>
                <td><?= htmlspecialchars($row['kepala_keluarga']) ?></td>
                <td><?= $row['tahun'] ?></td>
                <td><?= $row['jenis_ikut'] ?></td>
                <td>Rp<?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                <td>
                    <button onclick="lihatDetail('<?= $row['nokk'] ?>', <?= $row['tahun'] ?>)" class="text-blue-600 hover:underline">Detail</button>
                </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>