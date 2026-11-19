<?php
require_once __DIR__ . '/../config/database.php';

$event_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($event_id) {
    $stmt = $pdo->prepare("SELECT * FROM mabar_events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT * FROM mabar_events ORDER BY id ASC LIMIT 1");
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$event) {
    header('Location: mabar.php');
    exit();
}

$page_title = 'Detail Mabar: ' . htmlspecialchars($event['title']) . ' - Sportiva';
$active_page = 'mabar';
$base_url = '../';
$css_files = ['mabar_styles.css'];
$include_auth_modal = true;
require_once __DIR__ . '/../includes/header.php';

$filled = (int)$event['slots_filled'];
$total = max(1, (int)$event['total_slots']);
$remaining = max(0, $total - $filled);
$formatted_date = date('D, d M Y', strtotime($event['event_date']));
$formatted_time = date('H:i', strtotime($event['start_time'])) . ' - ' . date('H:i', strtotime($event['end_time'])) . ' WIB';
?>

<main class="mabar-detail-main">
    <section class="mabar-hero-banner">
        <div class="mabar-detail-container">
            <nav class="breadcrumb breadcrumb-inverse" aria-label="Breadcrumb">
                <a href="../index.php">Home</a>
                <span aria-hidden="true">/</span>
                <a href="mabar.php">Mabar</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php echo htmlspecialchars($event['title']); ?></span>
            </nav>

            <span class="mabar-hero-badge"><?php echo htmlspecialchars($event['sport'] . ' · ' . $event['skill_level']); ?></span>
            <h1><?php echo htmlspecialchars($event['title']); ?></h1>

            <div class="participants-info">
                <div class="participant-avatars" aria-hidden="true">
                    <img src="../assets/images/avatars/avatars1.png" alt="" width="36" height="36">
                    <img src="../assets/images/avatars/avatars2.png" alt="" width="36" height="36">
                    <img src="../assets/images/avatars/avatars3.png" alt="" width="36" height="36">
                    <img src="../assets/images/avatars/avatars4.png" alt="" width="36" height="36">
                    <img src="../assets/images/avatars/avatars5.png" alt="" width="36" height="36">
                    <div class="avatar-empty-placeholder">+<?php echo max(0, $filled - 5); ?></div>
                </div>
                <span class="join-status-pill"><?php echo $filled; ?> / <?php echo $total; ?> Peserta Bergabung (Sisa <?php echo $remaining; ?> slot)</span>
            </div>
        </div>
    </section>

    <section class="details-section-wrapper">
        <div class="mabar-detail-container">
            <div class="details-grid">
                <div class="info-column reveal-card">
                    <div class="host-banner-box">
                        <img src="../assets/images/<?php echo htmlspecialchars($event['host_logo']); ?>" alt="" width="48" height="48" class="host-logo" onerror="this.onerror=null;this.src='../assets/images/logo_bmx.png';">
                        <div>
                            <span class="host-label">Host Komunitas</span>
                            <strong><?php echo htmlspecialchars($event['host_name']); ?></strong>
                        </div>
                    </div>

                    <h2>Tentang Sesi Mabar</h2>
                    <div class="description">
                        <p>Sesi main bareng terbuka untuk cabang olahraga <strong><?php echo htmlspecialchars($event['sport']); ?></strong> dengan kategori kemampuan <strong><?php echo htmlspecialchars($event['skill_level']); ?></strong>.</p>
                        <p>Lapangan sudah dipesan di <strong><?php echo htmlspecialchars($event['venue_name']); ?></strong> pada jadwal <strong><?php echo $formatted_date; ?></strong> pukul <strong><?php echo $formatted_time; ?></strong>.</p>
                        <p>Peserta pria maupun wanita dipersilakan bergabung. Mohon hadir 15 menit sebelum sesi dimulai untuk pemanasan dan pembagian giliran bermain. Batas pembatalan keikutsertaan maksimal H-1 pukul 15:00 WIB.</p>
                        <p>Biaya patungan sudah termasuk sewa lapangan dan perlengkapan pertandingan standar (shuttlecock / bola permainan).</p>
                    </div>

                    <h2 class="section-divider">Lokasi Pertandingan</h2>
                    <div class="mabar-location-card">
                        <div class="mabar-location-header">
                            <div>
                                <strong><?php echo htmlspecialchars($event['venue_name']); ?></strong>
                                <p>Arena terverifikasi Sportiva</p>
                            </div>
                            <a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo urlencode($event['venue_name']); ?>" target="_blank" rel="noopener noreferrer" class="map-external-btn">Buka Peta</a>
                        </div>
                        <img class="map-image" src="../assets/images/map_preview.png" alt="Pratinjau Peta Lokasi <?php echo htmlspecialchars($event['venue_name']); ?>" width="720" height="320" loading="lazy">
                    </div>
                </div>

                <aside class="booking-column reveal-card" style="--stagger-index: 1;">
                    <div class="price-display">
                        <div>
                            <span class="price-caption">Biaya Patungan</span>
                            <div class="price-value-row">
                                <span class="amount">Rp45.000</span>
                                <span class="per-person">/peserta</span>
                            </div>
                        </div>
                        <span class="slot-badge-live">Sisa <?php echo $remaining; ?> Slot</span>
                    </div>

                    <button type="button" class="join-button" id="join-mabar-btn" data-title="<?php echo htmlspecialchars($event['title']); ?>">Gabung Sesi Mabar</button>

                    <div class="info-list">
                        <div class="info-list-item">
                            <p class="label">Waktu &amp; Tanggal</p>
                            <p class="value"><?php echo $formatted_date; ?> · <?php echo $formatted_time; ?></p>
                        </div>
                        <div class="info-list-item">
                            <p class="label">Arena Venue</p>
                            <p class="value"><?php echo htmlspecialchars($event['venue_name']); ?></p>
                        </div>
                        <div class="info-list-item">
                            <p class="label">Level Permainan</p>
                            <p class="value"><?php echo htmlspecialchars($event['skill_level']); ?></p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const joinBtn = document.getElementById('join-mabar-btn');
    if (joinBtn) {
        joinBtn.addEventListener('click', function() {
            const title = this.dataset.title || 'Sesi Mabar';
            const isJoined = this.classList.toggle('is-joined');
            if (isJoined) {
                this.textContent = 'Terdaftar di Sesi Ini';
                if (window.SportivaUI) {
                    window.SportivaUI.showToast(`Anda berhasil mendaftar di "${title}"`);
                }
            } else {
                this.textContent = 'Gabung Sesi Mabar';
                if (window.SportivaUI) {
                    window.SportivaUI.showToast('Pendaftaran sesi mabar dibatalkan');
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
