<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php'; 
include 'includes/header.php'; 

// Ambil data HTML dari database
$query = mysqli_query($conn, "SELECT * FROM tentang_kami WHERE id = 1");
$data = mysqli_fetch_assoc($query);
?>

<div class="container">
    <div class="about-content" style="color: var(--text-dark); line-height: 1.8; font-size: 1.1em;">
        <?php 
        // Jika data kosong, tampilkan pesan default
        if($data && !empty($data['konten_html'])) {
            // Echo langsung akan merender tag HTML menjadi tampilan visual
            echo $data['konten_html']; 
        } else {
            echo "<h2>Tentang Kami</h2><p>Informasi profil sedang diperbarui.</p>";
        }
        ?>
    </div>
</div>

<style>
    .about-content h2, .about-content h3 {
        color: var(--text-dark);
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 10px;
        margin-top: 30px;
    }
    .about-content a {
        color: var(--accent-secondary);
        font-weight: bold;
        text-decoration: none;
    }
    .about-content a:hover {
        text-decoration: underline;
        color: var(--accent-primary);
    }
    .about-content ul, .about-content ol {
        margin-left: 20px;
        margin-bottom: 20px;
    }
</style>

<?php include 'includes/footer.php'; ?>
