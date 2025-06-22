-- Setup tabel wilayah untuk aplikasi warga
-- Jalankan file ini di database MySQL

-- Tabel provinsi
CREATE TABLE IF NOT EXISTS wilayah_provinsi (
    id VARCHAR(2) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL
);

-- Tabel kabupaten/kota
CREATE TABLE IF NOT EXISTS wilayah_kabupaten (
    id VARCHAR(4) PRIMARY KEY,
    provinsi_id VARCHAR(2) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    FOREIGN KEY (provinsi_id) REFERENCES wilayah_provinsi(id)
);

-- Tabel kecamatan
CREATE TABLE IF NOT EXISTS wilayah_kecamatan (
    id VARCHAR(6) PRIMARY KEY,
    kabupaten_id VARCHAR(4) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    FOREIGN KEY (kabupaten_id) REFERENCES wilayah_kabupaten(id)
);

-- Tabel desa/kelurahan
CREATE TABLE IF NOT EXISTS wilayah_desa (
    id VARCHAR(10) PRIMARY KEY,
    kecamatan_id VARCHAR(6) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    FOREIGN KEY (kecamatan_id) REFERENCES wilayah_kecamatan(id)
);

-- Insert data provinsi Jawa Barat sebagai contoh
INSERT IGNORE INTO wilayah_provinsi (id, nama) VALUES 
('32', 'JAWA BARAT');

-- Insert data kabupaten/kota di Jawa Barat
INSERT IGNORE INTO wilayah_kabupaten (id, provinsi_id, nama) VALUES 
('3201', '32', 'KABUPATEN BOGOR'),
('3202', '32', 'KABUPATEN SUKABUMI'),
('3203', '32', 'KABUPATEN CIANJUR'),
('3204', '32', 'KABUPATEN BANDUNG'),
('3205', '32', 'KABUPATEN GARUT'),
('3206', '32', 'KABUPATEN TASIKMALAYA'),
('3207', '32', 'KABUPATEN CIAMIS'),
('3208', '32', 'KABUPATEN KUNINGAN'),
('3209', '32', 'KABUPATEN CIREBON'),
('3210', '32', 'KABUPATEN MAJALENGKA'),
('3211', '32', 'KABUPATEN SUMEDANG'),
('3212', '32', 'KABUPATEN INDRAMAYU'),
('3213', '32', 'KABUPATEN SUBANG'),
('3214', '32', 'KABUPATEN PURWAKARTA'),
('3215', '32', 'KABUPATEN KARAWANG'),
('3216', '32', 'KABUPATEN BEKASI'),
('3217', '32', 'KABUPATEN BANDUNG BARAT'),
('3218', '32', 'KABUPATEN PANGANDARAN'),
('3271', '32', 'KOTA BOGOR'),
('3272', '32', 'KOTA SUKABUMI'),
('3273', '32', 'KOTA BANDUNG'),
('3274', '32', 'KOTA CIREBON'),
('3275', '32', 'KOTA BEKASI'),
('3276', '32', 'KOTA DEPOK'),
('3277', '32', 'KOTA CIMAHI'),
('3278', '32', 'KOTA TASIKMALAYA'),
('3279', '32', 'KOTA BANJAR');

-- Insert data kecamatan di Bandung sebagai contoh
INSERT IGNORE INTO wilayah_kecamatan (id, kabupaten_id, nama) VALUES 
('327301', '3273', 'BANDUNG WETAN'),
('327302', '3273', 'BANDUNG UTARA'),
('327303', '3273', 'BANDUNG SELATAN'),
('327304', '3273', 'BANDUNG KULON'),
('327305', '3273', 'BANDUNG KIDUL'),
('327306', '3273', 'BUAHBATU'),
('327307', '3273', 'RAJAPOLOS'),
('327308', '3273', 'GEDEBAGE'),
('327309', '3273', 'CIBIRU'),
('327310', '3273', 'CINAMBO'),
('327311', '3273', 'ANDIR'),
('327312', '3273', 'CICENDO'),
('327313', '3273', 'BANDUNG WETAN'),
('327314', '3273', 'ASTANA ANYAR'),
('327315', '3273', 'REGOL'),
('327316', '3273', 'LENGKONG'),
('327317', '3273', 'BANDUNG KIDUL'),
('327318', '3273', 'BUAHBATU'),
('327319', '3273', 'RAJAPOLOS'),
('327320', '3273', 'GEDEBAGE'),
('327321', '3273', 'CIBIRU'),
('327322', '3273', 'CINAMBO'),
('327323', '3273', 'MANDALAJATI'),
('327324', '3273', 'UJUNG BERUNG'),
('327325', '3273', 'ARCAMANIK'),
('327326', '3273', 'ANTAPANI'),
('327327', '3273', 'BANDUNG WETAN'),
('327328', '3273', 'BANDUNG UTARA'),
('327329', '3273', 'BANDUNG SELATAN'),
('327330', '3273', 'BANDUNG KULON');

-- Insert data kelurahan di Bandung sebagai contoh
INSERT IGNORE INTO wilayah_desa (id, kecamatan_id, nama) VALUES 
('327301001', '327301', 'CITARUM'),
('327301002', '327301', 'TAMANSARI'),
('327301003', '327301', 'CITARUM'),
('327301004', '327301', 'TAMANSARI'),
('327302001', '327302', 'CITARUM'),
('327302002', '327302', 'TAMANSARI'),
('327302003', '327302', 'CITARUM'),
('327302004', '327302', 'TAMANSARI'),
('327303001', '327303', 'CITARUM'),
('327303002', '327303', 'TAMANSARI'),
('327303003', '327303', 'CITARUM'),
('327303004', '327303', 'TAMANSARI'),
('327304001', '327304', 'CITARUM'),
('327304002', '327304', 'TAMANSARI'),
('327304003', '327304', 'CITARUM'),
('327304004', '327304', 'TAMANSARI'),
('327305001', '327305', 'CITARUM'),
('327305002', '327305', 'TAMANSARI'),
('327305003', '327305', 'CITARUM'),
('327305004', '327305', 'TAMANSARI');

-- Catatan: Data di atas hanya contoh untuk Jawa Barat dan Bandung
-- Untuk data lengkap seluruh Indonesia, Anda perlu mengimport data dari BPS atau sumber resmi lainnya 