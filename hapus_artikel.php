<?php
// Pastikan session sudah berjalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/koneksi.php';

// PERLINDUNGAN: Jika belum login atau session habis, lempar ke index.php
if (!isset($_SESSION['login'])) {
    header("Location: index.php"); // <--- Arahkan ke beranda (index.php)
    exit;
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT gambar FROM artikel WHERE id_artikel = '$id'");
$row = mysqli_fetch_assoc($data);

// Hapus file gambar dari folder
if($row['gambar'] && file_exists("assets/images/".$row['gambar'])) {
    unlink("assets/images/".$row['gambar']);
}

// Hapus data dari database
mysqli_query($conn, "DELETE FROM artikel WHERE id_artikel = '$id'");
header("Location: daftar_artikel.php");
exit;
?>
