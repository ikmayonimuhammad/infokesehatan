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

include 'includes/header.php';
?>

<a href="admin_dashboard.php" class="btn-back">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    Kembali ke Dashboard
</a>

<h2>Daftar & Kelola Artikel</h2>

<div style="overflow-x: auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Penulis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT artikel.*, kategori.nama_kategori 
                      FROM artikel 
                      LEFT JOIN kategori ON artikel.id_kategori = kategori.id_kategori 
                      ORDER BY tanggal_publikasi DESC";
            $result = mysqli_query($conn, $query);

            while($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td>
                    <?php if($row['gambar']): ?>
                        <img src="assets/images/<?php echo $row['gambar']; ?>" width="60" style="border-radius: 4px;">
                    <?php else: ?>
                        <span style="color:#ccc;">No Image</span>
                    <?php endif; ?>
                </td>
                <td><b><?php echo $row['judul']; ?></b></td>
                <td><?php echo $row['nama_kategori']; ?></td>
                <td><?php echo $row['penulis']; ?></td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="edit_artikel.php?id=<?php echo $row['id_artikel']; ?>" class="btn-action edit">Edit</a>
                        <a href="hapus_artikel.php?id=<?php echo $row['id_artikel']; ?>" class="btn-action delete" onclick="return confirm('Yakin ingin menghapus artikel ini?')">Hapus</a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
