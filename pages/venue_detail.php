<?php
require_once __DIR__ . '/../config/database.php';

$venue_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$venue_id) {
    header('Location: sewa_lapangan.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt->execute([$venue_id]);
$venue = $stmt->fetch();
if (!$venue) {
    header('Location: sewa_lapangan.php');
    exit();
}

$stmt_schedules = $pdo->prepare("SELECT * FROM schedules WHERE venue_id = ? ORDER BY start_time ASC");
$stmt_schedules->execute([$venue_id]);
$schedules = $stmt_schedules->fetchAll();

$stmt_sports = $pdo->prepare("
    SELECT s.name, s.icon_svg 
    FROM venue_sports vs
    JOIN sports s ON vs.sport_id = s.id
    WHERE vs.venue_id = ?
");
$stmt_sports->execute([$venue_id]);
$sports = $stmt_sports->fetchAll();

$facilities = [
    'Cafe & Resto' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>',
    'Jual Makanan Ringan' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 21z" /></svg>',
    'Jual Minuman' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
    'Musholla' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>',
    'Parkir Mobil' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 013.375-3.375h9.75a3.375 3.375 0 013.375 3.375v1.875m-17.25 4.5h17.25" /></svg>',
    'Parkir Motor' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true"><path d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.502l1.54-2.665a.375.375 0 00-.086-.512L7.5 9.75M15.91 11.672a.375.375 0 010 .656" /></svg>'
];
$venue_rules = [
    'Pelanggan wajib hadir minimal 15 menit sebelum jadwal dimulai.',
    'Dilarang membawa air mineral kemasan gelas sekali pakai ke area lapangan.',
    'Dilarang bersandar pada jaring pembatas lapangan.',
    'Dilarang membawa senjata tajam dan minuman beralkohol.',
    'Wajib menggunakan sepatu olahraga bersol karet non-marking.'
];

$page_title = htmlspecialchars($venue['name']) . ' - Sportiva';
$active_page = 'sewa';
$base_url = '../';
$css_files = ['venue_detail_styles.css'];
$include_auth_modal = true;
require_once __DIR__ . '/../includes/header.php';
?>

<main class="main-content-wrapper">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="../index.php">Home</a>
        <span aria-hidden="true">/</span>
        <a href="sewa_lapangan.php">Sewa Lapangan</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page"><?php echo htmlspecialchars($venue['name']); ?></span>
    </nav> 

    <div class="venue-gallery reveal-card">
        <img src="../assets/images/<?php echo htmlspecialchars($venue['main_image']); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>" width="1200" height="460" fetchpriority="high" onerror="this.onerror=null;this.src='../assets/images/metro_atom.png';">
        <button type="button" class="gallery-btn" id="open-gallery-modal">Perbesar Foto Arena</button>
    </div>

    <div class="venue-content-grid">
        <div class="venue-details-main">
            <div class="venue-sports-tags">
                <?php foreach ($sports as $sport): ?>
                    <div class="sport-tag"><?php echo $sport['icon_svg']; ?> <span><?php echo htmlspecialchars($sport['name']); ?></span></div>
                <?php endforeach; ?>
            </div>

            <h1><?php echo htmlspecialchars($venue['name']); ?></h1>

            <div class="venue-meta">
                <span class="rating">
                    <svg class="icon-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c.321-.662 1.215-.662 1.536 0l1.681 3.468 3.82.556c.734.107 1.028.997.494 1.503l-2.764 2.694.652 3.803c.124.722-.638 1.278-1.284.944L10 13.6l-3.419 1.796c-.646.334-1.408-.222-1.284-.944l.652-3.803L2.27 8.411c-.534-.506-.24-1.396.494-1.503l3.82-.556 1.681-3.468z" clip-rule="evenodd" /></svg>
                    <strong><?php echo htmlspecialchars($venue['rating']); ?></strong>
                </span>
                <span aria-hidden="true">·</span>
                <span><?php echo htmlspecialchars($venue['city']); ?></span>
                <span aria-hidden="true">·</span>
                <span><?php echo htmlspecialchars($venue['address']); ?></span>
            </div>

            <section class="venue-section">
                <h2>Deskripsi Arena</h2>
                <p><?php echo nl2br(htmlspecialchars($venue['description'])); ?></p>
            </section>
            
            <section class="venue-section">
                <h2>Aturan Venue</h2>
                <ol class="rules-list">
                    <?php foreach (array_slice($venue_rules, 0, 5) as $rule): ?>
                        <li><?php echo htmlspecialchars($rule); ?></li>
                    <?php endforeach; ?>
                </ol>
                <button type="button" class="read-more-link" id="open-rules-modal">Baca Kebijakan Lengkap</button>
            </section>
            
            <section class="venue-section">
                <h2>Lokasi Venue</h2>
                <div class="location-box">
                    <div class="location-text">
                        <strong><?php echo htmlspecialchars($venue['city']); ?></strong>
                        <span><?php echo htmlspecialchars($venue['address']); ?></span>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo urlencode($venue['name'] . ' ' . $venue['address']); ?>" target="_blank" rel="noopener noreferrer" class="map-link">Buka di Google Maps</a>
                </div>
            </section>

            <section class="venue-section">
                <h2>Fasilitas Tersedia</h2>
                <ul class="facility-list">
                    <?php foreach ($facilities as $name => $icon): ?>
                        <li><?php echo $icon; ?> <span><?php echo htmlspecialchars($name); ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-link-style" id="open-facilities-modal">Detail Fasilitas Arena</button>
            </section>
        </div>

        <aside class="venue-sidebar">
            <div class="pricing-card">
                <div class="price-label">Harga Sewa Mulai Dari</div>
                <div class="price-amount">Rp<?php echo number_format($venue['min_price'], 0, ',', '.'); ?><span class="price-session"> /sesi</span></div>
                <p class="availability-summary"><?php echo count($schedules); ?> slot jadwal terdaftar di sistem</p>
                <a href="ketersediaan_venue.php?id=<?php echo $venue['id']; ?>" class="btn-primary" id="check-availability-btn">Cek Ketersediaan Jadwal</a>
            </div>
            <div class="benefits-card">
                <h3>Keuntungan Booking via Sportiva</h3>
                <ul>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Konfirmasi slot jadwal instan tanpa antre</span>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Reschedule jadwal fleksibel hingga H-3</span>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Harga transparan sesuai jam operasional venue</span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
    
    <section class="venue-section" id="membership-section">
        <div class="section-header-flex">
            <div>
                <h2>Paket Langganan Member</h2>
                <p>Hemat biaya sewa rutin untuk latihan tim maupun komunitas.</p>
            </div>
            <div class="membership-controls">
                <button type="button" class="slider-nav-btn" id="membership-prev" aria-label="Geser paket ke kiri">‹</button>
                <button type="button" class="slider-nav-btn" id="membership-next" aria-label="Geser paket ke kanan">›</button>
            </div>
        </div>
        <div class="membership-slider-container">
            <div class="membership-slider" id="membership-slider">
                <article class="membership-card">
                    <h3>Membership 2 Jam / Minggu</h3>
                    <ul>
                        <li>Alokasi 2 slot tetap setiap minggu</li>
                        <li>Durasi aktif 4 minggu penuh</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Hemat 10%</span>
                        <button type="button" class="btn-buy" data-package="Membership 2 Jam / Minggu">Pilih Paket</button>
                    </div>
                </article>
                <article class="membership-card">
                    <h3>Membership 1 Jam / Minggu</h3>
                    <ul>
                        <li>Alokasi 1 slot tetap setiap minggu</li>
                        <li>Prioritas pemesanan jadwal malam</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Hemat 10%</span>
                        <button type="button" class="btn-buy" data-package="Membership 1 Jam / Minggu">Pilih Paket</button>
                    </div>
                </article>
                <article class="membership-card">
                    <h3>Membership Klub 3 Jam / Minggu</h3>
                    <ul>
                        <li>Alokasi 3 slot per minggu untuk klub</li>
                        <li>Bebas pilih court utama</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Hemat 15%</span>
                        <button type="button" class="btn-buy" data-package="Membership Klub 3 Jam / Minggu">Pilih Paket</button>
                    </div>
                </article>
                <article class="membership-card">
                    <h3>Paket Pelajar &amp; Mahasiswa</h3>
                    <ul>
                        <li>Berlaku Senin–Jumat pukul 08:00–16:00</li>
                        <li>Wajib menunjukkan kartu pelajar aktif</li>
                    </ul>
                    <div class="card-footer">
                        <span class="discount-tag">Harga Khusus</span>
                        <button type="button" class="btn-buy" data-package="Paket Pelajar & Mahasiswa">Pilih Paket</button>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<!-- Modals -->
<div class="detail-modal" id="gallery-modal" role="dialog" aria-modal="true" aria-labelledby="gallery-modal-title">
    <div class="detail-modal-content">
        <button type="button" class="detail-modal-close" id="close-gallery-modal" aria-label="Tutup galeri">×</button>
        <h2 id="gallery-modal-title"><?php echo htmlspecialchars($venue['name']); ?></h2>
        <img src="../assets/images/<?php echo htmlspecialchars($venue['main_image']); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>" class="modal-preview-img" onerror="this.onerror=null;this.src='../assets/images/metro_atom.png';">
    </div>
</div>

<div class="detail-modal" id="rules-modal" role="dialog" aria-modal="true" aria-labelledby="rules-modal-title">
    <div class="detail-modal-content">
        <button type="button" class="detail-modal-close" id="close-rules-modal" aria-label="Tutup aturan">×</button>
        <h2 id="rules-modal-title">Aturan Venue &amp; Kebijakan</h2>
        <h3>Tata Tertib Lapangan</h3>
        <ol class="rules-list">
            <?php foreach ($venue_rules as $rule): ?>
                <li><?php echo htmlspecialchars($rule); ?></li>
            <?php endforeach; ?>
        </ol>
        <h3>Kebijakan Reschedule</h3>
        <p>Pengajuan perubahan jadwal dapat dilakukan maksimal 3 hari sebelum waktu sewa dan berlaku untuk 1 kali perubahan di venue yang sama.</p>
    </div>
</div>

<div class="detail-modal" id="facilities-modal" role="dialog" aria-modal="true" aria-labelledby="facilities-modal-title">
    <div class="detail-modal-content">
        <button type="button" class="detail-modal-close" id="close-facilities-modal" aria-label="Tutup fasilitas">×</button>
        <h2 id="facilities-modal-title">Daftar Lengkap Fasilitas</h2>
        <ul class="facility-list modal-facility-grid">
             <?php foreach ($facilities as $name => $icon): ?>
                <li><?php echo $icon; ?> <span><?php echo htmlspecialchars($name); ?></span></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function setupModal(openBtnId, closeBtnId, modalId) {
        const openBtn = document.getElementById(openBtnId);
        const closeBtn = document.getElementById(closeBtnId);
        const modal = document.getElementById(modalId);
        if (!openBtn || !closeBtn || !modal) return;
        
        openBtn.addEventListener('click', () => modal.classList.add('is-visible'));
        closeBtn.addEventListener('click', () => modal.classList.remove('is-visible'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('is-visible');
        });
    }
    setupModal('open-gallery-modal', 'close-gallery-modal', 'gallery-modal');
    setupModal('open-rules-modal', 'close-rules-modal', 'rules-modal');
    setupModal('open-facilities-modal', 'close-facilities-modal', 'facilities-modal');

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.detail-modal.is-visible').forEach(m => m.classList.remove('is-visible'));
        }
    });

    const slider = document.getElementById('membership-slider');
    const prevBtn = document.getElementById('membership-prev');
    const nextBtn = document.getElementById('membership-next');
    if (slider && prevBtn && nextBtn) {
        const scrollAmount = 320;
        prevBtn.addEventListener('click', () => slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => slider.scrollBy({ left: scrollAmount, behavior: 'smooth' }));
    }

    document.querySelectorAll('.btn-buy').forEach(btn => {
        btn.addEventListener('click', () => {
            const pkg = btn.dataset.package || 'Membership';
            if (window.SportivaUI) {
                window.SportivaUI.showToast(`Paket "${pkg}" dipilih. Silakan pilih jadwal bermain.`);
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
