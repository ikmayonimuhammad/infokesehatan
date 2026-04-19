<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Cek Komentar Masuk (Notifikasi)
$cek_komen = mysqli_query($conn, "SELECT COUNT(*) as belum_baca FROM komentar WHERE status_baca='belum'");
$notif = mysqli_fetch_assoc($cek_komen);
$jumlah_notif = $notif['belum_baca'];

include 'includes/header.php';
?>

<div class="container">
    
    <?php if($jumlah_notif > 0): ?>
        <div class="notification-bar">
            <div>
                <b style="font-size: 1.1em;">🔔 Notifikasi:</b> Ada <b><?php echo $jumlah_notif; ?></b> komentar baru dari pengunjung yang belum dibaca!
            </div>
            <a href="baca_komentar.php" class="btn-read-notif">Tandai Sudah Dibaca</a>
        </div>
    <?php endif; ?>

    <h2 style="color: var(--text-dark);">Dashboard Administrator</h2>
    <p style="font-size: 1.1em; color: var(--text-dark);">Selamat datang, <b><?php echo $_SESSION['nama']; ?></b>! <br>
    <span style="color: var(--text-light); font-size: 0.9em;">Hak Akses Anda: <?php echo strtoupper($_SESSION['role']); ?></span></p>

    <div class="admin-grid">
        <a href="tambah_artikel.php" class="admin-card">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            <h3>Tulis Artikel</h3>
            <p>Buat dan publikasikan konten kesehatan baru.</p>
        </a>

        <a href="daftar_artikel.php" class="admin-card">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            <h3>Kelola Artikel</h3>
            <p>Lihat, edit, atau hapus artikel yang sudah diterbitkan.</p>
        </a>

        <a href="tambah_kategori.php" class="admin-card">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            <h3>Tambah Kategori</h3>
            <p>Buat kategori baru untuk mengelompokkan artikel.</p>
        </a>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="edit_tentang.php" class="admin-card">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <h3>Info Tentang Kami</h3>
            <p>Edit profil, informasi, dan kontak perusahaan.</p>
        </a>

        <a href="tambah_user.php" class="admin-card">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            <h3>Tambah User</h3>
            <p>Kelola akun penulis dan administrator sistem.</p>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
