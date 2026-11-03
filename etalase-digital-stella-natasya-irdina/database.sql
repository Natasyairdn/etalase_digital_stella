-- Membuat database jika belum tersedia.
CREATE DATABASE IF NOT EXISTS db_etalase_stella;
USE db_etalase_stella;

-- Tabel users menyimpan data akun, password hash, dan role pengguna.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'siswa') DEFAULT 'siswa'
);

-- Tabel projects menyimpan karya yang diunggah oleh pengguna.
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_project VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(255) NOT NULL,
    link_project VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Data akun contoh untuk mengakses aplikasi.
-- Akun Default (Password semua akun: password123)
INSERT INTO users (nama, email, password, role) VALUES
('Super Admin Stella', 'superadmin@stella.sch.id', '$2y$12$yueu5ec.jK.E0VKRU7lQweIPQQi/0JMvzkA0yviOgeIjoCTQXPU3.', 'super_admin'),
('Admin Stella', 'admin@stella.sch.id', '$2y$12$ROK11TyyeK5vWKU60Nq7cOWHrAwt7n2HGTWHLA7CbNYoHuoWNxw.i', 'admin'),
('Siswa Contoh', 'siswa@stella.sch.id', '$2y$12$aiEXQPPghO8TCY27UQynq.8S8isjQn/FYg0E2ih5ClYGnlxF2omP2', 'siswa');
