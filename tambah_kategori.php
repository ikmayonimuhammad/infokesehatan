<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['login'])) { header("Location: index.php"); exit; }

$pesan = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kategori = $_POST['nama_kategori'];
    
    // PREPARED STATEMENT INSERT KATEGORI
    $stmt = mysqli_prepare($conn, "INSERT INTO kategori (nama_kategori) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $nama_kategori);
    
    if(mysqli_stmt_execute($stmt)) {
        $pesan = "<div class='alert success' style='background: var(--accent-primary); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Kategori baru berhasil ditambahkan!</div>";
    } else {
        $pesan = "<div class='alert error' style='background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal menambahkan kategori.</div>";
    }
    mysqli_stmt_close($stmt);
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 500px;">
    <a href="admin_dashboard.php" class="btn-back" style="margin-bottom: 20px;">&laquo; Kembali ke Dashboard</a>
    
    <h2 style="color: var(--text-dark); text-align: center;">Tambah Kategori Baru</h2>
    <?php echo $pesan; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" required autofocus placeholder="Contoh: Gizi & Diet">
        </div>
        <button type="submit" class="btn-submit">Simpan Kategori</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
