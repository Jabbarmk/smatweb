<?php
require_once __DIR__ . '/../includes/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: offers.php'); exit; }

$db = getDB();

$stmt = $db->prepare("
    SELECT o.*, b.name AS business_name, b.logo AS business_logo, b.image AS business_image,
           b.address AS business_address, b.phone AS business_phone, b.whatsapp AS business_whatsapp,
           b.emirate AS business_emirate,
           bc.name AS category_name
    FROM offers o
    JOIN businesses b ON o.business_id = b.id
    LEFT JOIN business_categories bc ON o.category_id = bc.id
    WHERE o.id = ? AND o.is_active = 1
");
$stmt->execute([$id]);
$offer = $stmt->fetch();
if (!$offer) { header('Location: offers.php'); exit; }

// Reviews
$reviews = $db->prepare("SELECT * FROM offer_reviews WHERE offer_id = ? ORDER BY created_at DESC");
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();

$avgRating = count($reviews) > 0
    ? array_sum(array_column($reviews, 'rating')) / count($reviews)
    : (float)$offer['rating'];

$pageTitle = $offer['title'];

$offerFallback = 'https://images.unsplash.com/photo-1513151233558-d860c5398176?w=800&h=500&fit=crop';
$logoFallback  = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=80&h=80&fit=crop';

$logoSrc = !empty($offer['business_logo']) ? getImageUrl($offer['business_logo'], 'businesses')
         : (!empty($offer['business_image']) ? getImageUrl($offer['business_image'], 'businesses') : $logoFallback);
$imgSrc  = !empty($offer['image']) ? getImageUrl($offer['image'], 'offers') : $offerFallback;

$waLink = !empty($offer['business_whatsapp'])
    ? 'https://wa.me/' . preg_replace('/\D/', '', $offer['business_whatsapp'])
      . '?text=' . rawurlencode('Hi, I want to order: ' . $offer['title'] . ' (' . $offer['currency'] . ' ' . number_format((float)$offer['price'], 0) . ')')
    : null;

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="<?= SITE_URL ?>/pages/offers.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1 style="font-size:15px;"><?= e(mb_strimwidth($offer['title'], 0, 30, '…')) ?></h1>
    <div class="right-actions"></div>
</div>

<!-- Hero Image -->
<div style="position:relative;margin:0 16px 16px;border-radius:20px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,0.12);">
    <img src="<?= e($imgSrc) ?>" alt="<?= e($offer['title']) ?>"
         onerror="this.src='<?= $offerFallback ?>'"
         style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block;">
    <?php if ($offer['discount_percent']): ?>
    <div style="position:absolute;top:12px;left:12px;background:#FF6B6B;color:#fff;font-size:12px;font-weight:800;padding:5px 12px;border-radius:999px;">
        -<?= (int)$offer['discount_percent'] ?>% OFF
    </div>
    <?php endif; ?>
    <div style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.55);border-radius:20px;padding:5px 10px;display:flex;align-items:center;gap:4px;">
        <i class="fas fa-star" style="color:#FDCB6E;font-size:11px;"></i>
        <span style="color:#fff;font-size:12px;font-weight:700;"><?= number_format($avgRating, 1) ?></span>
    </div>
</div>

<!-- Business Row -->
<a href="<?= SITE_URL ?>/pages/business_detail.php?id=<?= $offer['business_id'] ?>"
   style="display:flex;align-items:center;gap:12px;margin:0 16px 16px;background:#fff;padding:14px;border-radius:16px;text-decoration:none;color:inherit;box-shadow:0 2px 8px rgba(13,27,42,0.05);">
    <img src="<?= e($logoSrc) ?>" alt="<?= e($offer['business_name']) ?>"
         onerror="this.src='<?= $logoFallback ?>'"
         style="width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0;">
    <div style="flex:1;min-width:0;">
        <div style="font-size:15px;font-weight:800;color:#1A1A2E;"><?= e($offer['business_name']) ?></div>
        <div style="font-size:12px;color:#636E8A;">
            <?= e($offer['emirate'] ?: $offer['business_emirate'] ?: '') ?>
            <?php if ($offer['category_name']): ?> · <?= e($offer['category_name']) ?><?php endif; ?>
        </div>
    </div>
    <i class="fas fa-chevron-right" style="color:#9BA4B5;"></i>
</a>

<!-- Title + Price Card -->
<div style="background:#fff;margin:0 16px 16px;border-radius:20px;padding:18px 16px;box-shadow:0 2px 8px rgba(13,27,42,0.05);">
    <h2 style="font-size:20px;font-weight:800;color:#1A1A2E;margin:0 0 10px;"><?= e($offer['title']) ?></h2>
    <?php if ($offer['description']): ?>
    <p style="font-size:14px;color:#636E8A;line-height:1.6;margin:0 0 16px;"><?= e($offer['description']) ?></p>
    <?php endif; ?>
    <div style="display:flex;align-items:baseline;gap:10px;margin-bottom:16px;">
        <span style="font-size:28px;font-weight:800;color:#6C5CE7;">
            <?= e($offer['currency']) ?> <?= number_format((float)$offer['price'], 0) ?>
        </span>
        <?php if ($offer['original_price'] && $offer['original_price'] > $offer['price']): ?>
        <span style="font-size:15px;color:#9BA4B5;text-decoration:line-through;">
            <?= e($offer['currency']) ?> <?= number_format((float)$offer['original_price'], 0) ?>
        </span>
        <?php endif; ?>
    </div>
    <!-- Order buttons -->
    <div style="display:flex;gap:10px;">
        <?php if ($waLink): ?>
        <a href="<?= e($waLink) ?>" target="_blank"
           style="flex:1;padding:13px 0;background:#25D366;color:#fff;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
            <i class="fab fa-whatsapp"></i> Order on WhatsApp
        </a>
        <?php endif; ?>
        <?php if ($offer['business_phone']): ?>
        <a href="tel:<?= e($offer['business_phone']) ?>"
           style="flex:1;padding:13px 0;background:#6C5CE7;color:#fff;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
            <i class="fas fa-phone"></i> Call Now
        </a>
        <?php endif; ?>
        <?php if (!$waLink && !$offer['business_phone']): ?>
        <a href="<?= SITE_URL ?>/pages/business_detail.php?id=<?= $offer['business_id'] ?>"
           style="flex:1;padding:13px 0;background:#6C5CE7;color:#fff;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
            <i class="fas fa-store"></i> View Business
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Full Details -->
<?php if ($offer['details']): ?>
<div style="background:#fff;margin:0 16px 16px;border-radius:20px;padding:18px 16px;box-shadow:0 2px 8px rgba(13,27,42,0.05);">
    <h3 style="font-size:15px;font-weight:800;margin:0 0 14px;color:#1A1A2E;">Details</h3>
    <p style="font-size:14px;color:#636E8A;line-height:1.7;white-space:pre-wrap;"><?= e($offer['details']) ?></p>
</div>
<?php endif; ?>

<!-- Info -->
<div style="background:#fff;margin:0 16px 16px;border-radius:20px;padding:18px 16px;box-shadow:0 2px 8px rgba(13,27,42,0.05);">
    <h3 style="font-size:15px;font-weight:800;margin:0 0 14px;color:#1A1A2E;">Info</h3>
    <?php
    $infoRows = [
        ['fa-map-marker-alt', 'Location',     $offer['business_address']],
        ['fa-calendar-alt',   'Valid from',   $offer['valid_from'] ? date('d M Y', strtotime($offer['valid_from'])) : null],
        ['fa-calendar-times', 'Valid until',  $offer['valid_to']   ? date('d M Y', strtotime($offer['valid_to']))   : null],
        ['fa-phone',          'Phone',        $offer['business_phone']],
    ];
    foreach ($infoRows as [$icon, $label, $val]):
        if (!$val) continue;
    ?>
    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
        <div style="width:32px;height:32px;background:#f0f2f8;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6C5CE7;font-size:13px;flex-shrink:0;">
            <i class="fas <?= $icon ?>"></i>
        </div>
        <div>
            <div style="font-size:11px;color:#9BA4B5;font-weight:600;"><?= $label ?></div>
            <div style="font-size:14px;color:#1A1A2E;font-weight:500;"><?= e($val) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Reviews -->
<div style="background:#fff;margin:0 16px 16px;border-radius:20px;padding:18px 16px;box-shadow:0 2px 8px rgba(13,27,42,0.05);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <h3 style="font-size:15px;font-weight:800;margin:0;color:#1A1A2E;">Reviews</h3>
        <div style="display:flex;align-items:center;gap:6px;background:#FFF5E7;padding:4px 10px;border-radius:10px;">
            <i class="fas fa-star" style="color:#FDCB6E;font-size:12px;"></i>
            <span style="font-size:13px;font-weight:700;"><?= number_format($avgRating, 1) ?></span>
            <span style="font-size:11px;color:#9BA4B5;">(<?= count($reviews) ?>)</span>
        </div>
    </div>

    <?php if (empty($reviews)): ?>
    <p style="font-size:13px;color:#9BA4B5;text-align:center;padding:20px 0;">No reviews yet. Be the first!</p>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:14px;">
        <?php foreach ($reviews as $r):
            $initial = strtoupper(substr($r['user_name'] ?? 'U', 0, 1));
            $colors  = ['#6C5CE7','#00B894','#FD79A8','#FDCB6E','#0984E3','#E17055'];
            $bg      = $colors[ord($initial) % count($colors)];
        ?>
        <div style="display:flex;gap:10px;padding-bottom:14px;border-bottom:1px solid #F0F2F8;">
            <div style="width:36px;height:36px;border-radius:50%;background:<?= $bg ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:800;flex-shrink:0;">
                <?= e($initial) ?>
            </div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:13px;font-weight:700;color:#1A1A2E;"><?= e($r['user_name'] ?? 'Anonymous') ?></div>
                    <div style="display:flex;align-items:center;gap:3px;">
                        <i class="fas fa-star" style="color:#FDCB6E;font-size:11px;"></i>
                        <span style="font-size:12px;font-weight:700;"><?= number_format((float)$r['rating'], 1) ?></span>
                    </div>
                </div>
                <?php if ($r['comment']): ?>
                <p style="font-size:13px;color:#636E8A;line-height:1.5;margin:4px 0 0;"><?= e($r['comment']) ?></p>
                <?php endif; ?>
                <div style="font-size:11px;color:#9BA4B5;margin-top:4px;"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<div style="height:24px;"></div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
