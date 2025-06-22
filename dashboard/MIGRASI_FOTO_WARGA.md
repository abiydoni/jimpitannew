# Migrasi Penyimpanan Foto Warga

## Ringkasan Perubahan

Sistem penyimpanan foto warga telah dipindahkan dari folder `uploads/foto_warga/` ke folder `dashboard/images/warga/` untuk organisasi yang lebih baik dan keamanan yang meningkat.

## File yang Diubah

### 1. `dashboard/api/warga_action.php`

- **Fungsi `uploadFoto()`**: Mengubah path upload dari `../uploads/foto_warga/` ke `../images/warga/`
- **Return value**: Mengubah path return dari `uploads/foto_warga/` ke `images/warga/`

### 2. `dashboard/api/modal_warga.php`

- **Upload path**: Mengubah `$targetDir` dari `'uploads/'` ke `'images/warga/'`

### 3. `dashboard/api/kk_insert.php`

- **Upload path**: Mengubah `$upload_dir` dari `'../uploads/'` ke `'../images/warga/'`

### 4. `dashboard/api/kk_update.php`

- **Upload path**: Mengubah path dari `"uploads/"` ke `"images/warga/"`

## Struktur Folder Baru

```
dashboard/
├── images/
│   ├── warga/           # Folder baru untuk foto warga
│   │   ├── .htaccess    # Keamanan folder
│   │   └── README.md    # Dokumentasi folder
│   ├── users.gif
│   └── gambarprofil.png
```

## Keamanan

### File `.htaccess` di `dashboard/images/warga/`

- Mengizinkan akses hanya ke file gambar (jpg, jpeg, png, gif)
- Mencegah akses ke file PHP dan script berbahaya
- Menonaktifkan directory listing

## Format Nama File

Foto warga disimpan dengan format: `warga_[timestamp]_[unique_id].[extension]`
Contoh: `warga_1703123456_abc123def456.jpg`

## Migrasi Data Lama

### Script Migrasi

File `dashboard/migrate_photos.php` tersedia untuk memindahkan foto lama yang masih menggunakan path lama.

**Cara menjalankan:**

1. Akses `http://your-domain/dashboard/migrate_photos.php`
2. Script akan otomatis memindahkan semua foto lama ke folder baru
3. Update path di database
4. Hapus file lama

### Backup

**PENTING**: Lakukan backup database dan folder foto sebelum menjalankan script migrasi.

## Testing

### Test Upload Foto Baru

1. Buka halaman warga
2. Tambah warga baru dengan foto
3. Pastikan foto tersimpan di `dashboard/images/warga/`
4. Pastikan path di database adalah `images/warga/[filename]`

### Test Edit Foto

1. Edit warga yang sudah ada
2. Upload foto baru
3. Pastikan foto lama terhapus dan foto baru tersimpan di folder yang benar

## Troubleshooting

### Foto Tidak Muncul

1. Cek path di database
2. Cek apakah file ada di folder `dashboard/images/warga/`
3. Cek permission folder (755)
4. Cek file `.htaccess`

### Error Upload

1. Cek permission folder `dashboard/images/warga/`
2. Cek ukuran file (max 2MB)
3. Cek tipe file (hanya jpg, jpeg, png, gif)

### Error Migrasi

1. Backup database terlebih dahulu
2. Cek permission folder
3. Cek apakah file lama masih ada

## Rollback (Jika Perlu)

Jika perlu kembali ke sistem lama:

1. Restore backup database
2. Restore folder `uploads/foto_warga/` (jika ada)
3. Ubah kembali semua file yang diubah ke path lama

## Catatan Penting

- **Backup**: Selalu backup sebelum melakukan perubahan
- **Testing**: Test di environment development terlebih dahulu
- **Monitoring**: Monitor error log setelah migrasi
- **Cleanup**: Hapus file `migrate_photos.php` setelah migrasi selesai
