<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/koneksi.php';

// Hanya admin utama yang bisa tambah user
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }

$pesan = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    // Hashing BCRYPT
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Cek Username/Email ganda
    $stmt_cek = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username=? OR email=?");
    mysqli_stmt_bind_param($stmt_cek, "ss", $username, $email);
    mysqli_stmt_execute($stmt_cek);
    mysqli_stmt_store_result($stmt_cek);
    
    if(mysqli_stmt_num_rows($stmt_cek) > 0) {
        $pesan = "<div class='alert error' style='background-color: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Gagal: Username atau Email sudah digunakan!</div>";
    } else {
        // PREPARED STATEMENT INSERT USER
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $username, $password, $nama, $email, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan = "<div class='alert success' style='background-color: var(--accent-primary); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>User baru berhasil didaftarkan!</div>";
        } else {
            $pesan = "<div class='alert error' style='background-color: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>Error sistem.</div>";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($stmt_cek);
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 650px;">
    <a href="admin_dashboard.php" class="btn-back" style="margin-bottom: 20px;">&laquo; Kembali ke Dashboard</a>
    
    <h2 style="color: var(--text-dark);">Tambah Pengguna Baru</h2>
    <?php echo $pesan; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" required>
        </div>
        <div class="form-group">
            <label>Email Pengguna</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Username (Untuk Login)</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password Akun</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Hak Akses (Role)</label>
            <select name="role" required>
                <option value="admin">Administrator</option>
                <option value="penulis">Penulis</option>
            </select>
        </div>
        <button type="submit" class="btn-submit">Daftarkan User</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
