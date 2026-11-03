<?php
/* Pemeriksaan hak akses admin atau super admin sebelum diarahkan kembali ke halaman utama. */
require_once '../middleware/auth_check.php';
check_role(['super_admin', 'admin']);
header('Location: ../index.php');
exit();
?>
