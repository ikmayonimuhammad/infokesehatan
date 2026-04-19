<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }

$pesan = "";

// PROSES SIMPAN DATA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $konten_baru = $_POST['konten_html']; // Input HTML dibiarkan asli, diamankan lewat binding
    
    // PREPARED STATEMENT UPDATE
    $stmt = mysqli_prepare($conn, "UPDATE tentang_kami SET konten_html = ? WHERE id = 1");
    mysqli_stmt_bind_param($stmt, "s", $konten_baru);
    
    if(mysqli_stmt_execute($stmt)) {
        $pesan = "<div class='alert success' style='background: var(--accent-primary); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Halaman Tentang Kami berhasil diperbarui!</div>";
    } else {
        $pesan = "<div class='alert error' style='background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal memperbarui data.</div>";
    }
    mysqli_stmt_close($stmt);
}

// AMBIL DATA
$query = mysqli_query($conn, "SELECT * FROM tentang_kami WHERE id = 1");
$data = mysqli_fetch_assoc($query);

include 'includes/header.php';
?>

<div class="container">
    <a href="admin_dashboard.php" class="btn-back" style="margin-bottom: 25px;">&laquo; Kembali ke Dashboard</a>

    <h2 style="color: var(--text-dark);">Edit Info "Tentang Kami"</h2>
    <?php echo $pesan; ?>

    <form action="" method="POST">
        <div class="form-group">
            <textarea name="konten_html" rows="15" required style="width: 100%; padding: 15px; font-family: monospace; font-size: 1.05em; background: var(--bg-color); color: var(--text-dark); border: 2px solid var(--border-color); border-radius: 8px;"><?php echo htmlspecialchars($data['konten_html']); ?></textarea>
            <small style="color: var(--text-light); display: block; margin-top: 10px;">Gunakan tag HTML (seperti &lt;p&gt;, &lt;b&gt;, &lt;a&gt;) untuk memformat teks.</small>
        </div>
        <button type="submit" class="btn-submit" style="width: auto; padding: 12px 30px;">Simpan Profil</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
