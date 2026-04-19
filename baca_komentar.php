<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

// Cek keamanan
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Ubah status semua komentar menjadi 'sudah' dibaca
mysqli_query($conn, "UPDATE komentar SET status_baca = 'sudah' WHERE status_baca = 'belum'");

// Kembalikan ke dashboard
header("Location: admin_dashboard.php");
exit;
?>
