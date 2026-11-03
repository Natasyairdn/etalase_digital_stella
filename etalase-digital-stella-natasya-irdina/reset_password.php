<?php
/* Script administrasi untuk membuat ulang hash password akun contoh dan memperbaruinya di database. */

require_once 'config/kuis_db.php';

// Daftar akun yang password-nya akan dibuat ulang.
$accounts = [
    [
        'email' => 'superadmin@stella.sch.id',
        'password' => 'superadmin123'
    ],
    [
        'email' => 'admin@stella.sch.id',
        'password' => 'admin123'
    ],
    [
        'email' => 'siswa@stella.sch.id',
        'password' => 'siswa123'
    ]
];

// Mengulang setiap akun agar proses reset dilakukan satu per satu.
foreach ($accounts as $account) {

    // Membuat hash password baru sebelum disimpan ke database.
$hash = password_hash(
        $account['password'],
        PASSWORD_DEFAULT
    );

    // Menyiapkan query untuk memperbarui password berdasarkan email akun.
$stmt = $pdo->prepare("
        UPDATE users
        SET password = :password
        WHERE email = :email
    ");

    $stmt->execute([
        ':password' => $hash,
        ':email' => $account['email']
    ]);
}

// Menampilkan informasi hasil reset password pada browser.
echo "<h1>Password semua akun berhasil diubah!</h1>";

echo "<hr>";

echo "<h3>Super Admin</h3>";
echo "Email: superadmin@stella.sch.id<br>";
echo "Password: superadmin123<br>";

echo "<h3>Admin</h3>";
echo "Email: admin@stella.sch.id<br>";
echo "Password: admin123<br>";

echo "<h3>Siswa</h3>";
echo "Email: siswa@stella.sch.id<br>";
echo "Password: siswa123<br>";