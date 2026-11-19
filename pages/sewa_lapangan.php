<?php
$page_title = 'Sewa Lapangan - Sportiva';
$active_page = 'sewa';
$base_url = '../';
$css_files = ['sewa_lapangan_styles.css'];
$include_auth_modal = true;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

// --- PENGATURAN PAGINASI ---
$venues_per_page = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $venues_per_page;

// --- Ambil semua filter dari URL ---
$search_term = $_GET['venue_name'] ?? '';
$selected_city = $_GET['city'] ?? '';
$selected_sport_id = $_GET['sport_id'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'name_asc';
$min_price_filter = $_GET['min_price'] ?? '';
$max_price_filter = $_GET['max_price'] ?? '';

try {
    $stmt_cities = $pdo->query("SELECT DISTINCT city FROM venues WHERE city IS NOT NULL AND city != '' ORDER BY city ASC");
    $cities = $stmt_cities->fetchAll(PDO::FETCH_ASSOC);
    $stmt_sports_filter = $pdo->query("SELECT id, name FROM sports ORDER BY name ASC");
    $sports_for_filter = $stmt_sports_filter->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error mengambil data filter: " . $e->getMessage());
}

$sql_from_joins = "FROM venues v 
                   LEFT JOIN venue_sports vs ON v.id = vs.venue_id
                   LEFT JOIN schedules s ON v.id = s.venue_id";
$sql_group_by = "GROUP BY v.id";

$where_clauses = [];
$params = [];

if (!empty($search_term)) {
    $where_clauses[] = "v.name LIKE :search_term";
    $params[':search_term'] = '%' . $search_term . '%';
}
if (!empty($selected_city)) {
    $where_clauses[] = "v.city = :city";
    $params[':city'] = $selected_city;
}
if (!empty($selected_sport_id)) {
    $where_clauses[] = "vs.sport_id = :sport_id";
    $params[':sport_id'] = $selected_sport_id;
}
$sql_where = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

$having_clauses = [];
if ($min_price_filter !== '' && is_numeric($min_price_filter)) {
    $having_clauses[] = "min_price >= :min_price";
    $params[':min_price'] = $min_price_filter;
}
if ($max_price_filter !== '' && is_numeric($max_price_filter)) {
    $having_clauses[] = "min_price <= :max_price";
    $params[':max_price'] = $max_price_filter;
}
$sql_having = !empty($having_clauses) ? " HAVING " . implode(" AND ", $having_clauses) : "";

$count_query_string = "SELECT COUNT(*) FROM (
                            SELECT v.id, MIN(s.price) as min_price 
                            {$sql_from_joins} 
                            {$sql_where} 
                            {$sql_group_by} 
                            {$sql_having}
                       ) AS filtered_venues";
$stmt_count = $pdo->prepare($count_query_string);
$stmt_count->execute($params);
$total_venues = $stmt_count->fetchColumn();
$total_pages = $total_venues > 0 ? ceil($total_venues / $venues_per_page) : 0;

$venues = [];
$venue_count_on_page = 0;
if ($total_venues > 0) {
    $sql_select_data = "SELECT v.id, v.name, v.address, v.city, v.main_image, v.rating, MIN(s.price) as min_price";
    $sql_order_by = "";
    switch ($sort_by) {
        case 'price_asc': $sql_order_by = " ORDER BY CASE WHEN min_price IS NULL THEN 1 ELSE 0 END, min_price ASC, v.name ASC"; break;
        case 'price_desc': $sql_order_by = " ORDER BY min_price DESC, v.name ASC"; break;
        case 'name_desc': $sql_order_by = " ORDER BY v.name DESC"; break;
        default: $sql_order_by = " ORDER BY v.name ASC"; break;
    }

    $sql_limit = " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $venues_per_page;
    $params[':offset'] = $offset;

    $final_sql = $sql_select_data . " " . $sql_from_joins . " " . $sql_where . " " . $sql_group_by . " " . $sql_having . " " . $sql_order_by . " " . $sql_limit;
    $stmt = $pdo->prepare($final_sql);

    foreach ($params as $key => &$value) {
        $type = ($key === ':limit' || $key === ':offset') ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindParam($key, $value, $type);
    }
    $stmt->execute();
    $venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $venue_count_on_page = count($venues);

    if ($venue_count_on_page > 0) {
        $venue_ids = array_column($venues, 'id');
        $placeholders = implode(',', array_fill(0, count($venue_ids), '?'));
        $sql_sports = "SELECT vs.venue_id, s.name, s.icon_svg FROM venue_sports vs JOIN sports s ON vs.sport_id = s.id WHERE vs.venue_id IN ($placeholders)";
        $stmt_sports = $pdo->prepare($sql_sports);
        $stmt_sports->execute($venue_ids);
        $venue_sports_data = $stmt_sports->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
    }
}
$has_active_filters = ($search_term !== '' || $selected_city !== '' || $selected_sport_id !== '' || $min_price_filter !== '' || $max_price_filter !== '');
?>

<main class="main-content">
    <header class="page-intro">
        <div>
            <h1>Eksplorasi &amp; Sewa Lapangan</h1>
            <p>Temukan arena olahraga terbaik di kotamu dengan ketersediaan jadwal real-time.</p>
        </div>
    </header>

    <form class="search-form" action="sewa_lapangan.php" method="GET" id="filter-form" role="search">
        <div class="input-group">
            <label for="filter-venue-name" class="sr-only">Cari nama venue</label>
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
            <input type="text" id="filter-venue-name" name="venue_name" placeholder="Cari nama venue..." value="<?php echo htmlspecialchars($search_term); ?>">
        </div>

        <div class="input-group has-select">
            <label for="filter-city" class="sr-only">Filter Kota</label>
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <select id="filter-city" name="city" onchange="this.form.submit()">
                <option value="">Semua Kota</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city['city']); ?>" <?php if ($selected_city == $city['city']) echo 'selected'; ?>><?php echo htmlspecialchars($city['city']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-group has-select">
           <label for="filter-sport" class="sr-only">Filter Cabang Olahraga</label>
           <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.84l8.57 3.9a2 2 0 0 0 1.66 0l8.57-3.9a1 1 0 0 0 0-1.84Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
           <select id="filter-sport" name="sport_id" onchange="this.form.submit()">
               <option value="">Semua Olahraga</option>
               <?php foreach ($sports_for_filter as $sport): ?>
                   <option value="<?php echo $sport['id']; ?>" <?php if ($selected_sport_id == $sport['id']) echo 'selected'; ?>><?php echo htmlspecialchars($sport['name']); ?></option>
               <?php endforeach; ?>
           </select>
        </div>
        
        <button type="button" class="filter-button <?php echo ($min_price_filter !== '' || $max_price_filter !== '') ? 'has-active-price' : ''; ?>" id="open-filter-modal" aria-label="Filter rentang biaya">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
            <span>Filter Harga</span>
        </button>
        
        <button type="submit" class="search-button">Cari Venue</button>
        
        <input type="hidden" name="min_price" id="modal_min_price" value="<?php echo htmlspecialchars($min_price_filter); ?>">
        <input type="hidden" name="max_price" id="modal_max_price" value="<?php echo htmlspecialchars($max_price_filter); ?>">
    </form>
  
    <div class="results-header">
        <div class="results-count">
            Menampilkan <strong><?php echo $total_venues; ?></strong> venue olahraga
            <?php if ($has_active_filters): ?>
                <a href="sewa_lapangan.php" class="reset-filter-link">Reset Filter</a>
            <?php endif; ?>
        </div>
        <div class="sort-options">
            <label for="sort-select">Urutkan:</label>
            <select id="sort-select" name="sort_by" onchange="this.form.submit()" form="filter-form">
                <option value="name_asc" <?php if($sort_by == 'name_asc') echo 'selected'; ?>>Nama (A-Z)</option>
                <option value="name_desc" <?php if($sort_by == 'name_desc') echo 'selected'; ?>>Nama (Z-A)</option>
                <option value="price_asc" <?php if($sort_by == 'price_asc') echo 'selected'; ?>>Harga Termurah</option>
                <option value="price_desc" <?php if($sort_by == 'price_desc') echo 'selected'; ?>>Harga Termahal</option>
            </select>
        </div>
    </div>

    <?php if ($total_venues > 0): ?>
        <div class="venue-grid">
            <?php foreach ($venues as $idx => $venue): ?>
            <article class="venue-card reveal-card" style="--stagger-index: <?php echo $idx; ?>;">
              <a href="venue_detail.php?id=<?php echo $venue['id']; ?>">
                <div class="card-image-container">
                  <img src="../assets/images/<?php echo htmlspecialchars($venue['main_image'] ?? 'metro_atom.png'); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>" width="400" height="225" <?php echo ($idx < 3) ? 'fetchpriority="high"' : 'loading="lazy"'; ?> onerror="this.onerror=null;this.src='../assets/images/metro_atom.png';">
                  <span class="venue-city-badge"><?php echo htmlspecialchars($venue['city']); ?></span>
                </div>
                <div class="card-info">
                    <div class="venue-header-row">
                        <span class="venue-type">Arena Tersertifikasi</span>
                        <?php if (!empty($venue['rating']) && $venue['rating'] > 0): ?>
                        <div class="venue-rating-pill">
                            <svg class="icon-star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10.868 2.884c.321-.662 1.215-.662 1.536 0l1.681 3.468 3.82.556c.734.107 1.028.997.494 1.503l-2.764 2.694.652 3.803c.124.722-.638 1.278-1.284.944L10 13.6l-3.419 1.796c-.646.334-1.408-.222-1.284-.944l.652-3.803L2.27 8.411c-.534-.506-.24-1.396.494-1.503l3.82-.556 1.681-3.468z" clip-rule="evenodd" /></svg>
                            <span><?php echo number_format($venue['rating'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h2><?php echo htmlspecialchars($venue['name']); ?></h2>
                    <p class="venue-address-snippet"><?php echo htmlspecialchars($venue['address']); ?></p>
                    <?php if (!empty($venue_sports_data[$venue['id']])): ?>
                    <div class="venue-sports">
                        <?php foreach (array_slice($venue_sports_data[$venue['id']], 0, 3) as $sport): ?>
                        <div class="sport-item">
                            <?php echo $sport['icon_svg']; ?>
                            <span><?php echo htmlspecialchars($sport['name']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="card-bottom">
                        <?php if (!empty($venue['min_price']) && $venue['min_price'] > 0): ?>
                            <div class="venue-price">
                                <span>Mulai</span>
                                <strong>Rp<?php echo number_format($venue['min_price'], 0, ',', '.'); ?></strong>
                                <span>/sesi</span>
                            </div>
                        <?php else: ?>
                            <div class="venue-price"><span>Lihat detail jadwal</span></div>
                        <?php endif; ?>
                        <span class="card-action-cue">Pesan</span>
                    </div>
                </div>
              </a>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav class="pagination-container" aria-label="Navigasi halaman venue">
            <ul class="pagination">
                <?php
                $query_params = $_GET;
                if ($current_page > 1) {
                    $query_params['page'] = $current_page - 1;
                    echo '<li><a href="?' . htmlspecialchars(http_build_query($query_params)) . '" aria-label="Halaman sebelumnya">Sebelumnya</a></li>';
                }
                $range = 2;
                $last_page_shown = 0;
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
                        if ($last_page_shown && $i > $last_page_shown + 1) {
                            echo '<li class="disabled ellipsis"><span>...</span></li>';
                        }
                        $active_class = ($i == $current_page) ? 'active' : '';
                        $query_params['page'] = $i;
                        echo '<li class="' . $active_class . '"><a href="?' . htmlspecialchars(http_build_query($query_params)) . '">' . $i . '</a></li>';
                        $last_page_shown = $i;
                    }
                }
                if ($current_page < $total_pages) {
                    $query_params['page'] = $current_page + 1;
                    echo '<li><a href="?' . htmlspecialchars(http_build_query($query_params)) . '" aria-label="Halaman berikutnya">Berikutnya</a></li>';
                }
                ?>
            </ul>
        </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="no-results-container">
            <div class="no-results-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <h2>Venue Tidak Ditemukan</h2>
            <p>Tidak ada lapangan yang cocok dengan kombinasi filter pencarian Anda saat ini.</p>
            <a href="sewa_lapangan.php" class="btn-reset-empty">Tampilkan Semua Venue</a>
        </div>
    <?php endif; ?>
</main>

<!-- MODAL FILTER HARGA -->
<div class="modal-overlay" id="filter-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="filter-modal-title">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="filter-modal-title">Filter Rentang Harga</h2>
            <button type="button" class="modal-close-btn" id="close-filter-modal" aria-label="Tutup filter">×</button>
        </div>
        <div class="modal-body">
            <p class="modal-subtitle">Tentukan batas biaya sewa per sesi (dalam Rupiah).</p>
            <div class="price-filter-inputs">
                <div class="price-input-group">
                    <label for="min_price_display">Harga Minimum (Rp)</label>
                    <input type="number" id="min_price_display" placeholder="30000" value="<?php echo htmlspecialchars($min_price_filter); ?>">
                </div>
                <div class="price-input-group">
                    <label for="max_price_display">Harga Maksimum (Rp)</label>
                    <input type="number" id="max_price_display" placeholder="150000" value="<?php echo htmlspecialchars($max_price_filter); ?>">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="clear-filter-btn" id="clear-price-filter-btn">Reset</button>
            <button type="button" class="apply-filter-btn" id="apply-filter-btn">Terapkan Filter</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const openModalBtn = document.getElementById('open-filter-modal');
    const closeModalBtn = document.getElementById('close-filter-modal');
    const modalOverlay = document.getElementById('filter-modal-overlay');
    const applyFilterBtn = document.getElementById('apply-filter-btn');
    const clearFilterBtn = document.getElementById('clear-price-filter-btn');
    const filterForm = document.getElementById('filter-form');
    const minPriceDisplay = document.getElementById('min_price_display');
    const maxPriceDisplay = document.getElementById('max_price_display');
    const minPriceHidden = document.getElementById('modal_min_price');
    const maxPriceHidden = document.getElementById('modal_max_price');

    const openModal = () => modalOverlay?.classList.add('active');
    const closeModal = () => modalOverlay?.classList.remove('active');

    openModalBtn?.addEventListener('click', openModal);
    closeModalBtn?.addEventListener('click', closeModal);
    modalOverlay?.addEventListener('click', (e) => { if (e.target === modalOverlay) closeModal(); });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay?.classList.contains('active')) {
            closeModal();
        }
    });

    applyFilterBtn?.addEventListener('click', function() {
        minPriceHidden.value = minPriceDisplay.value;
        maxPriceHidden.value = maxPriceDisplay.value;
        filterForm.submit();
    });

    clearFilterBtn?.addEventListener('click', function() {
        minPriceDisplay.value = '';
        maxPriceDisplay.value = '';
        minPriceHidden.value = '';
        maxPriceHidden.value = '';
        filterForm.submit();
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
