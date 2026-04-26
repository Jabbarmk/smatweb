<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Classified Listings';
$currentPage = 'classifieds';

$db = getDB();

$categoryId = $_GET['category'] ?? null;
$sectionId  = $_GET['section']  ?? null;
$search     = $_GET['search']   ?? '';

$where  = ["c.is_active = 1"];
$params = [];

if ($categoryId) { $where[] = "c.category_id = ?"; $params[] = $categoryId; }
if ($sectionId)  { $where[] = "c.section_id = ?";  $params[] = $sectionId; }
if ($search) {
    $where[] = "(c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%";
}

$whereStr = implode(' AND ', $where);
$stmt = $db->prepare("SELECT c.*, cc.name as category_name FROM classifieds c LEFT JOIN classified_categories cc ON c.category_id = cc.id WHERE $whereStr ORDER BY c.created_at DESC");
$stmt->execute($params);
$items = $stmt->fetchAll();

$catName = 'Classifieds';
if ($categoryId) {
    $catStmt = $db->prepare("SELECT name FROM classified_categories WHERE id = ?");
    $catStmt->execute([$categoryId]);
    $cat = $catStmt->fetch();
    if ($cat) $catName = $cat['name'];
}

/* Rotating fallback images for list items */
$listFallbacks = [
    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=220&h=200&fit=crop',
    'https://images.unsplash.com/photo-1593359677879-a4bb92f829e1?w=220&h=200&fit=crop',
];

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1><?= e($catName) ?></h1>
</div>

<!-- Category banner image -->
<div style="position:relative;margin:16px;border-radius:20px;overflow:hidden;height:110px;box-shadow:0 4px 20px rgba(0,0,0,.1);">
    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=220&fit=crop"
         alt="<?= e($catName) ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(13,27,42,.65),rgba(108,92,231,.55));display:flex;align-items:center;padding:16px 20px;gap:12px;">
        <i class="fas fa-tags" style="color:#fff;font-size:28px;opacity:.9;"></i>
        <div>
            <div style="color:#fff;font-size:18px;font-weight:800;"><?= e($catName) ?></div>
            <div style="color:rgba(255,255,255,.8);font-size:12px;margin-top:2px;"><?= count($items) ?: 'Many' ?> items available</div>
        </div>
    </div>
</div>

<div class="page-search">
    <i class="fas fa-search search-icon"></i>
    <input type="text" placeholder="Search classifieds..." value="<?= e($search) ?>"
           onkeypress="if(event.key==='Enter') { let url = new URL(window.location); url.searchParams.set('search', this.value); window.location = url; }">
</div>

<div class="filter-tags">
    <span class="filter-tag active">All</span>
    <span class="filter-tag">Select Brand</span>
    <span class="filter-tag">Warranty</span>
    <span class="filter-tag">Price</span>
    <span class="filter-tag">Location</span>
</div>

<p style="padding:0 16px 8px;font-size:14px;font-weight:600;color:var(--text-secondary);">
    <?= e($catName) ?> <span style="color:var(--primary);font-weight:700;">(<?= count($items) ?> Results)</span>
</p>

<div class="classified-list">
    <?php foreach ($items as $idx => $item): ?>
    <a href="<?= SITE_URL ?>/pages/classified_detail.php?id=<?= $item['id'] ?>" class="classified-list-item">
        <img src="<?= getImageUrl($item['image'], 'classifieds') ?>"
             alt="<?= e($item['title']) ?>"
             class="item-img"
             onerror="this.onerror=null;this.src='<?= $listFallbacks[$idx % count($listFallbacks)] ?>'">
        <div class="item-info">
            <h3><?= e($item['title']) ?></h3>
            <p class="desc"><?= e(substr($item['description'] ?? '', 0, 65)) ?>...</p>
            <div class="specs">
                <?php if ($item['age']): ?><span class="spec-tag">Age: <?= e($item['age']) ?></span><?php endif; ?>
                <?php if ($item['model']): ?><span class="spec-tag"><?= e($item['model']) ?></span><?php endif; ?>
                <?php if ($item['warranty']): ?><span class="spec-tag"><?= e($item['warranty']) ?> warranty</span><?php endif; ?>
                <?php if ($item['color']): ?><span class="spec-tag"><?= e($item['color']) ?></span><?php endif; ?>
            </div>
            <div class="price-badge"><?= e($item['currency']) ?> <?= number_format($item['price']) ?></div>
            <?php if ($item['location']): ?>
            <div class="location-text"><i class="fas fa-map-marker-alt"></i> <?= e($item['location']) ?></div>
            <?php endif; ?>
        </div>
    </a>
    <?php endforeach; ?>

    <?php if (empty($items)): ?>
    <!-- Sample listings when DB empty -->
    <?php
    $demoItems = [
        ['title'=>'iPhone 15 Pro Max 256GB','desc'=>'Space Black, 95% battery health, original box included.','price'=>'AED 4,200','loc'=>'Dubai Marina','img'=>'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=220&h=200&fit=crop','specs'=>['Age: 6mo','iOS 17','Warranty: 6mo']],
        ['title'=>'Samsung 65" QLED TV','desc'=>'4K HDR, Smart TV, excellent condition with remote and stand.','price'=>'AED 3,800','loc'=>'JBR, Dubai','img'=>'https://images.unsplash.com/photo-1593359677879-a4bb92f829e1?w=220&h=200&fit=crop','specs'=>['65 inch','2023 Model']],
        ['title'=>'MacBook Pro M3 14"','desc'=>'Space Gray, 16GB RAM, 512GB SSD. AppleCare+ until 2026.','price'=>'AED 8,500','loc'=>'Downtown Dubai','img'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=220&h=200&fit=crop','specs'=>['M3 chip','16GB','512GB']],
        ['title'=>'Luxury Sofa Set 7-Seater','desc'=>'Italian leather, barely used. Selling due to relocation.','price'=>'AED 6,200','loc'=>'Mirdif, Dubai','img'=>'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=220&h=200&fit=crop','specs'=>['Italian Leather','Brown']],
        ['title'=>'BMW 5 Series 2022','desc'=>'530i, 8k km, full service history, fully loaded options.','price'=>'AED 168,000','loc'=>'Al Quoz, Dubai','img'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=220&h=200&fit=crop','specs'=>['2022','8,000km','Petrol']],
        ['title'=>'2BR Apartment for Rent','desc'=>'Fully furnished, sea view, all bills included.','price'=>'AED 7,500/mo','loc'=>'Dubai Marina','img'=>'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=220&h=200&fit=crop','specs'=>['2BR','Furnished','Sea View']],
    ];
    foreach ($demoItems as $d): ?>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="classified-list-item">
        <img src="<?= $d['img'] ?>" alt="<?= $d['title'] ?>" class="item-img">
        <div class="item-info">
            <h3><?= $d['title'] ?></h3>
            <p class="desc"><?= $d['desc'] ?></p>
            <div class="specs"><?php foreach ($d['specs'] as $sp): ?><span class="spec-tag"><?= $sp ?></span><?php endforeach; ?></div>
            <div class="price-badge"><?= $d['price'] ?></div>
            <div class="location-text"><i class="fas fa-map-marker-alt"></i> <?= $d['loc'] ?></div>
        </div>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
