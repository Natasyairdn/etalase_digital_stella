<?php
/* Memeriksa hak akses, menghapus file gambar terkait, lalu menghapus data karya dari database. */
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';
check_login();

// Mengambil ID karya yang akan dihapus dari parameter URL.
$id = $_GET['id'] ?? null;
// Melanjutkan proses hanya jika ID karya tersedia.
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $project = $stmt->fetch();
    if ($project) {
        // Mencegah siswa menghapus karya milik siswa lain.
if ($_SESSION['role'] === 'siswa' && $project['user_id'] != $_SESSION['user_id']) {
            tampil_akses_ditolak("Anda tidak dapat menghapus karya milik siswa lain.");
        }
        // Menghapus file gambar jika file tersebut masih tersedia di folder uploads.
if (file_exists('uploads/' . $project['gambar'])) {
            unlink('uploads/' . $project['gambar']);
        }
        // Menyiapkan perintah untuk menghapus data karya dari database.
$del = $pdo->prepare("DELETE FROM projects WHERE id = :id");
        $del->execute([':id' => $id]);
    }
}
header('Location: index.php');
exit();
?>
