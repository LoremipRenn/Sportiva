<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
$page_title = 'Verifikasi Akun - Sportiva';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,700&family=Roboto+Mono:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/otp_styles.css">
</head>
<body>
  <header class="site-header">
      <div class="header-content">
          <div class="logo-area">
              <a href="../index.php" class="logo-text">
                  <span class="logo-mark" aria-hidden="true"></span>
                  Sportiva
              </a>
          </div>
      </div>
  </header>

  <main class="otp-container">
    <div class="otp-card">
      <!-- Step Indicator -->
      <div class="otp-progress" aria-hidden="true">
        <span class="otp-progress-dot active" data-step="0"></span>
        <span class="otp-progress-dot" data-step="1"></span>
        <span class="otp-progress-dot" data-step="2"></span>
      </div>

      <!-- Step 1: OTP Verification Intro -->
      <section class="otp-step active" id="step1" aria-labelledby="otp-title-1">
        <img src="../assets/images/otp.png" alt="Ilustrasi Verifikasi OTP" width="180" height="180" class="otp-image">
        <h1 id="otp-title-1">Verifikasi Keamanan OTP</h1>
        <p>Kami akan mengirimkan kode satu kali (OTP) ke akun email terdaftar Anda untuk memastikan keamanan sesi.</p>
        <button type="button" class="btn-confirm" id="confirm-step1">Kirim Kode OTP</button>
      </section>

      <!-- Step 2: Verification Code Input -->
      <section class="otp-step" id="step2" aria-labelledby="otp-title-2">
        <h1 id="otp-title-2">Masukkan Kode Verifikasi</h1>
        <p>Kode 4 digit telah dikirimkan ke alamat email akun <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Member'); ?></strong>.</p>
        <div class="otp-inputs" role="group" aria-label="4 Digit Kode OTP">
          <input type="text" inputmode="numeric" maxlength="1" value="8" aria-label="Digit 1">
          <input type="text" inputmode="numeric" maxlength="1" value="5" aria-label="Digit 2">
          <input type="text" inputmode="numeric" maxlength="1" value="2" aria-label="Digit 3">
          <input type="text" inputmode="numeric" maxlength="1" value="9" aria-label="Digit 4">
        </div>
        <button type="button" class="btn-confirm" id="confirm-step2">Verifikasi Kode</button>
      </section>

      <!-- Step 3: Success Message -->
      <section class="otp-step" id="step3" aria-labelledby="otp-title-3">
        <div class="success-icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <h1 id="otp-title-3">Autentikasi Berhasil</h1>
        <p>Selamat datang kembali di Sportiva. Akun Anda telah terverifikasi dan siap digunakan untuk booking lapangan.</p>
        <button type="button" class="btn-confirm" id="confirm-step3">Masuk ke Beranda</button>
      </section>
    </div>
  </main>

  <script src="../assets/js/otp.js"></script>
</body>
</html>
