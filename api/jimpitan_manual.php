<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php'); // Redirect ke halaman login
    exit;
}

include 'db.php';
// Ambil data master_kk untuk dropdown
$stmt_kk = $pdo->query("SELECT code_id, kk_name FROM master_kk");
$master_kk = $stmt_kk->fetchAll(PDO::FETCH_ASSOC);

// Ambil tarif untuk nominal (kode_tarif = 'TR001')
$stmt_tarif = $pdo->prepare("SELECT tarif FROM tb_tarif WHERE kode_tarif = 'TR001' LIMIT 1");
$stmt_tarif->execute();
$tarif = $stmt_tarif->fetchColumn();

// SQL statement untuk mengambil data hari ini
$stmt = $pdo->prepare("
    SELECT master_kk.kk_name, report.* 
    FROM report 
    JOIN master_kk ON report.report_id = master_kk.code_id
    WHERE report.jimpitan_date = CURDATE()
          AND report.collector = 'system'
    ORDER BY report.scan_time DESC
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total scan
$total_scans = count($data);
$total_nominal = array_sum(array_column($data, 'nominal'));

// Proses penyimpanan data jika form disubmit
if (isset($_POST['submit'])) {
    $report_id = $_POST['report_id'];
    $jimpitan_date = $_POST['jimpitan_date'];
    $nominal = $tarif; // Ambil nominal dari tarif yang sudah di-fetch
    $collector = 'system';
    $kode_u = 'system';
    $nama_u = 'system';

    $stmt_insert = $pdo->prepare("INSERT INTO report (report_id, jimpitan_date, nominal, collector, kode_u, nama_u) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->execute([$report_id, $jimpitan_date, $nominal, $collector, $kode_u, $nama_u]);

    // Refresh halaman agar data tabel terupdate
    echo "<script>location.reload();</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jimpitan Manual</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

    <style>
        table th, table td {
            text-align: left;
        }
        table tr {
            height: 28px;
            line-height: 1.2;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-poppins text-gray-800">
    <!-- <div id="overlayDiv" class="absolute inset-0"></div> -->
            <!-- Loader GIF loading -->
    <div id="loader" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
        <img src="../assets/image/loading.gif" alt="Loading..." class="w-32 h-auto">
    </div>

    <div id="overlayDiv" class="fixed inset-0 -z-10 pointer-events-none"></div>

    <div class="relative z-10 flex flex-col max-w-4xl mx-auto p-4 shadow-lg rounded-lg">
        <h1 class="text-xl font-bold text-gray-700 mb-2">Input Jimpitan Manual</h1>
        <p class="text-sm text-gray-500 mb-4">Hari <span id="tanggal"></span></p>
        <div class="mb-4 bg-white p-4 rounded-md shadow">
            <form method="POST" action="proses_simpan.php">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama KK</label>
                        <select name="report_id" required class="w-full border rounded px-2 py-1">
                            <option value="">Pilih KK</option>
                            <?php foreach($master_kk as $kk): ?>
                                <option value="<?= $kk['code_id'] ?>"><?= htmlspecialchars($kk['kk_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Jimpitan</label>
                        <input type="date" name="jimpitan_date" required class="w-full border rounded px-2 py-1" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <input type="hidden" name="collector" value="system" />
                <input type="hidden" name="kode_u" value="system" />
                <input type="hidden" name="nama_u" value="system" />
                <button type="submit" name="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center gap-1">
                    <ion-icon name="save-outline"></ion-icon> Simpan
                </button>
            </form>
        </div>

        <div class="flex-1 border rounded-md mb-4 overflow-y-auto" style="max-height: 45vh;">
            <div class="overflow-auto rounded-md bg-white bg-opacity-50 p-1">
            <table class="min-w-full border-collapse text-sm text-gray-700">
                <thead class="sticky top-0 bg-gray-100 border-b">
                    <tr class='bg-gray-100 border-b'>
                        <th>No.</th>
                        <th>Nama KK</th>
                        <th class='text-center'>Nominal</th>
                        <th>Jaga</th>
                    </tr>
                </thead>
                <tbody id='data-table'>
                    <?php $no = 1; ?>
                    <?php foreach ($data as $row): ?>
                        <tr class='border-b hover:bg-gray-50'>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row["kk_name"]) ?></td>
                            <td class="text-center"><?= number_format($row["nominal"], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row["collector"]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-1 font-bold text-gray-700 text-left text-xl">
            Total Nominal Setor: Rp <span id="total-nominal"><?= number_format($total_nominal, 0, ',', '.') ?></span>
        </div>
        <div class="mt-4 font-bold text-gray-700 text-left">
            Total Scan: <span id="total-scans"><?= number_format($total_scans, 0, ',', '.') ?></span>
        </div>

        <button class="fixed bottom-4 right-4 w-12 h-12 bg-yellow-500 hover:bg-yellow-700 text-white font-bold rounded-full flex items-center justify-center shadow-lg transition-transform transform hover:scale-110"
            onclick="window.location.href='../index.php'" title="Pergi ke menu">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>
    </div>

    <script>
    // Menambahkan event listener untuk semua elemen tombol/link
    document.querySelectorAll('button, a, input[type="submit"]').forEach(element => {
        element.addEventListener('click', function (e) {
        // Mencegah form disubmit langsung atau link berpindah halaman
        e.preventDefault();

        // Tampilkan loader
        document.getElementById('loader').classList.remove('hidden');
        
        // Jika itu adalah form submit, submit form setelah beberapa detik
        if (this.type === 'submit') {
            setTimeout(function() {
            this.closest('form').submit();
            }.bind(this), 500); // Tunggu 500ms sebelum submit form
        } else {
            // Jika itu link, pindahkan halaman setelah beberapa detik
            setTimeout(() => {
            window.location.href = this.href;
            }, 500); // Tunggu 500ms sebelum pindah halaman
        }
        });
    });
    </script>
    <script>
        // Menampilkan tanggal dalam format Indonesia
        function formatTanggalIndonesia() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const tanggal = new Date();
            return `${hari[tanggal.getDay()]}, ${tanggal.getDate()} ${bulan[tanggal.getMonth()]} ${tanggal.getFullYear()}`;
        }

        document.getElementById("tanggal").textContent = formatTanggalIndonesia();

        // Fungsi untuk memperbarui tabel setiap 60 detik
        function updateTable() {
            $.ajax({
                url: 'get_data.php',
                method: 'GET',
                success: function(response) {
                    console.log(response); // Periksa output ini di konsol browser
                    const data = JSON.parse(response);
                    $('#data-table').empty();
                    data.data.forEach((row, index) => {
                        $('#data-table').append(`
                            <tr class='border-b hover:bg-gray-50'>
                                <td>${index + 1}</td>
                                <td>${row.kk_name}</td>
                                <td class="text-center">${parseInt(row.nominal).toLocaleString()}</td>
                                <td>${row.collector}</td>
                            </tr>
                        `);
                    });
                    $('#total-scans').text(data.data.length);
                    $('#total-nominal').text(parseInt(data.total_nominal).toLocaleString());
                },
                error: function() {
                    $('#data-table').html("<tr><td colspan='3' style='text-align: center;'>Gagal memuat data.</td></tr>");
                }
            });
        }

        // Panggil updateTable setiap 60 detik
        setInterval(updateTable, 10000);

        // Muat data pertama kali saat halaman dimuat
        $(document).ready(updateTable);

    </script>
    <script>
        const overlay = document.getElementById('overlayDiv');
        const savedColor = localStorage.getItem('overlayColor') || '#000000E6';
        overlay.style.backgroundColor = savedColor;
    </script>
</body>
</html>
