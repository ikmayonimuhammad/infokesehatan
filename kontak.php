<?php include 'includes/header.php'; ?>

<h2>Hubungi Kami</h2>
<p>Punya pertanyaan seputar kesehatan atau ingin memberikan saran untuk website kami? Silakan isi form di bawah ini.</p>

<form action="#" method="POST" style="margin-top: 20px;">
    <div class="form-group">
        <label for="nama">Nama Lengkap:</label>
        <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
    </div>
    
    <div class="form-group">
        <label for="email">Alamat Email:</label>
        <input type="email" id="email" name="email" placeholder="contoh@email.com" required>
    </div>
    
    <div class="form-group">
        <label for="pesan">Pesan / Pertanyaan:</label>
        <textarea id="pesan" name="pesan" rows="5" placeholder="Tulis pesan Anda di sini..." required></textarea>
    </div>
    
    <button type="submit" class="btn-submit">Kirim Pesan</button>
</form>

<?php include 'includes/footer.php'; ?>
