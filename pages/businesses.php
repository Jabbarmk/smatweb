<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Business Listings';
$currentPage = 'home';

$db = getDB();
$catId = $_GET['cat'] ?? null;

$catName = 'All Businesses';
if ($catId) {
    $catStmt = $db->prepare("SELECT name FROM business_categories WHERE id = ?");
    $catStmt->execute([$catId]);
    $cat = $catStmt->fetch();
    if ($cat) $catName = $cat['name'];
}

$query = "SELECT b.*, bc.name as category_name FROM businesses b LEFT JOIN business_categories bc ON b.category_id = bc.id WHERE b.is_active = 1";
$params = [];
if ($catId) {
    $query .= " AND b.category_id = ?";
    $params[] = $catId;
}
$query .= " ORDER BY b.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$businesses = $stmt->fetchAll();

/* Unsplash photo fallbacks per business category keywords */
$bizImgFallbacks = [
    'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1562243061-204550d8a2c9?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=480&h=270&fit=crop',
    'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=480&h=270&fit=crop',
];

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1><?= e($catName) ?></h1>
    <div class="right-actions">
        <span style="font-size:12px;font-weight:600;">Help?</span>
        <i class="fab fa-whatsapp" style="color: #25D366; font-size: 24px;"></i>
    </div>
</div>

<!-- Business Hero Banner -->
<div style="position:relative;margin:16px;border-radius:20px;overflow:hidden;height:160px;box-shadow:0 8px 30px rgba(0,0,0,0.15);">
    <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&h=320&fit=crop"
         alt="UAE Business" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(108,92,231,0.75),rgba(0,206,201,0.55));display:flex;flex-direction:column;justify-content:center;padding:20px;">
        <div style="color:#fff;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;opacity:.8;margin-bottom:6px;">Discover</div>
        <div style="color:#fff;font-size:22px;font-weight:800;line-height:1.2;"><?= e($catName) ?></div>
        <div style="color:rgba(255,255,255,.85);font-size:13px;margin-top:6px;">Top-rated businesses near you</div>
    </div>
</div>

<div class="tab-nav">
    <a href="#" class="active">Popular</a>
    <a href="#">Near Me</a>
</div>

<div class="biz-list">
    <?php foreach ($businesses as $idx => $biz): ?>
    <div class="biz-card">
        <img src="<?= getImageUrl($biz['image'], 'businesses') ?>"
             alt="<?= e($biz['name']) ?>"
             class="biz-img"
             style="cursor:pointer;"
             onclick="location.href='<?= SITE_URL ?>/pages/business_detail.php?id=<?= $biz['id'] ?>'"
             onerror="this.onerror=null;this.src='<?= $bizImgFallbacks[$idx % count($bizImgFallbacks)] ?>'">
        <div class="biz-body">
            <div class="biz-rating"><i class="fas fa-star"></i> <?= number_format((float)$biz['rating'], 1) ?></div>
            <h3><?= e($biz['name']) ?></h3>
            <div class="biz-type"><?= e($biz['description']) ?></div>
            <div class="biz-distance"><?= e($biz['distance']) ?></div>
            <div class="biz-address"><i class="fas fa-map-marker-alt"></i> <?= e($biz['address']) ?></div>
            <div class="biz-actions">
                <button class="btn"><i class="fas fa-share-alt"></i> Share</button>
                <a href="tel:<?= e($biz['phone']) ?>" class="btn btn-call"><i class="fas fa-phone"></i> Call</a>
                <a href="<?= SITE_URL ?>/pages/business_detail.php?id=<?= $biz['id'] ?>" class="btn-details"><i class="fas fa-eye"></i> View Details</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($businesses)): ?>
    <!-- Show sample business cards when DB is empty -->
    <?php
    $sampleBiz = [
        ['name'=>'Al Fanar Restaurant','type'=>'Arabic Cuisine','rating'=>'4.8','addr'=>'Dubai Creek, Deira','img'=>'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=480&h=270&fit=crop'],
        ['name'=>'Jumeirah Grand Hotel','type'=>'5-Star Luxury Hotel','rating'=>'4.9','addr'=>'Jumeirah Beach Road','img'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=480&h=270&fit=crop'],
        ['name'=>'Dubai Fitness Studio','type'=>'Gym & Wellness','rating'=>'4.6','addr'=>'Downtown Dubai','img'=>'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=480&h=270&fit=crop'],
        ['name'=>'Marina Bay Salon','type'=>'Beauty & Spa','rating'=>'4.7','addr'=>'Dubai Marina Walk','img'=>'https://images.unsplash.com/photo-1562243061-204550d8a2c9?w=480&h=270&fit=crop'],
    ];
    foreach ($sampleBiz as $s): ?>
    <div class="biz-card">
        <img src="<?= $s['img'] ?>" alt="<?= $s['name'] ?>" class="biz-img">
        <div class="biz-body">
            <div class="biz-rating"><i class="fas fa-star"></i> <?= $s['rating'] ?></div>
            <h3><?= $s['name'] ?></h3>
            <div class="biz-type"><?= $s['type'] ?></div>
            <div class="biz-address"><i class="fas fa-map-marker-alt"></i> <?= $s['addr'] ?></div>
            <div class="biz-actions">
                <button class="btn"><i class="fas fa-share-alt"></i> Share</button>
                <button class="btn"><i class="fab fa-whatsapp"></i> WhatsApp</button>
                <a href="#" class="btn btn-call"><i class="fas fa-phone"></i> Call</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="page-search" style="margin-bottom: 80px;">
    <i class="fas fa-search search-icon"></i>
    <input type="text" placeholder="Search Company">
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
