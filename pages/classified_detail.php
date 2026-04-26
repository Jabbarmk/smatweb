<?php
require_once __DIR__ . '/../includes/config.php';
$currentPage = 'classifieds';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT c.*, cc.name as category_name FROM classifieds c LEFT JOIN classified_categories cc ON c.category_id = cc.id WHERE c.id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: ' . SITE_URL . '/pages/classifieds.php');
    exit;
}

$pageTitle = $item['title'] . ' - SMARTUAE';

/* Fallback detail image based on category name keyword */
$categoryKeyword = strtolower($item['category_name'] ?? 'item');
$detailFallbacks = [
    'mobile'    => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=600&h=600&fit=crop',
    'phone'     => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=600&h=600&fit=crop',
    'laptop'    => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&h=600&fit=crop',
    'car'       => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=600&h=600&fit=crop',
    'vehicle'   => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=600&h=600&fit=crop',
    'furniture' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&h=600&fit=crop',
    'estate'    => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&h=600&fit=crop',
    'watch'     => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&h=600&fit=crop',
];
$detailFallback = 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&h=600&fit=crop';
foreach ($detailFallbacks as $key => $url) {
    if (str_contains($categoryKeyword, $key)) { $detailFallback = $url; break; }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1 style="flex:1;font-size:15px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;"><?= e($item['title']) ?></h1>
    <div class="right-actions">
        <i class="fas fa-heart" style="color:var(--warm);"></i>
        <i class="fas fa-share-alt"></i>
    </div>
</div>

<!-- Main image -->
<div style="position:relative;background:linear-gradient(135deg,#f8f9fc,#eef0f5);">
    <img src="<?= getImageUrl($item['image'], 'classifieds') ?>"
         alt="<?= e($item['title']) ?>"
         class="detail-image"
         onerror="this.onerror=null;this.src='<?= $detailFallback ?>'">
    <?php if ($item['category_name']): ?>
    <div style="position:absolute;top:14px;left:14px;background:var(--primary);color:#fff;font-size:11px;font-weight:700;padding:5px 12px;border-radius:50px;box-shadow:0 2px 8px rgba(108,92,231,.35);">
        <?= e($item['category_name']) ?>
    </div>
    <?php endif; ?>
</div>

<div class="detail-body">
    <h1><?= e($item['title']) ?></h1>
    <p class="description"><?= nl2br(e($item['description'])) ?></p>

    <div class="detail-price-row">
        <div class="price-badge" style="font-size:16px;padding:8px 20px;"><?= e($item['currency']) ?> <?= number_format($item['price']) ?></div>
        <?php if ($item['location']): ?>
        <div class="location-text"><i class="fas fa-map-marker-alt"></i> <?= e($item['location']) ?></div>
        <?php endif; ?>
    </div>

    <?php if ($item['age'] || $item['model'] || $item['warranty'] || $item['color']): ?>
    <div class="detail-specs">
        <?php if ($item['age']): ?>
        <div class="detail-spec">
            <div class="label">Age</div>
            <div class="value"><?= e($item['age']) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($item['model']): ?>
        <div class="detail-spec">
            <div class="label">Model</div>
            <div class="value"><?= e($item['model']) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($item['warranty']): ?>
        <div class="detail-spec">
            <div class="label">Warranty</div>
            <div class="value"><?= e($item['warranty']) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($item['color']): ?>
        <div class="detail-spec">
            <div class="label">Color</div>
            <div class="value"><?= e($item['color']) ?></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="detail-table">
        <h3>Item Details</h3>
        <?php foreach ([
            'Storage Capacity' => $item['storage']        ?? '',
            'Memory'           => $item['memory']          ?? '',
            'Condition'        => $item['condition_status'] ?? '',
            'Version'          => $item['version']         ?? '',
            'Battery Health'   => $item['battery_health']  ?? '',
            'Accompaniments'   => $item['accompaniments']  ?? '',
            'Carrier Lock'     => $item['carrier_lock']    ?? '',
        ] as $label => $value): ?>
            <?php if ($value): ?>
            <div class="detail-row">
                <span class="dt-label"><?= $label ?></span>
                <span class="dt-value"><?= e($value) ?></span>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="detail-date">
        <i class="far fa-calendar-alt" style="margin-right:6px;color:var(--primary);"></i>
        Posted on: <?= date('dS F Y', strtotime($item['created_at'])) ?>
    </div>

    <!-- Contact Actions -->
    <div style="display:flex;gap:10px;margin-top:16px;">
        <a href="https://wa.me/" style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;background:linear-gradient(135deg,#00B894,#00CEC9);color:#fff;border-radius:14px;font-size:14px;font-weight:700;box-shadow:0 4px 14px rgba(0,184,148,.3);">
            <i class="fab fa-whatsapp" style="font-size:18px;"></i> WhatsApp
        </a>
        <a href="tel:" style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;background:var(--white);color:var(--primary);border:2px solid var(--primary);border-radius:14px;font-size:14px;font-weight:700;">
            <i class="fas fa-phone" style="font-size:16px;"></i> Call
        </a>
    </div>

    <!-- Safety Tips -->
    <div style="margin-top:16px;padding:14px 16px;background:linear-gradient(135deg,rgba(253,203,110,.15),rgba(243,156,18,.1));border-radius:14px;border:1px solid rgba(243,156,18,.2);">
        <div style="font-size:13px;font-weight:700;color:#B7770D;margin-bottom:6px;">
            <i class="fas fa-shield-alt" style="margin-right:6px;"></i>Safety Tips
        </div>
        <ul style="list-style:none;padding:0;font-size:12px;color:var(--text-secondary);line-height:1.8;">
            <li>✓ Meet in a public, well-lit location</li>
            <li>✓ Inspect item carefully before payment</li>
            <li>✓ Never pay in advance via transfer</li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
