<?php
// Pastikan session sudah dimulai sebelum ada output HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>infokes</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script>
        (function() {
            let savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="header-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                    <path d="M19 10.5h-4.5V6a1.5 1.5 0 0 0-1.5-1.5h-2A1.5 1.5 0 0 0 9.5 6v4.5H5a1.5 1.5 0 0 0-1.5 1.5v2A1.5 1.5 0 0 0 5 15.5h4.5V20a1.5 1.5 0 0 0 1.5 1.5h2a1.5 1.5 0 0 0 1.5-1.5v-4.5H19a1.5 1.5 0 0 0 1.5-1.5v-2a1.5 1.5 0 0 0-1.5-1.5z"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>Kanal Informasi Kesehatan</h1>
                <p>Solusi Alami & Medis untuk Hidup Lebih Sehat</p>
            </div>
        </div>
    </header>
    
    <nav>
        <div class="nav-container">
            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="kategori.php">Kategori</a>
                <?php if(isset($_SESSION['login'])): ?>
                    <a href="admin_dashboard.php" class="nav-dashboard-link">Panel Admin</a>
                <?php endif; ?>
                <a href="tentang_kami.php">Tentang Kami</a>
                <a href="kontak.php">Kontak</a>
            </div>
            
            <div class="nav-auth" style="display: flex; gap: 15px; align-items: center;">
                
                <button id="theme-toggle" class="btn-theme-toggle" aria-label="Ganti Mode" title="Ganti Mode Terang/Gelap">
                    <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                    <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </button>

                <?php if(isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <a href="logout.php" class="btn-login" style="background-color: #ef4444; color: white !important; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        Login Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('theme-toggle');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    // Cek tema apa yang sedang aktif
                    let currentTheme = document.documentElement.getAttribute("data-theme");
                    let targetTheme = (currentTheme === "light") ? "dark" : "light";

                    // Ganti di HTML
                    document.documentElement.setAttribute('data-theme', targetTheme);
                    
                    // Simpan di memori browser
                    localStorage.setItem('theme', targetTheme);
                });
            }
        });
    </script>

    <div class="container">
