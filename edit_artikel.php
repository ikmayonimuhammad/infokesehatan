<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

if (!isset($_SESSION['login'])) { header("Location: index.php"); exit; }

$id_artikel = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pesan = "";

// PROSES UPDATE ARTIKEL
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $id_kategori = $_POST['id_kategori'];
    $konten = $_POST['konten']; // Mendukung kode HTML
    $gambar_lama = $_POST['gambar_lama'];
    
    $nama_file_baru = $gambar_lama;

    // Jika admin mengunggah gambar baru
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
            
            // Hapus file fisik gambar lama jika ada
            if(!empty($gambar_lama) && file_exists("assets/images/".$gambar_lama)){
                unlink("assets/images/".$gambar_lama);
            }
        } else {
            $pesan = "<div class='alert error' style='background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal: Format gambar tidak didukung.</div>";
        }
    }

    if (empty($pesan)) {
        // PREPARED STATEMENT UPDATE
        $stmt = mysqli_prepare($conn, "UPDATE artikel SET judul=?, id_kategori=?, konten=?, gambar=? WHERE id_artikel=?");
        mysqli_stmt_bind_param($stmt, "sissi", $judul, $id_kategori, $konten, $nama_file_baru, $id_artikel);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan = "<div class='alert success' style='background: var(--accent-primary); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Artikel berhasil diperbarui!</div>";
        } else {
            $pesan = "<div class='alert error' style='background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal mengupdate database.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// AMBIL DATA ARTIKEL UNTUK DITAMPILKAN DI FORM
$stmt_get = mysqli_prepare($conn, "SELECT * FROM artikel WHERE id_artikel=?");
mysqli_stmt_bind_param($stmt_get, "i", $id_artikel);
mysqli_stmt_execute($stmt_get);
$result = mysqli_stmt_get_result($stmt_get);
$data = mysqli_fetch_assoc($result);

include 'includes/header.php';
?>

<div class="container" style="max-width: 900px;">
    <a href="daftar_artikel.php" class="btn-back" style="margin-bottom: 25px;">&laquo; Batal & Kembali</a>
    
    <h2 style="color: var(--text-dark);">Edit Artikel</h2>
    <?php echo $pesan; ?>
    
    <?php if($data): ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="gambar_lama" value="<?php echo $data['gambar']; ?>">
        
        <div class="form-group">
            <label>Judul Artikel</label>
            <input type="text" name="judul" value="<?php echo htmlspecialchars($data['judul']); ?>" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="id_kategori" required>
                <?php 
                $kat = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                while($k = mysqli_fetch_assoc($kat)) {
                    $selected = ($k['id_kategori'] == $data['id_kategori']) ? "selected" : "";
                    echo "<option value='".$k['id_kategori']."' $selected>".$k['nama_kategori']."</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Ganti Gambar Sampul (Biarkan kosong jika tetap)</label>
            <input type="file" name="gambar" accept="image/*">
            <?php if(!empty($data['gambar'])): ?>
                <div style="margin-top: 10px;">
                    <small style="color: var(--text-light);">Gambar saat ini: <b><?php echo $data['gambar']; ?></b></small>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Konten Artikel (Support Kode HTML)</label>
            <div style="background: var(--bg-color); padding: 10px; border-radius: 8px; margin-bottom: 10px; font-size: 0.85em; color: var(--text-light); border-left: 4px solid var(--accent-secondary);">
                <b>Tips HTML:</b> Anda bisa menggunakan tag HTML untuk memformat tulisan ini agar lebih menarik di halaman pengunjung.
            </div>
            <textarea name="konten" rows="15" required><?php echo htmlspecialchars($data['konten']); ?></textarea>
        </div>

        <button type="submit" class="btn-submit" style="background: #3b82f6;">Simpan Perubahan</button>
    </form>
    <?php else: ?>
        <p style="text-align: center; color: var(--text-light);">Artikel tidak ditemukan.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
