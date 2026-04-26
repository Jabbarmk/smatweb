<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Business Categories';
$currentPage = 'home';

$db = getDB();

$search = $_GET['search'] ?? '';

// Get categories grouped
$query = "SELECT * FROM business_categories WHERE is_active = 1";
$params = [];
if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}
$query .= " ORDER BY sort_order";
$stmt = $db->prepare($query);
$stmt->execute($params);
$categories = $stmt->fetchAll();

// Group by group_name
$groups = [];
foreach ($categories as $cat) {
    $groups[$cat['group_name']][] = $cat;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <span class="logo-icon"><i class="fas fa-map-marker-alt"></i></span>
    <h1>BUSINESS CATEGORIES</h1>
</div>

<div class="page-search">
    <i class="fas fa-search search-icon"></i>
    <input type="text" placeholder="Search Business Categories" value="<?= e($search) ?>"
           onkeypress="if(event.key==='Enter') window.location.href='?search='+this.value">
    <button class="filter-btn"><i class="fas fa-sliders-h"></i></button>
</div>

<div class="filter-tags">
    <span class="filter-tag active">All</span>
    <span class="filter-tag">Colleges</span>
    <span class="filter-tag">Schools</span>
    <span class="filter-tag">Restaurants</span>
</div>

<?php foreach ($groups as $groupName => $cats): ?>
<div class="biz-cat-group">
    <h3><?= e($groupName) ?></h3>
    <div class="biz-cat-grid">
        <?php foreach ($cats as $cat): ?>
        <a href="<?= SITE_URL ?>/pages/businesses.php?cat=<?= $cat['id'] ?>" class="biz-cat-item">
            <div class="emoji"><?= $cat['icon'] ?></div>
            <span><?= e($cat['name']) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
