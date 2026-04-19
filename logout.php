<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hancurkan semua data session
session_unset();
session_destroy();

// Alihkan kembali ke Beranda setelah berhasil logout
header("Location: index.php");
exit;
?>
