<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$cart_count = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$base_url = $base_url ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title ?? 'Sportiva - Booking Lapangan & Komunitas Mabar'); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,700&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/global.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/sidebar_styles.css">
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/auth_styles.css">
  <?php if (isset($css_files) && is_array($css_files)): ?>
      <?php foreach (array_unique($css_files) as $css): ?>
          <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/<?php echo htmlspecialchars($css); ?>">
      <?php endforeach; ?>
  <?php endif; ?>
</head>
<body data-base-url="<?php echo htmlspecialchars($base_url); ?>">
<div class="page-container">
  <header class="site-header">
      <div class="header-content">
          <div class="logo-area">
            <a href="<?php echo $base_url; ?>index.php" class="logo-text" aria-label="Sportiva Beranda">
              <span class="logo-mark" aria-hidden="true"></span>
              Sportiva
            </a>
          </div>

          <div class="header-right">
              <nav class="main-nav" id="main-nav" aria-label="Navigasi Utama">
                  <ul>
                      <li><a href="<?php echo $base_url; ?>index.php" class="<?php echo (($active_page ?? '') === 'home') ? 'active' : ''; ?>">Home</a></li>
                      <li><a href="<?php echo $base_url; ?>pages/sewa_lapangan.php" class="<?php echo (($active_page ?? '') === 'sewa') ? 'active' : ''; ?>">Sewa Lapangan</a></li>
                      <li><a href="<?php echo $base_url; ?>pages/mabar.php" class="<?php echo (($active_page ?? '') === 'mabar') ? 'active' : ''; ?>">Mabar</a></li>
                  </ul>
              </nav>

              <div class="header-actions">
                  <button type="button" class="cart-link" id="header-cart-trigger" aria-label="Buka keranjang jadwal">
                      <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <circle cx="9" cy="21" r="1"></circle>
                          <circle cx="20" cy="21" r="1"></circle>
                          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                      </svg>
                      <span class="cart-badge <?php echo ($cart_count === 0) ? 'is-empty' : ''; ?>" id="header-cart-badge"><?php echo $cart_count; ?></span>
                  </button>
                  
                  <?php if ($is_logged_in): ?>
                      <div class="user-menu" id="user-menu">
                          <button type="button" class="user-menu-trigger" id="user-menu-trigger" aria-expanded="false" aria-haspopup="true">
                              <img src="<?php echo $base_url; ?>assets/images/user.png" alt="" width="32" height="32" class="user-avatar">
                              <span><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                              <svg class="user-menu-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                              </svg>
                          </button>
                          <div class="user-dropdown" id="user-dropdown" role="menu">
                              <div class="user-dropdown-header">
                                  <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                                  <span>Member Sportiva</span>
                              </div>
                              <a href="<?php echo $base_url; ?>pages/sewa_lapangan.php" role="menuitem">Cari Jadwal Lapangan</a>
                              <a href="<?php echo $base_url; ?>pages/mabar.php" role="menuitem">Event Mabar</a>
                              <a href="<?php echo $base_url; ?>actions/logout.php" class="logout-btn" role="menuitem">Keluar Akun</a>
                          </div>
                      </div>
                  <?php else: ?>
                      <div class="auth-buttons" id="auth-buttons-logged-out">
                          <button type="button" class="btn-login" id="login-btn-header">Masuk</button>
                          <button type="button" class="btn-register" id="register-btn-header">Daftar</button>
                      </div>
                  <?php endif; ?>

                  <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="main-nav">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                          <line x1="3" y1="6" x2="21" y2="6"></line>
                          <line x1="3" y1="12" x2="21" y2="12"></line>
                          <line x1="3" y1="18" x2="21" y2="18"></line>
                      </svg>
                  </button>
              </div>
          </div>
      </div>
  </header>
