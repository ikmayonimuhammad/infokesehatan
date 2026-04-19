<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';
include 'includes/header.php'; 

// --- 1. KONFIGURASI PAGINASI ---
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// --- 2. LOGIKA FILTER (PENCARIAN & KATEGORI) ---
$where_clauses = [];
$url_params = [];

// Cek jika ada pencarian (q)
if(isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $keyword = mysqli_real_escape_string($conn, $_GET['q']);
    $where_clauses[] = "artikel.judul LIKE '%$keyword%'";
    $url_params[] = "q=" . urlencode($keyword);
}

// Cek jika ada filter kategori (kat_id)
if(isset($_GET['kat_id'])) {
    $kat_id = intval($_GET['kat_id']);
    $where_clauses[] = "artikel.id_kategori = $kat_id";
    $url_params[] = "kat_id=$kat_id";
}

// Gabungkan semua clause WHERE
$where_sql = "";
if(count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Gabungkan parameter untuk URL Paginasi
$url_tambahan = "";
if(count($url_params) > 0) {
    $url_tambahan = "&" . implode("&", $url_params);
}

// --- 3. HITUNG TOTAL DATA & HALAMAN ---
$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM artikel $where_sql");
$total_data = mysqli_fetch_assoc($total_res);
$total_pages = ceil($total_data['total'] / $limit);

// --- 4. AMBIL DATA ARTIKEL ---
$query = "SELECT artikel.*, kategori.nama_kategori 
          FROM artikel 
          LEFT JOIN kategori ON artikel.id_kategori = kategori.id_kategori 
          $where_sql
          ORDER BY tanggal_publikasi DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<div class="main-content container">
    
    <div class="search-container" style="margin-bottom: 40px; background: var(--card-bg); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
        <form action="index.php" method="GET" style="display: flex; gap: 10px;">
            <?php if(isset($_GET['kat_id'])): ?>
                <input type="hidden" name="kat_id" value="<?php echo $_GET['kat_id']; ?>">
            <?php endif; ?>
            
            <div style="flex: 1; position: relative;">
                <input type="text" name="q" placeholder="Cari judul artikel kesehatan..." 
                       value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                       style="width: 100%; padding: 12px 15px; border-radius: 30px; border: 2px solid var(--border-color); background: var(--bg-color); color: var(--text-dark);">
            </div>
            <button type="submit" class="btn-submit" style="width: auto; padding: 0 25px; border-radius: 30px;">
                Cari
            </button>
            <?php if(isset($_GET['q']) || isset($_GET['kat_id'])): ?>
                <a href="index.php" class="btn-back" style="margin-bottom: 0; display: flex; align-items: center; justify-content: center; background: #ef4444; color: white; border: none; padding: 0 15px;"> Reset </a>
            <?php endif; ?>
        </form>
    </div>

    <h2 style="color: var(--text-dark); margin-bottom: 30px;">
        <?php 
            if(isset($_GET['q']) && !empty($_GET['q'])) {
                echo "Hasil Pencarian: \"" . htmlspecialchars($_GET['q']) . "\"";
            } elseif(isset($_GET['kat_id'])) {
                echo "Arsip Kategori";
            } else {
                echo "Artikel Kesehatan Terbaru";
            }
        ?>
    </h2>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="article-card" style="background: var(--card-bg); transition: all 0.3s ease;">
                <?php if(!empty($row['gambar']) && file_exists("assets/images/".$row['gambar'])): ?>
                    <img src="assets/images/<?php echo $row['gambar']; ?>" class="article-image" alt="Image">
                <?php endif; ?>
                
                <h3 style="margin-top: 10px; color: var(--text-dark);">
                    <?php echo htmlspecialchars($row['judul']); ?>
                </h3>
                
                <p style="font-size: 0.85em; color: var(--text-light); margin-bottom: 15px;">
                    📁 <?php echo htmlspecialchars($row['nama_kategori']); ?> | 
                    📅 <?php echo date('d M Y H:i', strtotime($row['tanggal_publikasi'])); ?> WIB
                </p>
                
                <div style="color: var(--text-light); margin-bottom: 20px; line-height: 1.6;">
                    <?php 
                        $content = strip_tags($row['konten']);
                        echo substr($content, 0, 150) . "..."; 
                    ?>
                </div>
                
                <a href="artikel.php?id=<?php echo $row['id_artikel']; ?>" class="read-more">Baca Selengkapnya &raquo;</a>
            </div>
        <?php endwhile; ?>

        <?php if($total_pages > 1): ?>
        <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px; flex-wrap: wrap;">
            
            <?php
                $max_links = 5;
                $start_page = max(1, $page - floor($max_links / 2));
                $end_page = $start_page + $max_links - 1;

                if ($end_page > $total_pages) {
                    $end_page = $total_pages;
                    $start_page = max(1, $end_page - $max_links + 1);
                }
            ?>

            <?php if($page > 1): ?>
                <a href="index.php?page=<?php echo $page - 1; ?><?php echo $url_tambahan; ?>" 
                   style="padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; background: var(--card-bg); color: var(--text-dark); border: 1px solid var(--border-color);">&laquo; Prev</a>
            <?php endif; ?>

            <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="index.php?page=<?php echo $i; ?><?php echo $url_tambahan; ?>" 
                   style="padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; 
                          background: <?php echo ($page == $i) ? 'var(--accent-primary)' : 'var(--card-bg)'; ?>;
                          color: <?php echo ($page == $i) ? 'var(--btn-text)' : 'var(--text-dark)'; ?>;
                          border: 1px solid var(--border-color);">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if($page < $total_pages): ?>
                <a href="index.php?page=<?php echo $page + 1; ?><?php echo $url_tambahan; ?>" 
                   style="padding: 10px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; background: var(--card-bg); color: var(--text-dark); border: 1px solid var(--border-color);">Next &raquo;</a>
            <?php endif; ?>

        </div>
        <?php endif; ?>

    <?php else: ?>
        <div style="text-align: center; padding: 40px 0;">
            <p style="color: var(--text-light); font-size: 1.2em;">Maaf, tidak ada artikel yang sesuai dengan pencarian Anda.</p>
            <a href="index.php" class="btn-back" style="display: inline-block; margin-top: 15px;">Tampilkan Semua Artikel</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
