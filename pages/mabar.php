<?php
$page_title = 'Komunitas Mabar - Sportiva';
$active_page = 'mabar';
$base_url = '../';
$css_files = ['mabar_styles.css'];
$include_auth_modal = true;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query("SELECT * FROM mabar_events ORDER BY event_date DESC, start_time DESC");
$events = $stmt->fetchAll();
?>

<main class="mabar-page-main">
  <!-- Banner Section -->
  <section class="banner-section reveal-card">
    <div class="banner-image-wrapper">
        <img src="../assets/images/banner_mabar.png" alt="Komunitas Main Bareng Sportiva" width="1280" height="360" fetchpriority="high" onerror="this.onerror=null;this.src='../assets/images/gala_sport.png';">
        <div class="banner-overlay-content">
            <span class="banner-kicker">Komunitas Olahraga</span>
            <h1>Main Bareng (Mabar) Lebih Seru</h1>
            <p>Gabung jadwal tanding terbuka bersama komunitas olahraga di kotamu tanpa harus membawa satu tim penuh.</p>
        </div>
    </div>
  </section>

  <!-- Results Section -->
  <section class="results-section">
    <div class="results-header">
      <p>Menampilkan <strong><?php echo count($events); ?> sesi mabar terbuka</strong></p>
    </div>

    <div class="content-area">
      <?php if (empty($events)): ?>
        <div class="mabar-empty-state">
          <h2>Belum Ada Event Mabar</h2>
          <p>Saat ini belum ada jadwal mabar komunitas yang terdaftar. Silakan cek kembali nanti atau sewa lapangan langsung.</p>
          <a href="sewa_lapangan.php" class="btn-mabar-empty">Sewa Lapangan</a>
        </div>
      <?php else: ?>
        <div class="event-grid">
          <?php foreach ($events as $idx => $event): ?>
            <?php
              $filled = (int)$event['slots_filled'];
              $total = max(1, (int)$event['total_slots']);
              $percent = min(100, round(($filled / $total) * 100));
            ?>
            <article class="event-card reveal-card" style="--stagger-index: <?php echo $idx; ?>;">
              <a href="mabar_detail.php?id=<?php echo htmlspecialchars($event['id']); ?>" class="event-card-link">
                <div class="event-card-top">
                  <span class="event-sport-pill"><?php echo htmlspecialchars($event['sport']); ?> · <?php echo htmlspecialchars($event['skill_level']); ?></span>
                  <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                </div>

                <div class="event-meta">
                  <div class="event-meta-row">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span><?php echo date('D, d M Y · H:i', strtotime($event['event_date'] . ' ' . $event['start_time'])); ?> WIB</span>
                  </div>
                  <div class="event-meta-row">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span><?php echo htmlspecialchars($event['venue_name']); ?></span>
                  </div>
                </div>

                <?php if (!empty($event['tags'])): ?>
                <div class="event-tags">
                  <?php 
                    $tags = explode(',', $event['tags']);
                    foreach ($tags as $tag_text) {
                      $clean_tag = trim(str_replace('⭐', '', $tag_text));
                      $tag_class = (strpos(strtolower($tag_text), 'superhost') !== false) ? 'superhost' : 'booked';
                      echo "<span class='tag " . $tag_class . "'>" . htmlspecialchars($clean_tag) . "</span>";
                    }
                  ?>
                </div>
                <?php endif; ?>

                <div class="event-slots-progress">
                  <div class="slots-label-row">
                    <span>Kuota Peserta</span>
                    <strong class="slot-info"><?php echo $filled; ?> / <?php echo $total; ?> Slot</strong>
                  </div>
                  <div class="slots-bar-track" aria-hidden="true">
                    <div class="slots-bar-fill" style="width: <?php echo $percent; ?>%;"></div>
                  </div>
                </div>

                <div class="host-info">
                  <img src="../assets/images/<?php echo htmlspecialchars($event['host_logo']); ?>" class="host-logo" alt="" width="40" height="40" onerror="this.onerror=null;this.src='../assets/images/logo_bmx.png';">
                  <div class="host-details">
                    <span class="host-organizer">Penyelenggara</span>
                    <p class="host-name"><?php echo htmlspecialchars($event['host_name']); ?></p>
                  </div>
                  <span class="event-detail-cue">Detail</span>
                </div>
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
