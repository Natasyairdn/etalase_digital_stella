<?php
/* Memproses unggahan karya baru, memvalidasi gambar, menyimpan data ke database, dan menampilkan formulir. */
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';
check_login();

// Variabel untuk menampung pesan kesalahan saat proses unggah.
$error = '';
// Memproses data formulir setelah tombol simpan ditekan.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_project = trim($_POST['nama_project']);
    $deskripsi = trim($_POST['deskripsi']);
    $link_project = trim($_POST['link_project']);
    $user_id = $_SESSION['user_id'];

    // Memeriksa apakah file gambar dikirim dan berhasil diterima server.
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Mengambil ekstensi file gambar dan mengubahnya menjadi huruf kecil.
$ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $error = "Format gambar harus JPG, PNG, GIF, atau WEBP.";
        } elseif ($_FILES['gambar']['size'] > 10 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 10 MB.";
        } else {
            // Membuat nama file unik agar tidak bertabrakan dengan file gambar lain.
$new_name = uniqid('project_', true) . '.' . $ext;
            // Memindahkan file gambar dari lokasi sementara ke folder uploads.
if (move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $new_name)) {
                // Menyiapkan query untuk menyimpan informasi karya ke tabel projects.
$stmt = $pdo->prepare("INSERT INTO projects (user_id, nama_project, deskripsi, gambar, link_project) VALUES (:user_id, :nama, :desk, :img, :link)");
                $stmt->execute([':user_id' => $user_id, ':nama' => $nama_project, ':desk' => $deskripsi, ':img' => $new_name, ':link' => $link_project]);
                header('Location: index.php');
                exit();
            } else {
                $error = "Gagal mengunggah file gambar.";
            }
        }
    } elseif (isset($_FILES['gambar']) && in_array($_FILES['gambar']['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
        $error = "Ukuran gambar melebihi batas server. Perkecil gambar, atau naikkan upload_max_filesize di php.ini.";
    } else {
        $error = "Gambar wajib diunggah.";
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
    <title>Tambah Karya - Etalase Digital Stella</title>
    <link rel="icon" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <link rel="stylesheet" href="assets/css/form.css?v=6">
</head>
<body>

<?php render_navbar('', true); ?>

<div class="form-container">
    <h2>Unggah karya digital baru</h2>
    <?php if($error): ?><div class="alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_project">Nama karya/aplikasi</label>
            <input type="text" id="nama_project" name="nama_project" placeholder="Contoh: Aplikasi IoT" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi karya</label>
            <textarea id="deskripsi" name="deskripsi" placeholder="Jelaskan fitur, latar belakang, dan kegunaan karyamu" required></textarea>
        </div>
        <div class="form-group">
            <label for="link_project">Link project (demo web, video, GitHub)</label>
            <input type="url" id="link_project" name="link_project" placeholder="https://..." required>
        </div>
        <div class="form-group">
            <label for="inputGambar">Foto/tangkapan layar karya (maks. 10 MB)</label>
            <input type="file" name="gambar" id="inputGambar" accept="image/*" required onchange="previewImage(event)">
            <div class="preview-box" id="previewBox">
                <img id="imgPreview" src="#" alt="Pratinjau gambar">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan &amp; publikasikan</button>
            <a href="index.php" class="btn btn-ghost">Batalkan</a>
        </div>
    </form>
</div>
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('imgPreview');
        output.src = reader.result;
        document.getElementById('previewBox').style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
</body>
</html>
