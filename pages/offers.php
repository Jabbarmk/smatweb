<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Top Offers';
$currentPage = 'offers';

$db = getDB();

$UAE_EMIRATES = ['Dubai','Abu Dhabi','Sharjah','Ajman','Ras Al Khaimah','Fujairah','Umm Al Quwain'];

$dbEmirates = $db->query("SELECT DISTINCT emirate FROM offers WHERE is_active=1 AND emirate IS NOT NULL AND emirate <> '' ORDER BY emirate")->fetchAll(PDO::FETCH_COLUMN);
$emirates = array_unique(array_merge($UAE_EMIRATES, $dbEmirates));

$selectedLoc = $_GET['loc'] ?? $emirates[0] ?? 'Dubai';
$selectedCat = $_GET['cat'] ?? '';

$catStmt = $db->query("SELECT DISTINCT bc.id, bc.name, bc.icon FROM offers o JOIN business_categories bc ON o.category_id = bc.id WHERE o.is_active=1 ORDER BY bc.sort_order, bc.name");
$categories = $catStmt->fetchAll();

$where = ['o.is_active = 1', 'o.emirate = ?'];
$params = [$selectedLoc];
if ($selectedCat) { $where[] = 'o.category_id = ?'; $params[] = $selectedCat; }

$stmt = $db->prepare("
    SELECT o.*, b.name AS business_name, b.logo AS business_logo, b.image AS business_image,
           b.address AS business_address, b.phone AS business_phone, b.whatsapp AS business_whatsapp,
           bc.name AS category_name
    FROM offers o
    JOIN businesses b ON o.business_id = b.id
    LEFT JOIN business_categories bc ON o.category_id = bc.id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY o.ranking DESC, o.created_at DESC
");
$stmt->execute($params);
$offers = $stmt->fetchAll();

$foodFallbacks = [
    'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1561651188-d207bbec4ec3?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&h=1000&fit=crop',
    'https://images.unsplash.com/photo-1529543544282-ea669407fca3?w=800&h=1000&fit=crop',
];
$logoFallback  = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=80&h=80&fit=crop';

include __DIR__ . '/../includes/header.php';
?>

<style>
/* ── Grid system ─────────────────────────────── */
.offers-grid {
    padding: 0 20px 24px;
    display: grid;
    gap: 16px;
    grid-template-columns: 1fr; /* default: 1 col */
}
.offers-grid.cols-2 { grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 0 12px 24px; }
.offers-grid.cols-3 { grid-template-columns: repeat(3, 1fr); gap: 8px;  padding: 0 10px 24px; }

/* ── Card base ───────────────────────────────── */
.offer-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 6px 28px rgba(13,27,42,0.09);
    transition: transform .15s ease, box-shadow .15s ease;
}
.offer-card:active { transform: scale(.98); }

/* smaller radius in tight grids */
.cols-2 .offer-card, .cols-3 .offer-card { border-radius: 14px; box-shadow: 0 3px 12px rgba(13,27,42,0.08); }

/* ── Business header ─────────────────────────── */
.offer-biz-header { display: flex; align-items: center; gap: 10px; padding: 12px 14px; text-decoration: none; color: inherit; }
.cols-2 .offer-biz-header { padding: 8px 10px; gap: 7px; }
.cols-3 .offer-biz-header { padding: 6px 8px;  gap: 5px; }

.offer-biz-avatar { width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0; }
.cols-2 .offer-biz-avatar { width:28px;height:28px; }
.cols-3 .offer-biz-avatar { width:22px;height:22px; }

.offer-biz-name { font-size:14px;font-weight:700;color:#1A1A2E;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.cols-2 .offer-biz-name { font-size:11px; }
.cols-3 .offer-biz-name { font-size:10px; }

.offer-biz-sub { font-size:11px;color:#9BA4B5;display:flex;align-items:center;gap:4px; }
.cols-2 .offer-biz-sub { display:none; }
.cols-3 .offer-biz-sub { display:none; }

.offer-discount-badge { background:#FF6B6B;color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:12px;flex-shrink:0; }
.cols-2 .offer-discount-badge { font-size:9px;padding:3px 6px;border-radius:8px; }
.cols-3 .offer-discount-badge { display:none; }

/* ── Image ───────────────────────────────────── */
.offer-img { width:100%;object-fit:cover;display:block; aspect-ratio:3/4; }
.cols-2 .offer-img { aspect-ratio:1/1; }
.cols-3 .offer-img { aspect-ratio:1/1; }

/* ── Body ────────────────────────────────────── */
.offer-body { padding: 16px 18px 18px; }
.cols-2 .offer-body { padding: 10px 10px 12px; }
.cols-3 .offer-body { padding: 7px 8px 10px; }

.offer-title { font-size:16px;font-weight:800;color:#1A1A2E;margin:0 0 6px; }
.cols-2 .offer-title { font-size:13px;margin:0 0 4px; }
.cols-3 .offer-title { font-size:11px;margin:0 0 3px;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

.offer-desc { font-size:13px;color:#636E8A;line-height:1.5;margin:0 0 10px; }
.cols-2 .offer-desc { display:none; }
.cols-3 .offer-desc { display:none; }

.offer-price-row { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:12px; }
.cols-2 .offer-price-row { margin-bottom:8px; gap:4px; }
.cols-3 .offer-price-row { margin-bottom:6px; flex-direction:column;align-items:flex-start;gap:2px; }

.offer-price { font-size:20px;font-weight:800;color:#6C5CE7; }
.cols-2 .offer-price { font-size:14px; }
.cols-3 .offer-price { font-size:12px; }

.offer-original { font-size:13px;color:#9BA4B5;text-decoration:line-through; }
.cols-2 .offer-original { font-size:11px; }
.cols-3 .offer-original { display:none; }

.offer-rating { display:flex;align-items:center;gap:4px;background:#FFF5E7;border-radius:10px;padding:4px 10px; }
.cols-2 .offer-rating { padding:3px 7px; }
.cols-3 .offer-rating { padding:2px 5px; border-radius:6px; }

.offer-rating i  { color:#FDCB6E;font-size:12px; }
.offer-rating span { font-size:12px;font-weight:700;color:#1A1A2E; }
.cols-2 .offer-rating span { font-size:10px; }
.cols-3 .offer-rating span { font-size:10px; }
.cols-3 .offer-rating i { font-size:10px; }

.offer-btns { display:flex;gap:8px; }
.cols-2 .offer-btns { gap:5px; }
.cols-3 .offer-btns { flex-direction:column;gap:4px; }

.offer-btn { flex:1;padding:11px 0;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px; }
.cols-2 .offer-btn { padding:8px 0;font-size:11px;border-radius:8px;gap:3px; }
.cols-3 .offer-btn { padding:6px 0;font-size:10px;border-radius:7px;gap:2px; }

/* hide "Read More" text in 3-col, show icon only */
.cols-3 .offer-btn-label { display:none; }

/* ── Grid toggle buttons ─────────────────────── */
.grid-toggle { display:flex;gap:4px;align-items:center; }
.grid-btn {
    width:30px;height:30px;border:none;border-radius:8px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;gap:2px;
    background:#f0f2f8;transition:background .15s;padding:0;
}
.grid-btn.active { background:#6C5CE7; }
.grid-btn svg rect { fill:#9BA4B5; }
.grid-btn.active svg rect { fill:#fff; }
</style>

<div class="page-topbar">
    <a href="<?= SITE_URL ?>/" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1>OFFERS</h1>
    <div class="right-actions"></div>
</div>

<!-- Location Selector -->
<div style="margin:12px 16px;background:#fff;border-radius:16px;padding:10px 14px;display:flex;align-items:center;gap:10px;box-shadow:0 2px 8px rgba(13,27,42,0.05);">
    <i class="fas fa-map-marker-alt" style="color:#6C5CE7;font-size:16px;"></i>
    <span style="font-size:12px;color:#9BA4B5;font-weight:600;">LOCATION</span>
    <form method="GET" action="" style="flex:1;margin:0;">
        <?php if ($selectedCat): ?><input type="hidden" name="cat" value="<?= e($selectedCat) ?>"><?php endif; ?>
        <select name="loc" onchange="this.form.submit()"
            style="width:100%;border:none;font-size:14px;font-weight:700;color:#1A1A2E;background:transparent;outline:none;cursor:pointer;">
            <?php foreach ($emirates as $em): ?>
                <option value="<?= e($em) ?>" <?= $em === $selectedLoc ? 'selected' : '' ?>><?= e($em) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <i class="fas fa-chevron-down" style="color:#9BA4B5;font-size:12px;pointer-events:none;"></i>
</div>

<!-- Category Tabs -->
<div style="margin:0 0 14px;padding:0 12px;display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;-webkit-overflow-scrolling:touch;">
    <a href="?loc=<?= urlencode($selectedLoc) ?>"
       style="padding:8px 16px;border-radius:999px;background:<?= !$selectedCat ? '#6C5CE7' : '#fff' ?>;color:<?= !$selectedCat ? '#fff' : '#1A1A2E' ?>;font-size:13px;font-weight:700;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(13,27,42,0.05);flex-shrink:0;">
        <i class="fas fa-fire" style="font-size:11px;"></i> All
    </a>
    <?php foreach ($categories as $cat): ?>
    <a href="?loc=<?= urlencode($selectedLoc) ?>&cat=<?= $cat['id'] ?>"
       style="padding:8px 16px;border-radius:999px;background:<?= (string)$cat['id'] === $selectedCat ? '#6C5CE7' : '#fff' ?>;color:<?= (string)$cat['id'] === $selectedCat ? '#fff' : '#1A1A2E' ?>;font-size:13px;font-weight:700;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:0 2px 6px rgba(13,27,42,0.05);flex-shrink:0;">
        <?php if ($cat['icon']): ?><span><?= $cat['icon'] ?></span><?php endif; ?>
        <?= e($cat['name']) ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Title + Grid toggle -->
<div style="padding:0 16px 12px;display:flex;align-items:center;justify-content:space-between;">
    <div>
        <h2 style="font-size:20px;font-weight:800;color:#1A1A2E;margin:0 0 2px;">
            Top Offers in <span style="color:#6C5CE7;"><?= e($selectedLoc) ?></span>
        </h2>
        <span style="font-size:12px;color:#9BA4B5;font-weight:600;"><?= count($offers) ?> offers</span>
    </div>

    <!-- Grid toggle: 1 / 2 / 3 -->
    <div class="grid-toggle">
        <!-- 1 col -->
        <button class="grid-btn active" id="btn1" onclick="setGrid(1)" title="1 column">
            <svg width="16" height="16" viewBox="0 0 16 16">
                <rect x="2" y="2" width="12" height="4" rx="1"/>
                <rect x="2" y="8" width="12" height="4" rx="1"/>
            </svg>
        </button>
        <!-- 2 col -->
        <button class="grid-btn" id="btn2" onclick="setGrid(2)" title="2 columns">
            <svg width="16" height="16" viewBox="0 0 16 16">
                <rect x="1" y="1" width="6" height="6" rx="1"/>
                <rect x="9" y="1" width="6" height="6" rx="1"/>
                <rect x="1" y="9" width="6" height="6" rx="1"/>
                <rect x="9" y="9" width="6" height="6" rx="1"/>
            </svg>
        </button>
        <!-- 3 col -->
        <button class="grid-btn" id="btn3" onclick="setGrid(3)" title="3 columns">
            <svg width="16" height="16" viewBox="0 0 16 16">
                <rect x="1"  y="1" width="4" height="4" rx="1"/>
                <rect x="6"  y="1" width="4" height="4" rx="1"/>
                <rect x="11" y="1" width="4" height="4" rx="1"/>
                <rect x="1"  y="7" width="4" height="4" rx="1"/>
                <rect x="6"  y="7" width="4" height="4" rx="1"/>
                <rect x="11" y="7" width="4" height="4" rx="1"/>
                <rect x="1"  y="13" width="4" height="2" rx="1"/>
                <rect x="6"  y="13" width="4" height="2" rx="1"/>
                <rect x="11" y="13" width="4" height="2" rx="1"/>
            </svg>
        </button>
    </div>
</div>

<!-- Offers Grid -->
<div class="offers-grid" id="offersGrid">

<?php if (empty($offers)): ?>
    <div style="background:#fff;padding:32px;border-radius:16px;text-align:center;color:#9BA4B5;grid-column:1/-1;">
        <i class="fas fa-tag" style="font-size:32px;margin-bottom:12px;display:block;color:#DDD;"></i>
        No offers found in <?= e($selectedLoc) ?>.
    </div>
<?php endif; ?>

<?php foreach ($offers as $idx => $offer):
    $logoSrc = !empty($offer['business_logo']) ? getImageUrl($offer['business_logo'], 'businesses')
             : (!empty($offer['business_image']) ? getImageUrl($offer['business_image'], 'businesses') : $logoFallback);
    $imgSrc  = !empty($offer['image']) ? getImageUrl($offer['image'], 'offers') : $foodFallbacks[$idx % count($foodFallbacks)];
    $imgErr  = $foodFallbacks[($idx + 1) % count($foodFallbacks)];
    $waLink  = !empty($offer['business_whatsapp'])
             ? 'https://wa.me/' . preg_replace('/\D/', '', $offer['business_whatsapp'])
               . '?text=' . rawurlencode('Hi, I want to order: ' . $offer['title'] . ' (' . $offer['currency'] . ' ' . number_format((float)$offer['price'], 0) . ')')
             : null;
    $orderHref  = $waLink ?: ($offer['business_phone'] ? 'tel:' . $offer['business_phone'] : SITE_URL . '/pages/offer_detail.php?id=' . $offer['id']);
    $orderLabel = $waLink ? 'Order Now' : ($offer['business_phone'] ? 'Call Now' : 'Order Now');
    $orderIcon  = $waLink ? 'fa-bolt' : ($offer['business_phone'] ? 'fa-phone' : 'fa-bolt');
?>
<div class="offer-card">

    <!-- Business header -->
    <a href="<?= SITE_URL ?>/pages/business_detail.php?id=<?= $offer['business_id'] ?>" class="offer-biz-header">
        <img src="<?= e($logoSrc) ?>" alt="<?= e($offer['business_name']) ?>"
             onerror="this.src='<?= $logoFallback ?>'" class="offer-biz-avatar">
        <div style="flex:1;min-width:0;">
            <div class="offer-biz-name"><?= e($offer['business_name']) ?></div>
            <div class="offer-biz-sub">
                <i class="fas fa-map-marker-alt"></i>
                <?= e($offer['emirate'] ?: $offer['business_emirate'] ?: '') ?>
                <?php if ($offer['category_name']): ?><span>·</span><span><?= e($offer['category_name']) ?></span><?php endif; ?>
            </div>
        </div>
        <?php if ($offer['discount_percent']): ?>
        <div class="offer-discount-badge">-<?= (int)$offer['discount_percent'] ?>%</div>
        <?php endif; ?>
    </a>

    <!-- Image -->
    <a href="<?= SITE_URL ?>/pages/offer_detail.php?id=<?= $offer['id'] ?>" style="display:block;">
        <img src="<?= e($imgSrc) ?>" alt="<?= e($offer['title']) ?>"
             onerror="this.src='<?= $imgErr ?>'" class="offer-img">
    </a>

    <!-- Body -->
    <div class="offer-body">
        <a href="<?= SITE_URL ?>/pages/offer_detail.php?id=<?= $offer['id'] ?>" style="text-decoration:none;color:inherit;">
            <h3 class="offer-title"><?= e($offer['title']) ?></h3>
            <?php if ($offer['description']): ?>
            <p class="offer-desc"><?= e(mb_strimwidth($offer['description'], 0, 120, '…')) ?></p>
            <?php endif; ?>
        </a>

        <div class="offer-price-row">
            <div style="display:flex;align-items:baseline;gap:6px;flex-wrap:wrap;">
                <span class="offer-price"><?= e($offer['currency']) ?> <?= number_format((float)$offer['price'], 0) ?></span>
                <?php if ($offer['original_price'] && $offer['original_price'] > $offer['price']): ?>
                <span class="offer-original"><?= e($offer['currency']) ?> <?= number_format((float)$offer['original_price'], 0) ?></span>
                <?php endif; ?>
            </div>
            <div class="offer-rating">
                <i class="fas fa-star"></i>
                <span><?= number_format((float)$offer['rating'], 1) ?></span>
            </div>
        </div>

        <div class="offer-btns">
            <a href="<?= SITE_URL ?>/pages/offer_detail.php?id=<?= $offer['id'] ?>"
               class="offer-btn" style="background:#f0f2f8;color:#1A1A2E;">
                <i class="fas fa-book-open"></i> <span class="offer-btn-label">Read More</span>
            </a>
            <a href="<?= e($orderHref) ?>" <?= $waLink ? 'target="_blank"' : '' ?>
               class="offer-btn" style="background:#6C5CE7;color:#fff;">
                <i class="fas <?= $orderIcon ?>"></i> <span class="offer-btn-label"><?= $orderLabel ?></span>
            </a>
        </div>
    </div>
</div>
<?php endforeach; ?>

</div>

<script>
(function(){
    var grid = document.getElementById('offersGrid');
    var saved = localStorage.getItem('offersGrid') || '1';
    applyGrid(saved);

    function applyGrid(n) {
        grid.className = 'offers-grid' + (n > 1 ? ' cols-' + n : '');
        [1,2,3].forEach(function(i){
            var btn = document.getElementById('btn'+i);
            btn.classList.toggle('active', String(i) === String(n));
        });
    }

    window.setGrid = function(n) {
        localStorage.setItem('offersGrid', n);
        applyGrid(n);
    };

    applyGrid(saved);
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
