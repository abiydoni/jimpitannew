<?php
/**
 * Script untuk memindahkan foto warga dari path lama ke path baru
 * Jalankan script ini sekali saja untuk memindahkan foto yang sudah ada
 */

include 'api/db.php';

try {
    // Ambil semua data warga yang memiliki foto
    $stmt = $pdo->query("SELECT id_warga, nama, foto FROM tb_warga WHERE foto IS NOT NULL AND foto != ''");
    $wargaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $migrated = 0;
    $errors = [];
    
    foreach ($wargaList as $warga) {
        $oldPath = $warga['foto'];
        
        // Skip jika sudah menggunakan path baru
        if (strpos($oldPath, 'images/warga/') === 0) {
            continue;
        }
        
        // Cek apakah file lama ada
        $fullOldPath = '../' . $oldPath;
        if (file_exists($fullOldPath)) {
            // Buat nama file baru
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            $newFilename = 'warga_' . time() . '_' . uniqid() . '.' . $extension;
            $newPath = 'images/warga/' . $newFilename;
            $fullNewPath = '../' . $newPath;
            
            // Pindahkan file
            if (copy($fullOldPath, $fullNewPath)) {
                // Update database
                $updateStmt = $pdo->prepare("UPDATE tb_warga SET foto = ? WHERE id_warga = ?");
                $updateStmt->execute([$newPath, $warga['id_warga']]);
                
                // Hapus file lama
                unlink($fullOldPath);
                
                $migrated++;
                echo "✓ Berhasil memindahkan foto {$warga['nama']}: {$oldPath} → {$newPath}\n";
            } else {
                $errors[] = "Gagal memindahkan foto {$warga['nama']}: {$oldPath}";
                echo "✗ Gagal memindahkan foto {$warga['nama']}: {$oldPath}\n";
            }
        } else {
            $errors[] = "File tidak ditemukan: {$oldPath} (Warga: {$warga['nama']})";
            echo "✗ File tidak ditemukan: {$oldPath} (Warga: {$warga['nama']})\n";
        }
    }
    
    echo "\n=== HASIL MIGRASI ===\n";
    echo "Total foto yang dipindahkan: {$migrated}\n";
    echo "Total error: " . count($errors) . "\n";
    
    if (!empty($errors)) {
        echo "\n=== DAFTAR ERROR ===\n";
        foreach ($errors as $error) {
            echo "- {$error}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 