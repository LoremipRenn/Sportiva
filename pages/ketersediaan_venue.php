<?php
require_once __DIR__ . '/../config/database.php';

$venue_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$venue_id) {
    header('Location: sewa_lapangan.php');
    exit();
}

$stmt_venue = $pdo->prepare("SELECT * FROM venues WHERE id = ?");
$stmt_venue->execute([$venue_id]);
$venue = $stmt_venue->fetch(PDO::FETCH_ASSOC);
if (!$venue) {
    header('Location: sewa_lapangan.php');
    exit();
}

$stmt_sports = $pdo->prepare("SELECT s.id, s.name, s.icon_svg FROM venue_sports vs JOIN sports s ON vs.sport_id = s.id WHERE vs.venue_id = ?");
$stmt_sports->execute([$venue_id]);
$sports_at_venue = $stmt_sports->fetchAll(PDO::FETCH_ASSOC);

$courts = [];
foreach ($sports_at_venue as $sport) {
    $stmt_schedules = $pdo->prepare("SELECT * FROM schedules WHERE venue_id = ? AND sport_id = ? ORDER BY start_time ASC");
    $stmt_schedules->execute([$venue_id, $sport['id']]);
    $schedules = $stmt_schedules->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($schedules)) {
        $courts[$sport['id']] = [
            'name' => "Lapangan " . $sport['name'],
            'sport_name' => $sport['name'],
            'description' => 'Permukaan lantai standar turnamen dengan pencahayaan LED anti-silau.',
            'tags' => [
                ['icon' => $sport['icon_svg'], 'text' => $sport['name']],
                ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.243 3.243a1 1 0 01.963-.22l6 2.25a1 1 0 01.548.905v10.148a1 1 0 01-1.548.905l-6-2.25a1 1 0 01-.963-.22L3.243 8.31a1 1 0 010-1.62l6-4.447z" clip-rule="evenodd" /></svg>', 'text' => 'Indoor Arena'],
                ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v.518l4.447 5.93a.75.75 0 01-.944 1.22l-1.42-1.065a.75.75 0 00-.91-.03L10 11.482l-1.873-1.124a.75.75 0 00-.91-.03L6 11.435a.75.75 0 01-.944-1.22L9.5 4.268V3.75A.75.75 0 0110 3zM10 6.25a.75.75 0 01.75.75v5.5a.75.75 0 01-1.5 0v-5.5a.75.75 0 01.75-.75z" clip-rule="evenodd" /></svg>', 'text' => 'Pro Surface']
            ],
            'schedules' => $schedules
        ];
    }
}

$page_title = 'Ketersediaan Jadwal - ' . htmlspecialchars($venue['name']);
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
        <a href="venue_detail.php?id=<?php echo $venue_id; ?>"><?php echo htmlspecialchars($venue['name']); ?></a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">Pilih Jadwal</span>
    </nav>

    <div class="availability-page-header">
        <a href="venue_detail.php?id=<?php echo $venue_id; ?>" class="back-btn-pill" aria-label="Kembali ke detail venue">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            <span>Kembali</span>
        </a>
        <div>
            <h1>Pilih Lapangan &amp; Jadwal</h1>
            <p><?php echo htmlspecialchars($venue['name']); ?> · <?php echo htmlspecialchars($venue['city']); ?></p>
        </div>
    </div>

    <?php if (empty($courts)): ?>
        <div class="empty-schedule-box">
            <h2>Belum Ada Jadwal Tersedia</h2>
            <p>Saat ini belum ada slot waktu yang dibuka untuk venue ini. Silakan periksa venue lainnya.</p>
            <a href="sewa_lapangan.php" class="btn-primary" style="max-width: 240px; margin: 12px auto 0;">Cari Venue Lain</a>
        </div>
    <?php else: ?>
        <div class="courts-list">
            <?php $cIdx = 0; foreach ($courts as $sportId => $court): ?>
                <section class="court-booking-card reveal-card" style="--stagger-index: <?php echo $cIdx++; ?>;">
                    <div class="court-left-column">
                        <div class="court-image-slider">
                            <img src="../assets/images/<?php echo htmlspecialchars($venue['main_image']); ?>" data-full-image="../assets/images/<?php echo htmlspecialchars($venue['main_image']); ?>" alt="<?php echo htmlspecialchars($court['name']); ?>" width="420" height="240" onerror="this.onerror=null;this.src='../assets/images/metro_atom.png';">
                            <span class="zoom-hint">Klik untuk perbesar</span>
                        </div>
                    </div>

                    <div class="court-right-column">
                        <div class="court-details">
                            <div class="court-title-row">
                                <h2><?php echo htmlspecialchars($court['name']); ?></h2>
                                <span class="date-indicator"><?php echo date('D, d M Y'); ?></span>
                            </div>
                            <p class="description"><?php echo htmlspecialchars($court['description']); ?></p>
                            <div class="court-features">
                                <?php foreach ($court['tags'] as $tag): ?>
                                    <div class="feature-item"><?php echo $tag['icon']; ?><span><?php echo htmlspecialchars($tag['text']); ?></span></div>
                                <?php endforeach; ?>
                            </div>

                            <button type="button" class="schedule-dropdown open" aria-expanded="true">
                                <span><?php echo count($court['schedules']); ?> Slot Jadwal</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="chevron" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                            </button>

                            <div class="time-slots-grid open">
                                <?php foreach ($court['schedules'] as $schedule): ?>
                                    <?php 
                                        $timeLabel = date('H:i', strtotime($schedule['start_time'])) . ' - ' . date('H:i', strtotime($schedule['end_time']));
                                    ?>
                                    <?php if ($schedule['is_booked']): ?>
                                        <button type="button" class="time-slot booked" disabled aria-disabled="true" title="Sudah dipesan">
                                            <span class="duration">60 Menit</span>
                                            <span class="time"><?php echo $timeLabel; ?></span>
                                            <span class="price">Booked</span>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="time-slot available"
                                            data-id="<?php echo $schedule['id']; ?>"
                                            data-venue="<?php echo htmlspecialchars($venue['name']); ?>"
                                            data-court="<?php echo htmlspecialchars($court['name']); ?>"
                                            data-time="<?php echo $timeLabel; ?>"
                                            data-price="<?php echo $schedule['price']; ?>">
                                            <span class="duration">60 Menit</span>
                                            <span class="time"><?php echo $timeLabel; ?></span>
                                            <span class="price">Rp<?php echo number_format($schedule['price'], 0, ',', '.'); ?></span>
                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<div class="detail-modal" id="imageModal" role="dialog" aria-modal="true" aria-label="Pratinjau Gambar Lapangan">
    <div class="detail-modal-content">
        <button type="button" class="detail-modal-close" id="imageModalClose" aria-label="Tutup pratinjau">×</button>
        <img src="../assets/images/<?php echo htmlspecialchars($venue['main_image']); ?>" alt="Pratinjau Lapangan" id="modalImageContent" class="modal-preview-img" width="800" height="450">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedDate = "<?php echo date('D, d M Y'); ?>";

    // Toggle schedule dropdown
    document.querySelectorAll('.schedule-dropdown').forEach(button => {
        button.addEventListener('click', () => {
            const grid = button.nextElementSibling;
            if (grid && grid.classList.contains('time-slots-grid')) {
                const isOpen = grid.classList.toggle('open');
                button.classList.toggle('open', isOpen);
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
        });
    });

    // Sync selected state with global cart
    function syncSlotsWithCart(cartItems) {
        const selectedIds = new Set((cartItems || []).map(item => String(item.id)));
        document.querySelectorAll('.time-slot.available').forEach(slotEl => {
            const isSelected = selectedIds.has(String(slotEl.dataset.id));
            slotEl.classList.toggle('selected', isSelected);
            slotEl.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
    }

    document.addEventListener('sportiva:cart-updated', function(e) {
        syncSlotsWithCart(e.detail.items);
    });

    if (window.SportivaUI) {
        syncSlotsWithCart(window.SportivaUI.getCart());
    }

    // Handle time slot selection via global cart manager
    document.querySelectorAll('.time-slots-grid').forEach(grid => {
        grid.addEventListener('click', function(e) {
            const slotEl = e.target.closest('.time-slot.available');
            if (!slotEl || !window.SportivaUI) return;

            const slotId = String(slotEl.dataset.id);
            const isAlreadySelected = slotEl.classList.contains('selected');

            if (isAlreadySelected) {
                window.SportivaUI.removeCartItem(slotId);
            } else {
                const slotData = {
                    id: slotId,
                    venue: slotEl.dataset.venue,
                    court: slotEl.dataset.court,
                    time: slotEl.dataset.time,
                    price: slotEl.dataset.price,
                    date: selectedDate
                };
                window.SportivaUI.addCartItem(slotData, true);
            }
        });
    });

    // Image lightbox
    const imageModal = document.getElementById('imageModal');
    const modalImageContent = document.getElementById('modalImageContent');
    const closeModalBtn = document.getElementById('imageModalClose');

    document.querySelectorAll('.court-image-slider img').forEach(image => {
        image.addEventListener('click', function() {
            modalImageContent.src = this.dataset.fullImage;
            imageModal.classList.add('is-visible');
        });
    });

    const closeModal = () => imageModal?.classList.remove('is-visible');
    closeModalBtn?.addEventListener('click', closeModal);
    imageModal?.addEventListener('click', (e) => {
        if (e.target === imageModal) closeModal();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
