<?php $base_url = $base_url ?? ''; ?>
  <footer class="site-footer">
      <div class="footer-inner">
          <div class="footer-brand">
              <a href="<?php echo $base_url; ?>index.php" class="logo-text">
                  <span class="logo-mark" aria-hidden="true"></span>
                  Sportiva
              </a>
              <p>Platform booking lapangan olahraga dan komunitas mabar di berbagai kota Indonesia.</p>
          </div>
          <nav class="footer-links" aria-label="Navigasi Footer">
              <a href="<?php echo $base_url; ?>index.php">Home</a>
              <a href="<?php echo $base_url; ?>pages/sewa_lapangan.php">Sewa Lapangan</a>
              <a href="<?php echo $base_url; ?>pages/mabar.php">Komunitas Mabar</a>
          </nav>
          <div class="footer-copy">
              <span>© <?php echo date('Y'); ?> Sportiva Indonesia. Hak cipta dilindungi.</span>
              <span>Booking Cepat · Jadwal Real-Time</span>
          </div>
      </div>
  </footer>
</div> <!-- .page-container -->

<div class="sidebar-overlay" id="sidebar-overlay"></div>
<aside class="cart-sidebar" id="global-cart-sidebar" aria-label="Keranjang Jadwal Dipilih">
  <div class="cart-header">
      <div class="cart-header-title">
          <h2>Jadwal Dipilih</h2>
          <span class="cart-count-pill" id="cart-drawer-count">0</span>
      </div>
      <button type="button" class="close-btn" id="cart-close-btn" aria-label="Tutup keranjang">×</button>
  </div>
  <div class="cart-body" id="global-cart-body">
      <div class="cart-empty is-visible" id="global-cart-empty">
          <div class="cart-empty-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
          </div>
          <h3>Belum Ada Jadwal</h3>
          <p>Pilih venue dan jam bermain untuk mulai memesan lapangan.</p>
          <a href="<?php echo $base_url; ?>pages/sewa_lapangan.php" class="cart-empty-cta">Cari Lapangan</a>
      </div>
      <div id="global-cart-items"></div>
  </div>
  <div class="cart-footer" id="global-cart-footer">
      <div class="cart-total">
          <span>Total Estimasi</span>
          <strong id="global-cart-total">Rp0</strong>
      </div>
      <button type="button" class="btn-checkout" id="global-checkout-btn" disabled>Lanjut Pembayaran</button>
  </div>
</aside>

<div class="toast-container" id="toast-container" aria-live="polite"></div>

<?php if (!isset($_SESSION['user_id'])): ?>
<div class="auth-modal-overlay" id="auth-modal-container" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
    <div class="auth-modal">
        <button type="button" class="close-modal-btn" id="close-modal-btn" aria-label="Tutup dialog">×</button>
        <div class="auth-forms-viewport">
            <!-- Form Login -->
            <div class="form-container" id="login-form">
                <span class="auth-modal-badge">Sportiva Account</span>
                <h3 id="auth-modal-title">Masuk ke Sportiva</h3>
                <p>Belum punya akun? <button type="button" class="switch-link" id="switch-to-register">Daftar sekarang</button></p>
                <form action="<?php echo $base_url; ?>actions/auth.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label for="login-email">Alamat Email</label>
                        <input type="email" id="login-email" name="email" placeholder="nama@email.com" required autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn">Masuk</button>
                </form>
            </div>
            <!-- Form Register -->
            <div class="form-container hidden from-right" id="register-form">
                <span class="auth-modal-badge">Member Baru</span>
                <h3>Buat Akun Sportiva</h3>
                <p>Sudah punya akun? <button type="button" class="switch-link" id="switch-to-login">Masuk di sini</button></p>
                <form action="<?php echo $base_url; ?>actions/auth.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group">
                        <label for="register-name">Nama Lengkap</label>
                        <input type="text" id="register-name" name="name" placeholder="Nama lengkap Anda" required autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label for="register-email">Alamat Email</label>
                        <input type="email" id="register-email" name="email" placeholder="nama@email.com" required autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" placeholder="Minimal 6 karakter" required autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn">Daftar Akun</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="<?php echo $base_url; ?>assets/js/main.js"></script>
<?php if (isset($js_files) && is_array($js_files)): ?>
    <?php foreach (array_unique($js_files) as $js): ?>
        <script src="<?php echo $base_url; ?>assets/js/<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
