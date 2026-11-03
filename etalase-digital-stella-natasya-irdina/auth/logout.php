<?php
/* Mengakhiri sesi pengguna dan mengarahkan pengguna kembali ke halaman login. */
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>