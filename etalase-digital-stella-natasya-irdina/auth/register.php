<?php
/* Proses pendaftaran akun siswa baru beserta tampilan formulir registrasi. */
// Memulai session pengguna.
session_start();
require_once '../config/kuis_db.php';
$error = '';
// Memproses data ketika formulir pendaftaran dikirim.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    // Mengubah password menjadi hash sebelum disimpan, bukan menyimpan teks aslinya.
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    // Mencoba menyimpan akun baru ke tabel users.
try {
        $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, 'siswa')");
        $stmt->execute([':nama' => $nama, ':email' => $email, ':password' => $password]);
        header('Location: login.php');
        exit();
    // Menangani kegagalan penyimpanan, misalnya email sudah digunakan.
} catch (PDOException $e) {
        $error = "Email sudah terdaftar atau terjadi kesalahan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#A71D2A">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Daftar - Etalase Digital Stella</title>
    <link rel="icon" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="../assets/css/auth.css?v=6">
</head>
<body>
<div class="auth-wrapper">
    <div class="frames" aria-hidden="true"><span class="frame frame-a"></span><span class="frame frame-b"></span><span class="frame frame-c"></span></div>
    <main class="auth-card">
        <div class="auth-logo"><img src="../assets/img/logo.png" alt="Logo Telkom Schools"></div>
        <h2>Daftar akun siswa</h2>
        <p class="auth-sub">Cukup nama, email, dan kata sandi.</p>
        <?php if($error): ?><div class="alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-control">
                <label for="nama">Nama lengkap</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-control">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-control">
                <label for="password">Kata sandi</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:13px;">Daftar</button>
        </form>
        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </main>
</div>
</body>
</html>
