<?php
session_start();
session_unset();  // Menghapus semua variabel sesi
session_destroy();  // Mengakhiri sesi

header("Location: loginUser.php");  // Redirect ke halaman login setelah logout
exit;
?>
