<?php
/* Halaman utama etalase: mengambil data karya, melakukan pencarian, dan menampilkan kartu karya. */
require_once 'config/kuis_db.php';
require_once 'middleware/auth_check.php';
check_login();

// Mengambil kata kunci pencarian dari URL, lalu menghapus spasi di awal/akhir.
$q = trim($_GET['q'] ?? '');
// Menyiapkan query untuk mengambil data karya sekaligus nama pembuatnya.
$sql = "SELECT projects.*, users.nama AS pembuat
        FROM projects
        JOIN users ON projects.user_id = users.id";
$params = [];
// Jika ada kata kunci, tambahkan kondisi pencarian berdasarkan nama karya atau pembuat.
if ($q !== '') {
    $sql .= " WHERE projects.nama_project LIKE :q1 OR users.nama LIKE :q2";
    $params = [':q1' => "%$q%", ':q2' => "%$q%"];
}
$sql .= " ORDER BY projects.created_at DESC";
// Menjalankan query menggunakan prepared statement agar parameter pencarian aman.
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Mengecek apakah pengguna yang sedang login memiliki role siswa.
$is_siswa = $_SESSION['role'] === 'siswa';
$milik_saya = [];
$lainnya = [];
if ($is_siswa) {
    foreach ($projects as $p) {
        if ($p['user_id'] == $_SESSION['user_id']) { $milik_saya[] = $p; } else { $lainnya[] = $p; }
    }
}

// Fungsi untuk menampilkan satu kartu karya agar tampilan dapat digunakan berulang.
function tampil_kartu($p) {
    $detail = 'project_detail.php?id=' . $p['id'];
    ?>
        <div class="card-project">
            <a class="card-media" href="<?= $detail ?>">
                <img src="uploads/<?= htmlspecialchars($p['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nama_project']) ?>">
            </a>
            <div class="card-body">
                <div class="card-title"><a href="<?= $detail ?>"><?= htmlspecialchars($p['nama_project']) ?></a></div>
                <div class="card-meta">
                    <span>Oleh <?= htmlspecialchars($p['pembuat']) ?></span>
                    <span><?= date('d-m-Y', strtotime($p['created_at'])) ?></span>
                </div>
                <div class="card-text"><?= htmlspecialchars($p['deskripsi']) ?></div>
                <a href="<?= htmlspecialchars($p['link_project']) ?>" target="_blank" rel="noopener" class="card-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>Lihat project tautan</a>
                <?php if (bisa_kelola($p)): ?>
                <div class="card-actions">
                    <a href="project_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Edit</a>
                    <a href="project_hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus karya ini?')" class="btn btn-danger"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/></svg>Hapus</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php
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
    <title>Etalase Digital Stella</title>
    <link rel="icon" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <link rel="stylesheet" href="assets/css/main.css?v=6">
</head>
<body>

<?php render_navbar(''); ?>

<div class="container">
    <div class="catalog-bar">
        <h1>Katalog karya siswa <span class="count-badge"><?= count($projects) ?> karya</span></h1>
        <form method="GET" class="search-form" role="search">
            <label class="search-field">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari karya/nama pembuat" aria-label="Cari karya atau nama pembuat">
            </label>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
        <a href="project_tambah.php" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Tambah karya</a>
    </div>

    <?php if (count($projects) === 0): ?>
        <div class="empty">
            <?php if ($q !== ''): ?>
                <h3>Karya tidak ditemukan</h3>
                <p>Tidak ada karya atau pembuat yang cocok dengan “<?= htmlspecialchars($q) ?>”.</p>
                <a href="index.php" class="btn btn-ghost">Tampilkan semua karya</a>
            <?php else: ?>
                <h3>Belum ada karya</h3>
                <p>Etalase masih kosong. Unggah karya pertama untuk memulainya.</p>
                <a href="project_tambah.php" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Tambah karya</a>
            <?php endif; ?>
        </div>

    <?php elseif ($is_siswa): ?>
        <h2 class="section-title">Karya saya</h2>
        <?php if (count($milik_saya) > 0): ?>
            <div class="grid-projects">
                <?php foreach ($milik_saya as $p) { tampil_kartu($p); } ?>
            </div>
        <?php else: ?>
            <p class="section-empty">Kamu belum punya karya<?= $q !== '' ? ' yang cocok dengan pencarian' : '' ?>. <a href="project_tambah.php">Tambah karya</a></p>
        <?php endif; ?>

        <h2 class="section-title">Karya siswa lainnya</h2>
        <?php if (count($lainnya) > 0): ?>
            <div class="grid-projects">
                <?php foreach ($lainnya as $p) { tampil_kartu($p); } ?>
            </div>
        <?php else: ?>
            <p class="section-empty">Belum ada karya dari siswa lain.</p>
        <?php endif; ?>

    <?php else: ?>
        <div class="grid-projects">
            <?php foreach ($projects as $p) { tampil_kartu($p); } ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
