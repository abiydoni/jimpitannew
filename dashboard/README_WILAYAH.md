# Fitur Dropdown Wilayah Saling Berhubungan - EMSIFA API

## Deskripsi

Fitur ini memungkinkan pengguna untuk memilih wilayah (Provinsi → Kota/Kabupaten → Kecamatan → Kelurahan) dengan dropdown yang saling berhubungan menggunakan **EMSIFA API** (API Wilayah Indonesia).

## API yang Digunakan

- **EMSIFA API**: https://www.emsifa.com/api-wilayah-indonesia/
- **Gratis**: Tidak memerlukan API key
- **Lengkap**: Data seluruh wilayah Indonesia yang terupdate

## Cara Kerja

1. **Provinsi**: Dropdown pertama yang berisi semua provinsi di Indonesia
2. **Kota/Kabupaten**: Dropdown kedua yang akan terisi berdasarkan provinsi yang dipilih
3. **Kecamatan**: Dropdown ketiga yang akan terisi berdasarkan kota yang dipilih
4. **Kelurahan**: Dropdown keempat yang akan terisi berdasarkan kecamatan yang dipilih

## File yang Terlibat

- `dashboard/warga.php` - Form input warga dengan dropdown wilayah
- `dashboard/api/wilayah.php` - API untuk mengambil data wilayah dari EMSIFA
- `dashboard/api/warga_action.php` - API untuk menyimpan data warga

## API Endpoints EMSIFA

- `GET https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json` - Semua provinsi
- `GET https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{provinsi_id}.json` - Kota berdasarkan provinsi
- `GET https://www.emsifa.com/api-wilayah-indonesia/api/districts/{kota_id}.json` - Kecamatan berdasarkan kota
- `GET https://www.emsifa.com/api-wilayah-indonesia/api/villages/{kecamatan_id}.json` - Kelurahan berdasarkan kecamatan

## API Endpoints Lokal

- `GET api/wilayah.php?action=provinsi` - Mengambil semua provinsi
- `GET api/wilayah.php?action=kota&provinsi_id=ID_PROVINSI` - Mengambil kota berdasarkan provinsi
- `GET api/wilayah.php?action=kecamatan&kota_id=ID_KOTA` - Mengambil kecamatan berdasarkan kota
- `GET api/wilayah.php?action=kelurahan&kecamatan_id=ID_KECAMATAN` - Mengambil kelurahan berdasarkan kecamatan

## Cara Penggunaan

1. Buka halaman "Data Warga" di dashboard
2. Klik tombol "+ Tambah Warga"
3. Pilih Provinsi dari dropdown pertama
4. Kota/Kabupaten akan otomatis terisi berdasarkan provinsi yang dipilih
5. Pilih Kota/Kabupaten
6. Kecamatan akan otomatis terisi berdasarkan kota yang dipilih
7. Pilih Kecamatan
8. Kelurahan akan otomatis terisi berdasarkan kecamatan yang dipilih
9. Pilih Kelurahan

## Fitur Tambahan

- **Validasi**: Dropdown akan disabled sampai parent dropdown dipilih
- **Reset**: Saat form dibuka ulang, dropdown akan direset ke kondisi awal
- **Edit**: Saat edit data, dropdown akan otomatis terisi sesuai data yang ada
- **Error Handling**: Pesan error yang informatif jika terjadi masalah
- **Caching**: Data wilayah disimpan di hidden input untuk performa
- **Nama Wilayah**: Menyimpan nama wilayah (bukan ID) ke database

## Format Data EMSIFA

```json
[
  {
    "id": "11",
    "name": "ACEH"
  }
]
```

## Keunggulan EMSIFA API

- ✅ **Gratis**: Tidak memerlukan API key atau registrasi
- ✅ **Lengkap**: Data seluruh wilayah Indonesia
- ✅ **Update**: Data terupdate sesuai perubahan administratif
- ✅ **Stabil**: API yang reliable dan cepat
- ✅ **JSON**: Format response yang mudah diproses

## Troubleshooting

1. **Dropdown tidak terisi**:

   - Periksa koneksi internet
   - Pastikan EMSIFA API dapat diakses
   - Cek console browser untuk error JavaScript

2. **Error API**:

   - Periksa file `api/wilayah.php`
   - Pastikan `allow_url_fopen` diaktifkan di PHP
   - Cek error log PHP

3. **Data tidak tersimpan**:

   - Pastikan hidden input terisi dengan benar
   - Periksa validasi form
   - Cek response dari `warga_action.php`

4. **Edit data tidak muncul**:
   - Pastikan data wilayah di database sesuai format
   - Periksa fungsi pencarian ID berdasarkan nama

## Catatan Penting

- **Internet Required**: Fitur ini memerlukan koneksi internet untuk mengakses EMSIFA API
- **Performance**: Data di-cache di hidden input untuk mengurangi request API
- **Fallback**: Jika API tidak tersedia, dropdown akan menampilkan pesan error
- **Data Storage**: Nama wilayah (bukan ID) yang disimpan ke database untuk kemudahan pembacaan
