<?php
// File test untuk upload foto
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test Upload Foto</h2>";

// Cek apakah direktori upload ada
$uploadDir = 'images/warga/';
if (is_dir($uploadDir)) {
    echo "<p>✅ Direktori upload ada: $uploadDir</p>";
    if (is_writable($uploadDir)) {
        echo "<p>✅ Direktori upload bisa ditulis</p>";
    } else {
        echo "<p>❌ Direktori upload tidak bisa ditulis</p>";
    }
} else {
    echo "<p>❌ Direktori upload tidak ada: $uploadDir</p>";
}

// Cek apakah ada file foto yang sudah diupload
$files = glob($uploadDir . '*.jpg');
$files = array_merge($files, glob($uploadDir . '*.jpeg'));
$files = array_merge($files, glob($uploadDir . '*.png'));
$files = array_merge($files, glob($uploadDir . '*.gif'));

if (count($files) > 0) {
    echo "<p>✅ Ditemukan " . count($files) . " file foto:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul>";
} else {
    echo "<p>ℹ️ Belum ada file foto yang diupload</p>";
}

// Form upload test
echo "<h3>Test Upload</h3>";
echo "<form method='post' enctype='multipart/form-data'>";
echo "<input type='file' name='test_foto' accept='image/*' required>";
echo "<input type='submit' value='Upload Test'>";
echo "</form>";

if ($_FILES && isset($_FILES['test_foto'])) {
    $file = $_FILES['test_foto'];
    echo "<h3>Hasil Upload:</h3>";
    echo "<p>Nama file: " . $file['name'] . "</p>";
    echo "<p>Tipe file: " . $file['type'] . "</p>";
    echo "<p>Ukuran file: " . $file['size'] . " bytes</p>";
    echo "<p>Error code: " . $file['error'] . "</p>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = 'test_' . time() . '_' . uniqid() . '.jpg';
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            echo "<p>✅ Upload berhasil: $filepath</p>";
            echo "<img src='$filepath' style='max-width: 200px; border: 1px solid #ccc;'>";
        } else {
            echo "<p>❌ Upload gagal</p>";
        }
    } else {
        echo "<p>❌ Error upload: " . $file['error'] . "</p>";
    }
}
?> 