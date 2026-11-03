<?php
/* Proses login: memeriksa email dan password, lalu menyimpan identitas pengguna ke session. */

// Menampilkan seluruh error PHP selama proses pengembangan.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Memulai session agar data login dapat disimpan dan digunakan antarhalaman.
session_start();

require_once '../config/kuis_db.php';

$error = '';

// Blok ini dijalankan ketika formulir login dikirim.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Menyiapkan query untuk mencari akun berdasarkan email.
$stmt = $pdo->prepare("
        SELECT id, nama, email, password, role
        FROM users
        WHERE email = :email
        LIMIT 1
    ");

    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch();

    // Memeriksa apakah email ditemukan dan password sesuai dengan hash di database.
if (!$user) {

        $error = "Email tidak ditemukan di database.";

    } elseif (!password_verify($password, $user['password'])) {

        $error = "Password salah.";

    } else {

        // Menyimpan identitas pengguna ke session setelah login berhasil.
$_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        header('Location: ../index.php');
        exit();
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
    <title>Login - Etalase Digital Stella</title>
    <link rel="icon" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="../assets/css/auth.css?v=6">
</head>
<body>
<div class="auth-wrapper">
    <div class="frames" aria-hidden="true"><span class="frame frame-a"></span><span class="frame frame-b"></span><span class="frame frame-c"></span></div>
    <main class="auth-card">
        <div class="auth-logo"><img src="../assets/img/logo.png" alt="Logo Telkom Schools"></div>
        <h2>Etalase Digital Stella</h2>
        <p class="auth-sub">Katalog karya digital siswa</p>
        <?php if($error): ?><div class="alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-control">
                <label for="email">Username</label>
                <input type="email" id="email" name="email" placeholder="nama@stella.sch.id" required>
            </div>
            <div class="form-control">
                <label for="password">Kata sandi</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:13px;">Masuk sekarang</button>
        </form>
        <div class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar baru</a>
        </div>
    </main>
</div>
</body>
</html>
