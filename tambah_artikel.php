<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['login'])) { header("Location: index.php"); exit; }

$pesan = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $id_kategori = $_POST['id_kategori'];
    $penulis = $_POST['penulis'];
    $konten = $_POST['konten']; // Menerima kode HTML
    $nama_file_baru = "";

    // LOGIKA SECURE UPLOAD
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($mime_type, $allowed_types)) {
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $nama_file_baru = uniqid('img_', true) . '.' . strtolower($ext);
            move_uploaded_file($file_tmp, "assets/images/" . $nama_file_baru);
        } else {
            $pesan = "<div class='alert error' style='background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal: Format gambar tidak valid!</div>";
        }
    }

    if (empty($pesan)) {
        // PREPARED STATEMENT (Aman menyimpan string HTML)
        $stmt = mysqli_prepare($conn, "INSERT INTO artikel (judul, id_kategori, penulis, konten, gambar, tanggal_publikasi) VALUES (?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, "sisss", $judul, $id_kategori, $penulis, $konten, $nama_file_baru);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan = "<div class='alert success' style='background: var(--accent-primary); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Artikel berhasil diterbitkan!</div>";
        } else {
            $pesan = "<div class='alert error' style='background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal menerbitkan artikel.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 900px;">
    <a href="admin_dashboard.php" class="btn-back" style="margin-bottom: 25px;">&laquo; Kembali ke Dashboard</a>
    
    <h2 style="color: var(--text-dark);">Tulis Artikel Baru</h2>
    <?php echo $pesan; ?>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul Artikel</label>
            <input type="text" name="judul" placeholder="Masukkan judul artikel" required>
        </div>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 250px;">
                <label>Kategori</label>
                <select name="id_kategori" required>
                    <?php 
                    $kat = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                    while($k = mysqli_fetch_assoc($kat)) echo "<option value='".$k['id_kategori']."'>".$k['nama_kategori']."</option>";
                    ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 250px;">
                <label>Penulis</label>
                <input type="text" name="penulis" value="<?php echo $_SESSION['nama']; ?>" readonly style="background: var(--bg-color); cursor: not-allowed;">
            </div>
        </div>

        <div class="form-group">
            <label>Gambar Sampul</label>
            <input type="file" name="gambar" accept="image/*" required>
            <small style="color: var(--text-light);">Format disarankan: JPG, PNG, atau WebP.</small>
        </div>

        <div class="form-group">
            <label>Konten Artikel (Support Kode HTML)</label>
            <div style="background: var(--bg-color); padding: 10px; border-radius: 8px; margin-bottom: 10px; font-size: 0.85em; color: var(--text-light); border-left: 4px solid var(--accent-secondary);">
                <b>Tips Cepat HTML:</b> <br>
                <code>&lt;b&gt;Teks Tebal&lt;/b&gt;</code> | 
                <code>&lt;i&gt;Miring&lt;/i&gt;</code> | 
                <code>&lt;a href="URL"&gt;Link&lt;/a&gt;</code> | 
                <code>&lt;br&gt;</code> (Baris Baru) | 
                <code>&lt;p&gt;Paragraf&lt;/p&gt;</code>
            </div>
            <textarea name="konten" rows="15" required placeholder="Tulis konten artikel Anda di sini..."></textarea>
        </div>

        <button type="submit" class="btn-submit">Publikasikan Artikel</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
