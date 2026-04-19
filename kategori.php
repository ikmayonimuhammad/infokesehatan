<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';
include 'includes/header.php'; 
?>

<div class="container">
    <h2 style="color: var(--text-dark);">Kategori Artikel Kesehatan</h2>
    <p style="color: var(--text-light);">Pilih topik di bawah ini untuk menemukan artikel yang paling sesuai dengan kebutuhan Anda:</p>

    <div class="admin-grid" style="margin-top: 30px;">
        <?php
        // Mengambil semua daftar kategori
        $kat_query = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
        
        while($kat = mysqli_fetch_assoc($kat_query)) {
            $id_kat = $kat['id_kategori'];
            
            // Menghitung jumlah artikel di setiap kategori ini
            // Kita gunakan Prepared Statement bawaan untuk mencegah error
            $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel WHERE id_kategori = '$id_kat'");
            $count_data = mysqli_fetch_assoc($count_query);
            $total_artikel = $count_data['total'];
        ?>
        
        <a href="index.php?kat_id=<?php echo $id_kat; ?>" class="admin-card" style="padding: 25px 20px; text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent-primary); margin-bottom: 15px;">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            
            <h3 style="margin: 0; color: var(--text-dark); font-size: 1.3em;">
                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
            </h3>
            
            <p style="margin: 5px 0 0 0; color: var(--text-light); font-weight: bold;">
                <?php echo $total_artikel; ?> Artikel
            </p>
        </a>
        
        <?php } ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
