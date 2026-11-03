<?php
/* Menampilkan detail satu karya berdasarkan ID, termasuk pembuat, tanggal, deskripsi, dan tautan. */
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';
check_login();

// Mengambil ID karya yang ingin ditampilkan dari URL.
$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit(); }

// Mengambil detail karya dan menggabungkan nama pembuat dari tabel users.
$stmt = $pdo->prepare("SELECT projects.*, users.nama AS pembuat
                       FROM projects
                       JOIN users ON projects.user_id = users.id
                       WHERE projects.id = :id");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch();
if (!$p) {
    tampil_pesan_halaman('Karya tidak ditemukan', 'Karya ini mungkin sudah dihapus atau linknya salah.', 404);
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
    <title><?= htmlspecialchars($p['nama_project']) ?> - Etalase Digital Stella</title>
    <link rel="icon" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <link rel="stylesheet" href="assets/css/main.css?v=6">
</head>
<body>

<?php render_navbar('', true); ?>

<!-- Bagian utama halaman yang menampilkan gambar dan informasi detail karya. -->
    <div class="detail">
    <div class="detail-media">
        <img src="uploads/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama_project']) ?>">
    </div>
    <div class="detail-info">
        <h1><?= htmlspecialchars($p['nama_project']) ?></h1>
        <div class="detail-meta">
            <span class="chip">Oleh <b><?= htmlspecialchars($p['pembuat']) ?></b></span>
            <span class="chip">Diunggah <?= date('d-m-Y', strtotime($p['created_at'])) ?></span>
        </div>
        <div class="detail-desc"><?= nl2br(htmlspecialchars($p['deskripsi'])) ?></div>
        <div class="detail-actions">
            <a href="<?= htmlspecialchars($p['link_project']) ?>" target="_blank" rel="noopener" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>Buka project</a>
            <button type="button" class="btn btn-ghost" onclick="salinLink(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V6a2 2 0 0 1 2-2h9"/></svg>Salin link</button>
            <?php if (bisa_kelola($p)): ?>
                <a href="project_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Edit</a>
                <a href="project_hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus karya ini?')" class="btn btn-danger"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>Hapus</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript untuk fitur menyalin tautan halaman detail karya. -->
<script>

// Mengambil alamat halaman saat ini dan menyalinnya ke clipboard.
function salinLink(btn) {
    var url = location.href;
    var teks = btn.lastChild;
    var awal = teks.textContent;
    function selesai() {
        teks.textContent = 'Link tersalin';
        setTimeout(function () { teks.textContent = awal; }, 1800);
    }
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(selesai);
    } else {
        var i = document.createElement('input');
        i.value = url; document.body.appendChild(i); i.select();
        document.execCommand('copy'); document.body.removeChild(i); selesai();
    }
}
</script>
</body>
</html>
