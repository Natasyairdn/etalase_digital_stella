<?php
/* Pengaturan koneksi PHP ke database MySQL menggunakan PDO. */
// Menentukan alamat server, nama database, dan kredensial MySQL.
$host = 'localhost';
$db = 'db_etalase_stella';
$user = 'root';
$pass = '';
// Mencoba membuat koneksi PDO ke database.
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
// Menangani error jika koneksi database gagal.
} catch (PDOException $e) {
    die("Koneksi database gagal: ".$e->getMessage());
}
?>