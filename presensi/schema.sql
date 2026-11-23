-- ============================================================
-- schema.sql : DDL + Seeder  |  Aplikasi Presensi Siswa
-- Jalankan lewat phpMyAdmin (tab SQL / Import) atau:
--   mysql -u root < schema.sql
-- ============================================================
CREATE DATABASE IF NOT EXISTS presensi_siswa
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE presensi_siswa;

-- Urutan: kelas -> siswa -> presensi (tabel yang dirujuk dibuat lebih dulu)
CREATE TABLE IF NOT EXISTS kelas (
  id_kelas   INT AUTO_INCREMENT PRIMARY KEY,
  nama_kelas VARCHAR(20) NOT NULL UNIQUE,
  jurusan    VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS siswa (
  nisn     CHAR(10) PRIMARY KEY,
  nama     VARCHAR(100) NOT NULL,
  alamat   VARCHAR(255) NULL,
  id_kelas INT NOT NULL,
  CONSTRAINT fk_siswa_kelas FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS presensi (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nisn          CHAR(10) NOT NULL,
  tanggal_waktu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_presensi_siswa FOREIGN KEY (nisn) REFERENCES siswa(nisn)
) ENGINE=InnoDB;

-- Tabel pendukung login (berdiri sendiri)
CREATE TABLE IF NOT EXISTS users (
  id_user  INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- ===================== SEEDER =====================
INSERT INTO kelas (nama_kelas, jurusan) VALUES
  ('X PPLG 1',  'Pengembangan Perangkat Lunak dan Gim'),
  ('X PPLG 2',  'Pengembangan Perangkat Lunak dan Gim'),
  ('XI PPLG 1', 'Pengembangan Perangkat Lunak dan Gim'),
  ('XI PPLG 2', 'Pengembangan Perangkat Lunak dan Gim'),
  ('XII PPLG 1','Pengembangan Perangkat Lunak dan Gim');

-- Akun demo: username admin / password admin123 (disimpan sebagai hash)
INSERT INTO users (username, password) VALUES
  ('admin', '$2y$10$I2J/c6KSI5yFKYY0tPl92uRukYsXJTDjReJF0YiDfR8GHNoF0SO12');

-- Contoh siswa (NISN diawali 0 untuk menguji penyimpanan sebagai teks)
INSERT INTO siswa (nisn, nama, alamat, id_kelas) VALUES
  ('0012345678', 'Budi Santoso', 'Jl. Pahlawan No. 1, Samarinda', 3),
  ('0012345679', 'Sari Wulandari', 'Jl. Juanda No. 5, Samarinda', 3),
  ('0012345680', 'Andi Pratama', NULL, 4);
