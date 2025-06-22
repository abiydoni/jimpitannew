# Folder Penyimpanan Foto Warga

Folder ini digunakan untuk menyimpan foto-foto warga yang diupload melalui sistem.

## Struktur File

- Foto warga disimpan dengan format: `warga_[timestamp]_[unique_id].[extension]`
- Contoh: `warga_1703123456_abc123def456.jpg`

## Keamanan

- File `.htaccess` mengizinkan akses hanya ke file gambar (jpg, jpeg, png, gif)
- Mencegah akses ke file PHP dan script lainnya
- Directory listing dinonaktifkan

## File yang Diubah

1. `dashboard/api/warga_action.php` - Fungsi uploadFoto
2. `dashboard/api/modal_warga.php` - Upload foto modal
3. `dashboard/api/kk_insert.php` - Upload foto KK
4. `dashboard/api/kk_update.php` - Update foto KK

## Path Database

Foto disimpan di database dengan path relatif: `images/warga/[filename]`

## Backup

Disarankan untuk melakukan backup folder ini secara berkala untuk mencegah kehilangan data foto warga.
