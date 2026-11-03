<?php
/* Kumpulan fungsi untuk memeriksa login, role pengguna, hak kelola karya, dan menampilkan navigasi. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menentukan alamat dasar proyek agar tautan tetap benar dari folder berbeda.
function base_url_proyek() {
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    if (preg_match('#/(admin|auth)$#', $dir)) {
        $dir = rtrim(str_replace('\\', '/', dirname($dir)), '/');
    }
    return $dir;
}

// Menampilkan halaman pesan seperti akses ditolak atau data tidak ditemukan.
function tampil_pesan_halaman($judul, $pesan, $status = 403) {
    $dir = base_url_proyek();
    http_response_code($status);
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
    <title><?= htmlspecialchars($judul) ?> - Etalase Digital Stella</title>
    <link rel="icon" href="<?= htmlspecialchars($dir) ?>/assets/img/logo.png">
    <link rel="stylesheet" href="<?= htmlspecialchars($dir) ?>/assets/css/style.css?v=6">
</head>
<body>
<div class="denied">
    <div class="denied-card">
        <h1><?= htmlspecialchars($judul) ?></h1>
        <p><?= htmlspecialchars($pesan) ?></p>
        <a href="<?= htmlspecialchars($dir) ?>/index.php" class="btn btn-primary">Kembali ke etalase</a>
    </div>
</div>
</body>
</html>
<?php
    exit();
}

// Memanggil halaman pesan dengan status HTTP 403 (akses ditolak).
function tampil_akses_ditolak($pesan) {
    tampil_pesan_halaman('Akses ditolak', $pesan, 403);
}

// Memastikan pengguna sudah login sebelum membuka halaman yang dilindungi.
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: auth/login.php');
        exit();
    }
}

// Memastikan role pengguna termasuk dalam daftar role yang diizinkan.
function check_role($roles = []) {
    check_login();
    if (!in_array($_SESSION['role'], $roles)) {
        tampil_akses_ditolak("Anda tidak memiliki hak akses ke halaman ini.");
    }
}

// Admin dan super admin boleh mengelola semua karya, siswa hanya karyanya sendiri.
// Menentukan apakah pengguna boleh mengelola karya tertentu.
function bisa_kelola($project) {
    return in_array($_SESSION['role'], ['super_admin', 'admin'])
        || $_SESSION['user_id'] == $project['user_id'];
}

// $base: '' untuk halaman di folder utama, '../' untuk halaman di folder admin.
// $kembali: true menampilkan tombol "Kembali ke etalase" (halaman tambah, edit, detail).
// Membuat navbar yang menampilkan identitas, menu sesuai role, dan tombol logout.
function render_navbar($base = '', $kembali = false) {
    $nama = $_SESSION['nama'] ?? '';
    $role = str_replace('_', ' ', $_SESSION['role'] ?? '');
    $inisial = strtoupper(mb_substr($nama, 0, 1));
    ?>
<div class="navbar">
    <a class="brand" href="<?= $base ?>index.php"><img class="brand-logo" src="<?= $base ?>assets/img/logo.png" alt="Logo Telkom Schools"><span class="brand-name">Etalase Digital Stella</span></a>
    <div class="navbar-user">
        <?php if ($kembali): ?>
            <a href="<?= $base ?>index.php" class="btn btn-ghost">Kembali ke etalase</a>
        <?php else: ?>
            <span class="user-chip">
                <span class="avatar"><?= htmlspecialchars($inisial) ?></span>
                <span>Halo, <b><?= htmlspecialchars((($_SESSION['role'] ?? '') === 'siswa') ? 'Siswa' : $nama) ?></b></span>
            </span>
            <?php if (($_SESSION['role'] ?? '') === 'super_admin'): ?>
                <a href="<?= $base ?>admin/users.php" class="btn btn-ghost">Kelola user</a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= $base ?>auth/logout.php" class="btn btn-ghost">Log out</a>
    </div>
</div>
<?php
}
?>
