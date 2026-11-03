<?php
/* Mengambil data karya, memeriksa hak akses, lalu memproses perubahan informasi dan gambar karya. */
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';
check_login();

// Mengambil ID karya dari parameter URL.
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit(); }

// Mengambil data karya yang akan diedit berdasarkan ID.
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
$stmt->execute([':id' => $id]);
$project = $stmt->fetch();
if (!$project) {
    tampil_pesan_halaman('Karya tidak ditemukan', 'Karya ini mungkin sudah dihapus atau linknya salah.', 404);
}

// Siswa hanya boleh mengedit karya miliknya sendiri.
if ($_SESSION['role'] === 'siswa' && $project['user_id'] != $_SESSION['user_id']) {
    tampil_akses_ditolak("Anda tidak dapat mengedit karya milik siswa lain.");
}

// Menyimpan pesan kesalahan selama proses edit.
$error = '';
// Memproses perubahan ketika formulir edit dikirim.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi = trim($_POST['deskripsi']);
    $link_project = trim($_POST['link_project']);
    $gambar_nama = $project['gambar'];
    $gambar_lama = null;

    // Memeriksa apakah pengguna mengunggah gambar pengganti.
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $error = "Format gambar harus JPG, PNG, GIF, atau WEBP.";
        } elseif ($_FILES['gambar']['size'] > 10 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 10 MB.";
        } else {
            $new_name = uniqid('project_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $new_name)) {
                $gambar_lama = $project['gambar'];
                $gambar_nama = $new_name;
            } else {
                $error = "Gagal mengunggah file gambar.";
            }
        }
    } elseif (isset($_FILES['gambar']) && in_array($_FILES['gambar']['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
        $error = "Ukuran gambar melebihi batas server. Perkecil gambar, atau naikkan upload_max_filesize di php.ini.";
    }

    // Data diperbarui hanya jika tidak ada kesalahan validasi.
if ($error === '') {
        $stmt = $pdo->prepare("UPDATE projects SET nama_project = :nama, deskripsi = :desk, gambar = :img, link_project = :link WHERE id = :id");
        $stmt->execute([':nama' => $nama_project, ':desk' => $deskripsi, ':img' => $gambar_nama, ':link' => $link_project, ':id' => $id]);
        if ($gambar_lama && file_exists('uploads/' . $gambar_lama)) {
            unlink('uploads/' . $gambar_lama);
        }
        header('Location: index.php');
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
    <title>Edit Karya - Etalase Digital Stella</title>
    <link rel="icon" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <link rel="stylesheet" href="assets/css/form.css?v=6">
</head>
<body>

<?php render_navbar('', true); ?>

<div class="form-container">
    <h2>Edit karya digital</h2>
    <?php if($error): ?><div class="alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_project">Nama karya/aplikasi</label>
            <input type="text" id="nama_project" name="nama_project" value="<?= htmlspecialchars($project['nama_project']) ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi karya</label>
            <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($project['deskripsi']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="link_project">Link project (demo web, video, GitHub)</label>
            <input type="url" id="link_project" name="link_project" value="<?= htmlspecialchars($project['link_project']) ?>" required>
        </div>
        <div class="form-group">
            <label for="gambar">Ganti foto/tangkapan layar (opsional, maks. 10 MB)</label>
            <div class="current-img"><img src="uploads/<?= htmlspecialchars($project['gambar']) ?>" alt="Gambar saat ini"></div>
            <input type="file" id="gambar" name="gambar" accept="image/*">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
            <a href="index.php" class="btn btn-ghost">Batalkan</a>
        </div>
    </form>
</div>
</body>
</html>
