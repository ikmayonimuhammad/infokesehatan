<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php'; 

$id_artikel = isset($_GET['id']) ? intval($_GET['id']) : 0;

// PROSES SUBMIT KOMENTAR
if(isset($_POST['kirim_komentar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi_komentar']);
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    
    $status = isset($_SESSION['login']) ? 'sudah' : 'belum';

    $query_komen = "INSERT INTO komentar (id_artikel, parent_id, nama, email, isi_komentar, status_baca) 
                    VALUES ('$id_artikel', '$parent_id', '$nama', '$email', '$isi', '$status')";
    mysqli_query($conn, $query_komen);
    
    header("Location: artikel.php?id=$id_artikel#komentar-area");
    exit;
}

include 'includes/header.php'; 

$query = "SELECT artikel.*, kategori.nama_kategori 
          FROM artikel 
          LEFT JOIN kategori ON artikel.id_kategori = kategori.id_kategori 
          WHERE id_artikel = '$id_artikel'";
$result = mysqli_query($conn, $query);
?>

<style>
    .article-content img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 8px;
        display: block;
        margin: 10px auto;
    }
    .article-content {
        overflow-x: hidden; 
    }
    .article-text-body {
        line-height: 1.8; 
        font-size: 1.1em; 
        color: var(--text-dark);
        word-wrap: break-word; 
    }
    
    .article-main-card {
        background: var(--card-bg); 
        padding: 30px; 
        border-radius: 12px; 
        border: 1px solid var(--border-color); 
        box-shadow: var(--glow-shadow);
        transition: all 0.4s ease;
    }
</style>

<div class="article-wrapper">
    <a href="index.php" class="btn-back" style="margin-bottom: 25px;">
        &laquo; Kembali ke Beranda
    </a>

    <?php if(mysqli_num_rows($result) > 0): 
        $row = mysqli_fetch_assoc($result);
    ?>
        <article class="article-content article-main-card">
            
            <h2 style="border-bottom: none; padding-bottom: 0; margin: 0 0 10px 0; font-size: 2.2em; color: var(--text-dark);">
                <?php echo htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            
            <p style="color: var(--text-light); font-size: 0.95em; margin-bottom: 25px; border-bottom: 2px solid var(--border-color); padding-bottom: 15px;">
                <span style="color: var(--accent-primary); font-weight: bold;">📁 <?php echo htmlspecialchars($row['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?></span> &nbsp;|&nbsp; 
                ✍️ Oleh: <?php echo htmlspecialchars($row['penulis'], ENT_QUOTES, 'UTF-8'); ?> &nbsp;|&nbsp; 
                📅 <?php echo date('d M Y', strtotime($row['tanggal_publikasi'])); ?>
            </p>

            <?php if(!empty($row['gambar']) && file_exists("assets/images/".$row['gambar'])): ?>
                <img src="assets/images/<?php echo $row['gambar']; ?>" alt="<?php echo htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8'); ?>" class="article-detail-image" style="margin-bottom: 25px;">
            <?php endif; ?>

            <div class="article-text-body">
                <?php echo $row['konten']; // Konten artikel biasanya dari editor yang sudah terpercaya ?>
            </div>
            
        </article>

        <div id="komentar-area" class="comment-section" style="margin-top: 50px; border-top: 2px solid var(--border-color); padding-top: 30px;">
            <h3 style="color: var(--text-dark);">💬 Diskusi & Komentar</h3>
            
            <div style="margin-bottom: 40px;">
                <?php
                $q_utama = mysqli_query($conn, "SELECT * FROM komentar WHERE id_artikel='$id_artikel' AND parent_id=0 ORDER BY tanggal ASC");
                if(mysqli_num_rows($q_utama) > 0) {
                    while($k = mysqli_fetch_assoc($q_utama)) {
                        $id_k = $k['id_komentar'];
                ?>
                    <div class="comment-box" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 15px;">
                        <div class="comment-header" style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; margin-bottom: 10px;">
                            <span style="font-weight: bold; color: var(--text-dark);">
                                👤 <?php echo htmlspecialchars($k['nama'], ENT_QUOTES, 'UTF-8'); ?> 
                                <?php if($k['nama'] == $row['penulis']) echo "<span style='background:var(--accent-primary); color:white; padding:2px 6px; border-radius:4px; font-size:0.8em; margin-left:5px;'>Penulis</span>"; ?>
                            </span>
                            <span style="font-size: 0.85em; color: var(--text-light);"><?php echo date('d M Y H:i', strtotime($k['tanggal'])); ?></span>
                        </div>
                        <p style="margin: 0 0 10px 0; color: var(--text-dark);">
                            <?php echo nl2br(htmlspecialchars($k['isi_komentar'], ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                        <button style="background:none; border:none; color:var(--accent-secondary); font-weight:bold; cursor:pointer;" onclick="setReply(<?php echo $id_k; ?>, '<?php echo htmlspecialchars($k['nama'], ENT_QUOTES, 'UTF-8'); ?>')">↳ Balas Komentar</button>
                    </div>

                    <?php
                    $q_balasan = mysqli_query($conn, "SELECT * FROM komentar WHERE parent_id='$id_k' ORDER BY tanggal ASC");
                    while($b = mysqli_fetch_assoc($q_balasan)) {
                    ?>
                        <div class="comment-box" style="background: var(--bg-color); border-left: 4px solid var(--accent-primary); border-radius: 10px; padding: 15px 20px; margin-bottom: 15px; margin-left: 40px;">
                            <div class="comment-header" style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; margin-bottom: 10px;">
                                <span style="font-weight: bold; color: var(--text-dark);">
                                    ↳ 👤 <?php echo htmlspecialchars($b['nama'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                                <span style="font-size: 0.85em; color: var(--text-light);"><?php echo date('d M Y H:i', strtotime($b['tanggal'])); ?></span>
                            </div>
                            <p style="margin: 0; color: var(--text-dark);">
                                <?php echo nl2br(htmlspecialchars($b['isi_komentar'], ENT_QUOTES, 'UTF-8')); ?>
                            </p>
                        </div>
                    <?php } ?>
                    
                <?php 
                    } 
                } else {
                    echo "<p style='color:var(--text-light);'>Belum ada komentar.</p>";
                }
                ?>
            </div>

            <div style="background: var(--card-bg); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color);">
                <h4 style="margin-top: 0; color: var(--text-dark);" id="form-title">Tinggalkan Komentar</h4>
                <p id="komentar_label" style="color: var(--accent-secondary); font-weight: bold; margin-top:-10px;"></p>
                
                <form id="form-komentar" action="" method="POST">
                    <input type="hidden" name="parent_id" id="parent_id" value="0">
                    
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <?php if(isset($_SESSION['login'])): ?>
                            <div class="form-group" style="flex: 1; min-width: 250px;">
                                <label>Nama *</label>
                                <input type="text" name="nama" required value="<?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>" readonly style="background-color: var(--bg-color); color: var(--text-light); cursor: not-allowed; border-color: var(--border-color);">
                            </div>
                            <div class="form-group" style="flex: 1; min-width: 250px;">
                                <label>Email *</label>
                                <input type="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?>" readonly style="background-color: var(--bg-color); color: var(--text-light); cursor: not-allowed; border-color: var(--border-color);">
                            </div>
                        <?php else: ?>
                            <div class="form-group" style="flex: 1; min-width: 250px;">
                                <label>Nama *</label>
                                <input type="text" name="nama" required placeholder="Masukkan nama Anda">
                            </div>
                            <div class="form-group" style="flex: 1; min-width: 250px;">
                                <label>Email * (Privat)</label>
                                <input type="email" name="email" required placeholder="contoh@email.com">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>Isi Komentar *</label>
                        <textarea name="isi_komentar" rows="4" required placeholder="Tulis pendapat Anda..."></textarea>
                    </div>
                    <button type="submit" name="kirim_komentar" class="btn-submit" style="width: auto;">Kirim Komentar</button>
                    <button type="button" onclick="batalBalas()" id="btn-batal" style="display: none; background: #ef4444; color:white; border:none; padding:12px 20px; border-radius:30px; cursor:pointer; margin-left:10px;">Batal Balas</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p style="color: var(--text-dark); text-align: center;">Artikel tidak ditemukan.</p>
    <?php endif; ?>
</div>

<script>
    function setReply(id, nama) {
        document.getElementById('parent_id').value = id;
        document.getElementById('komentar_label').innerText = 'Membalas ke: ' + nama;
        document.getElementById('form-title').innerText = 'Tulis Balasan';
        document.getElementById('btn-batal').style.display = 'inline-block';
        document.getElementById('form-komentar').scrollIntoView({behavior: "smooth"});
    }
    function batalBalas() {
        document.getElementById('parent_id').value = 0;
        document.getElementById('komentar_label').innerText = '';
        document.getElementById('form-title').innerText = 'Tinggalkan Komentar';
        document.getElementById('btn-batal').style.display = 'none';
    }
</script>

<?php include 'includes/footer.php'; ?>
