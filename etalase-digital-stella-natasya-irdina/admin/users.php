<?php
/* Halaman khusus super admin untuk menambahkan admin, menampilkan daftar pengguna, dan menghapus akun yang diizinkan. */
require_once '../config/kuis_db.php';
require_once '../middleware/auth_check.php';
// Halaman ini hanya boleh dibuka oleh pengguna dengan role super_admin.
check_role(['super_admin']);

// Memeriksa apakah formulir tambah admin dikirim.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_admin'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    // Menyiapkan query untuk memasukkan akun admin baru.
$stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :pass, 'admin')");
    $stmt->execute([':nama' => $nama, ':email' => $email, ':pass' => $pass]);
}

// Memproses penghapusan akun jika parameter hapus tersedia di URL.
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'super_admin'");
    $stmt->execute([':id' => $_GET['hapus']]);
    header('Location: users.php');
    exit();
}

// Mengambil seluruh data pengguna untuk ditampilkan dalam tabel.
$users = $pdo->query("SELECT * FROM users ORDER BY role ASC")->fetchAll();
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
    <title>Kelola User - Etalase Digital Stella</title>
    <link rel="icon" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css?v=6">
    <link rel="stylesheet" href="../assets/css/form.css?v=6">
    <link rel="stylesheet" href="../assets/css/admin.css?v=6">
</head>
<body>
<?php render_navbar('../', true); ?>
<div class="page-wrap">
    <h2>Kelola user</h2>

    <div class="form-container">
        <h3>Tambah admin baru</h3>
        <form method="POST">
            <div class="form-group">
                <label for="nama">Nama admin</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="email">Email admin</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="tambah_admin" class="btn btn-primary">Tambah admin</button>
        </form>
    </div>

    <div class="table-container">
        <h3>Daftar pengguna</h3>
        <table class="custom-table">
            <tr>
                <th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th>
            </tr>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nama']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge-role badge-<?= $u['role'] ?>"><?= htmlspecialchars(str_replace('_', ' ', $u['role'])) ?></span></td>
                <td>
                    <?php if($u['role'] !== 'super_admin'): ?>
                        <a href="users.php?hapus=<?= $u['id'] ?>" onclick="return confirm('Hapus user ini?')" class="btn btn-danger" style="padding: 6px 14px; font-size: 13px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>Hapus</a>
                    <?php else: ?>
                        <span class="protected">Dilindungi</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>
