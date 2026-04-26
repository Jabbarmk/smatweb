<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'SMARTUAE - Home';
$currentPage = 'home';

$db = getDB();

$sliders      = $db->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
$mainCats     = $db->query("SELECT * FROM main_categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
$popCats      = $db->query("SELECT * FROM popular_categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
$sections     = $db->query("SELECT * FROM classified_sections WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

$totalBiz         = $db->query("SELECT COUNT(*) FROM businesses WHERE is_active = 1")->fetchColumn();
$totalJobs        = $db->query("SELECT COUNT(*) FROM jobs WHERE is_active = 1")->fetchColumn();
$totalClassifieds = $db->query("SELECT COUNT(*) FROM classifieds WHERE is_active = 1")->fetchColumn();

/* Fallback images used when DB has no uploaded image */
$sliderFallbacks = [
    ['img' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&fit=crop',
     'title' => 'Discover Dubai', 'sub' => 'Find the best businesses near you', 'btn' => 'Explore'],
    ['img' => 'https://images.unsplash.com/photo-1547721064-da6cfb341d50?w=800&fit=crop',
     'title' => 'Dubai Marina', 'sub' => 'Premium classifieds & real estate', 'btn' => 'Browse'],
    ['img' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?w=800&fit=crop',
     'title' => 'Career in UAE', 'sub' => 'Thousands of jobs waiting for you', 'btn' => 'Find Jobs'],
];

include __DIR__ . '/includes/header.php';
?>

<!-- Image Slider -->
<div class="slider-container">
    <div class="slider-track">
        <?php if (!empty($sliders)): ?>
            <?php foreach ($sliders as $slide): ?>
            <div class="slide">
                <img src="<?= getImageUrl($slide['image'], 'slides') ?>"
                     alt="<?= e($slide['title']) ?>"
                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&fit=crop'">
                <div class="slide-content">
                    <h2><?= e($slide['title']) ?></h2>
                    <p><?= e($slide['subtitle']) ?></p>
                    <?php if ($slide['button_text']): ?>
                    <a href="<?= e($slide['button_link']) ?>" class="slide-btn"><?= e($slide['button_text']) ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($sliderFallbacks as $slide): ?>
            <div class="slide">
                <img src="<?= $slide['img'] ?>" alt="<?= $slide['title'] ?>">
                <div class="slide-content">
                    <h2><?= $slide['title'] ?></h2>
                    <p><?= $slide['sub'] ?></p>
                    <a href="<?= SITE_URL ?>/pages/categories.php" class="slide-btn"><?= $slide['btn'] ?></a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="slider-dots">
        <?php $count = !empty($sliders) ? count($sliders) : count($sliderFallbacks); ?>
        <?php for ($i = 0; $i < $count; $i++): ?>
        <div class="dot <?= $i === 0 ? 'active' : '' ?>"></div>
        <?php endfor; ?>
    </div>
</div>

<!-- Main Category Icons -->
<div class="section-header">
    <h2>Categories</h2>
    <a href="<?= SITE_URL ?>/pages/categories.php">See all</a>
</div>
<div class="category-icons">
    <?php foreach ($mainCats as $cat): ?>
    <a href="<?= SITE_URL ?>/pages/categories.php?cat=<?= $cat['id'] ?>" class="cat-icon-item">
        <div class="icon"><?= $cat['icon'] ?></div>
        <span><?= e($cat['name']) ?></span>
    </a>
    <?php endforeach; ?>
    <a href="<?= SITE_URL ?>/pages/categories.php" class="cat-icon-item">
        <div class="icon">📋</div>
        <span>All</span>
    </a>
</div>

<!-- Feature Grid -->
<div class="section-header">
    <h2>Our Services</h2>
</div>
<div class="feature-grid">
    <a href="<?= SITE_URL ?>/pages/categories.php" class="feature-card purple">
        <div class="feature-icon"><i class="fas fa-store"></i></div>
        <h4>Business Directory</h4>
        <p>Find local shops and services</p>
    </a>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="feature-card teal">
        <div class="feature-icon"><i class="fas fa-tags"></i></div>
        <h4>Classifieds</h4>
        <p>Buy and sell used items</p>
    </a>
    <a href="<?= SITE_URL ?>/pages/jobs.php" class="feature-card pink">
        <div class="feature-icon"><i class="fas fa-briefcase"></i></div>
        <h4>Job Portal</h4>
        <p>Find your dream career</p>
    </a>
    <a href="<?= SITE_URL ?>/pages/profile.php" class="feature-card amber">
        <div class="feature-icon"><i class="fas fa-file-alt"></i></div>
        <h4>Smart CV</h4>
        <p>Build your professional profile</p>
    </a>
</div>

<!-- Popular Categories -->
<div class="section-header">
    <h2>Popular Categories</h2>
</div>
<div class="popular-cats">
    <?php if (!empty($popCats)): ?>
        <?php
        $catFallbacks = [
            'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400&h=267&fit=crop',
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=267&fit=crop',
            'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=400&h=267&fit=crop',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=267&fit=crop',
        ];
        ?>
        <?php foreach ($popCats as $idx => $pc): ?>
        <a href="<?= SITE_URL ?>/pages/businesses.php?cat=<?= $pc['id'] ?>" class="pop-cat-card">
            <img src="<?= getImageUrl($pc['image'], 'categories') ?>"
                 alt="<?= e($pc['name']) ?>"
                 onerror="this.onerror=null;this.src='<?= $catFallbacks[$idx % count($catFallbacks)] ?>'">
            <div class="label"><?= e($pc['name']) ?></div>
        </a>
        <?php endforeach; ?>
    <?php else: ?>
        <a href="<?= SITE_URL ?>/pages/businesses.php" class="pop-cat-card">
            <img src="https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400&h=267&fit=crop" alt="City Tours">
            <div class="label">City Tours</div>
        </a>
        <a href="<?= SITE_URL ?>/pages/businesses.php" class="pop-cat-card">
            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=267&fit=crop" alt="Restaurants">
            <div class="label">Restaurants</div>
        </a>
        <a href="<?= SITE_URL ?>/pages/businesses.php" class="pop-cat-card">
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=267&fit=crop" alt="Real Estate">
            <div class="label">Real Estate</div>
        </a>
        <a href="<?= SITE_URL ?>/pages/businesses.php" class="pop-cat-card">
            <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=400&h=267&fit=crop" alt="Car Rental">
            <div class="label">Car Rental</div>
        </a>
    <?php endif; ?>
</div>

<!-- Promo Card -->
<div class="promo-card">
    <div class="promo-text">
        <h3>Post Your Ad Free!</h3>
        <p>Reach thousands of buyers in UAE instantly</p>
    </div>
    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=160&h=160&fit=crop"
         alt="Post Ad" class="promo-img">
</div>

<!-- Hero Banner -->
<div class="hero-banner">
    <h2>Discover the Best in UAE</h2>
    <p>Businesses · Jobs · Classifieds – all in one place</p>
    <a href="<?= SITE_URL ?>/pages/categories.php" class="hero-btn">Explore Now</a>
    <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=240&h=160&fit=crop"
         alt="Dubai" class="hero-img"
         style="border-radius:14px;object-fit:cover;width:130px;height:100px;">
</div>

<!-- Classified Sections from DB -->
<?php
$clsFallbacks = [
    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=300&h=300&fit=crop',
];
foreach ($sections as $section):
    $items = $db->prepare("SELECT * FROM classifieds WHERE section_id = ? AND is_active = 1 ORDER BY created_at DESC LIMIT 6");
    $items->execute([$section['id']]);
    $items = $items->fetchAll();
    if (empty($items)) continue;
?>
<div class="section-header">
    <h2><?= e($section['name']) ?></h2>
    <a href="<?= SITE_URL ?>/pages/classified_list.php?section=<?= $section['id'] ?>">View all</a>
</div>
<div class="classified-row">
    <?php foreach ($items as $idx => $item): ?>
    <a href="<?= SITE_URL ?>/pages/classified_detail.php?id=<?= $item['id'] ?>" class="classified-card">
        <img src="<?= getImageUrl($item['image'], 'classifieds') ?>"
             alt="<?= e($item['title']) ?>"
             class="card-img"
             onerror="this.onerror=null;this.src='<?= $clsFallbacks[$idx % count($clsFallbacks)] ?>'">
        <div class="card-body">
            <div class="price"><?= e($item['currency']) ?> <?= number_format($item['price']) ?> <small>/month</small></div>
            <div class="card-title"><?= e($item['title']) ?></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>

<!-- Explore UAE Gallery -->
<div class="section-header">
    <h2>Explore UAE</h2>
</div>
<div class="gallery-row">
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&h=300&fit=crop" alt="Dubai Skyline">
        <div class="gallery-overlay">Dubai Skyline</div>
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1518684079-3c830dcef090?w=400&h=300&fit=crop" alt="Burj Khalifa">
        <div class="gallery-overlay">Burj Khalifa</div>
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1580674684081-7617fbf3d745?w=400&h=300&fit=crop" alt="Abu Dhabi">
        <div class="gallery-overlay">Abu Dhabi</div>
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1547721064-da6cfb341d50?w=400&h=300&fit=crop" alt="Dubai Marina">
        <div class="gallery-overlay">Dubai Marina</div>
    </div>
    <div class="gallery-item">
        <img src="https://images.unsplash.com/photo-1483683804023-6ccdb62f86ef?w=400&h=300&fit=crop" alt="Palm Jumeirah">
        <div class="gallery-overlay">Palm Jumeirah</div>
    </div>
</div>

<!-- Top Businesses Teaser -->
<div class="section-header">
    <h2>Top Businesses</h2>
    <a href="<?= SITE_URL ?>/pages/businesses.php">See all</a>
</div>
<div class="gallery-row">
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=320&h=240&fit=crop" alt="Fine Dining">
        <div class="gallery-overlay">Fine Dining</div>
    </div>
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=320&h=240&fit=crop" alt="Luxury Hotels">
        <div class="gallery-overlay">Luxury Hotels</div>
    </div>
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1562243061-204550d8a2c9?w=320&h=240&fit=crop" alt="Salons & Spa">
        <div class="gallery-overlay">Salons & Spa</div>
    </div>
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=320&h=240&fit=crop" alt="Fitness">
        <div class="gallery-overlay">Fitness</div>
    </div>
</div>

<!-- Stats Banner -->
<div class="stats-banner">
    <div class="stat-item">
        <div class="stat-num"><?= $totalBiz ?: '50' ?>+</div>
        <div class="stat-label">Businesses</div>
    </div>
    <div class="stat-item">
        <div class="stat-num"><?= $totalJobs ?: '30' ?>+</div>
        <div class="stat-label">Active Jobs</div>
    </div>
    <div class="stat-item">
        <div class="stat-num"><?= $totalClassifieds ?: '100' ?>+</div>
        <div class="stat-label">Listings</div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
