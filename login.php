<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password_input = $_POST['password']; 

    // Cari data berdasarkan username saja
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password BCRYPT
        if (password_verify($password_input, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role']; 
            $_SESSION['email'] = $row['email'];
            
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 60px auto;">
    <div style="text-align: center; margin-bottom: 30px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
        <h2 style="border: none; padding-bottom: 0; margin-top: 10px;">Login Admin</h2>
    </div>
    
    <?php if($error): ?>
        <div class="alert error" style="background-color: #ef4444; color: white; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-submit">Masuk</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
