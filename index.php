<?php
$page_title = 'Sportiva - Booking Lapangan & Komunitas Mabar';
$active_page = 'home';
$base_url = '';
$css_files = ['home_styles.css'];
$include_auth_modal = true;

require_once 'includes/header.php';
?>

<main class="hero-section">
    <!-- Hero Slides in HTML for instant LCP (R-40) -->
    <div class="slider-wrapper" aria-hidden="true">
        <div class="hero-slide is-active">
            <img src="assets/images/slider/Football_Slider.png" alt="Lapangan Sepak Bola dan Futsal" width="1920" height="1080" fetchpriority="high">
        </div>
        <div class="hero-slide">
            <img src="assets/images/slider/Badminton_Slider.png" alt="Lapangan Badminton Indoor" width="1920" height="1080">
        </div>
        <div class="hero-slide">
            <img src="assets/images/slider/Basketball_Slider.png" alt="Arena Basket Indoor" width="1920" height="1080">
        </div>
        <div class="hero-slide">
            <img src="assets/images/slider/TableTennis_Slider.png" alt="Meja Tenis Meja Kompetisi" width="1920" height="1080">
        </div>
        <div class="hero-slide">
            <img src="assets/images/slider/Volleyball_Slider.png" alt="Lapangan Voli Indoor" width="1920" height="1080">
        </div>
    </div>

    <div class="hero-backdrop-gradient" aria-hidden="true"></div>

    <button type="button" class="slider-arrow prev-arrow" aria-label="Slide olahraga sebelumnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>

    <div class="hero-center-content">
        <div class="info-box">
            <div class="info-box-progress">
                <div class="info-box-progress-bar" id="hero-progress-bar"></div>
            </div>
            <span class="info-sport-tag" id="hero-sport-tag">Sepak Bola &amp; Futsal</span>
            <h1 id="hero-sport-title">Football</h1>
            <p id="hero-sport-desc">Booking lapangan rumput sintetis maupun vinyl indoor dengan jadwal real-time untuk latihan tim dan turnamen.</p>
            <div class="hero-cta-group">
                <a href="pages/sewa_lapangan.php" class="hero-btn-primary">Cari Jadwal Lapangan</a>
                <a href="pages/mabar.php" class="hero-btn-secondary">Gabung Komunitas Mabar</a>
            </div>
        </div>

        <div class="slider-pagination" role="tablist" aria-label="Pilih cabang olahraga">
            <button type="button" class="dot active" role="tab" aria-selected="true" aria-label="Football"></button>
            <button type="button" class="dot" role="tab" aria-selected="false" aria-label="Badminton"></button>
            <button type="button" class="dot" role="tab" aria-selected="false" aria-label="Basketball"></button>
            <button type="button" class="dot" role="tab" aria-selected="false" aria-label="Table Tennis"></button>
            <button type="button" class="dot" role="tab" aria-selected="false" aria-label="Volleyball"></button>
        </div>
    </div>

    <button type="button" class="slider-arrow next-arrow" aria-label="Slide olahraga berikutnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>
</main>

<?php require_once 'includes/footer.php'; ?>
