<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Manajemen Data Warga</h1>
        <!-- Tombol Aksi -->
        <div class="mb-4 flex flex-wrap gap-2">
            <button id="btnTambah" class="bg-blue-500 text-white px-4 py-2 rounded">➕ Tambah Data</button>
            <button id="btnImport" class="bg-green-500 text-white px-4 py-2 rounded">⬆️ Import Excel</button>
            <button onclick="exportExcel()" class="bg-yellow-500 text-white px-4 py-2 rounded">⬇️ Export Excel</button>
            <input type="text" id="searchInput" placeholder="🔍 Cari warga..." class="border px-2 py-1 rounded w-full sm:w-auto flex-grow">
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200" id="tabelWarga">
                <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                    <tr>
                        <th class="px-3 py-2">Foto</th>
                        <th class="px-3 py-2">NIK</th>
                        <th class="px-3 py-2">Nama</th>
                        <th class="px-3 py-2">Alamat</th>
                        <th class="px-3 py-2">Wilayah</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataBody" class="divide-y divide-gray-200 text-sm">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white w-full max-w-2xl p-6 rounded-lg shadow-lg relative">
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-800" onclick="tutupModal()">✖️</button>
            <form id="formWarga" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="nik" id="nik" placeholder="NIK" required class="border px-3 py-2 rounded">
                    <input type="text" name="nokk" id="nokk" placeholder="No KK" required class="border px-3 py-2 rounded">
                    <input type="text" name="nama" id="nama" placeholder="Nama" required class="border px-3 py-2 rounded">
                    <select name="jenkel" id="jenkel" required class="border px-3 py-2 rounded">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <input type="text" name="tpt_lahir" id="tpt_lahir" placeholder="Tempat Lahir" required class="border px-3 py-2 rounded">
                    <input type="date" name="tgl_lahir" id="tgl_lahir" required class="border px-3 py-2 rounded">
                    <input type="text" name="agama" id="agama" placeholder="Agama" required class="border px-3 py-2 rounded">
                    <input type="text" name="status" id="status" placeholder="Status" required class="border px-3 py-2 rounded">
                    <input type="text" name="pekerjaan" id="pekerjaan" placeholder="Pekerjaan" required class="border px-3 py-2 rounded">
                    <input type="text" name="hp" id="hp" placeholder="Nomor HP" required class="border px-3 py-2 rounded">
                </div>
                <textarea name="alamat" id="alamat" placeholder="Alamat lengkap..." class="border w-full mt-4 px-3 py-2 rounded" required></textarea>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <input type="text" name="rt" id="rt" placeholder="RT" required class="border px-3 py-2 rounded">
                    <input type="text" name="rw" id="rw" placeholder="RW" required class="border px-3 py-2 rounded">
                    <select id="provinsi" name="provinsi" required class="border px-3 py-2 rounded"></select>
                    <select id="kota" name="kota" required class="border px-3 py-2 rounded"></select>
                    <select id="kecamatan" name="kecamatan" required class="border px-3 py-2 rounded"></select>
                    <select id="kelurahan" name="kelurahan" required class="border px-3 py-2 rounded"></select>
                </div>
                <div class="mt-4">
                    <label>Upload Foto:</label>
                    <input type="file" name="foto" id="foto" accept="image/*" class="block mt-1">
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <input type="file" id="fileExcel" accept=".xlsx" class="hidden">
    <script src="warga.js"></script>
</body>
</html>
