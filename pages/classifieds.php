<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Classifieds';
$currentPage = 'classifieds';

$db = getDB();

$clsCats  = $db->query("SELECT * FROM classified_categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
$sections = $db->query("SELECT * FROM classified_sections WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

$clsFallbacks = [
    'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=300&fit=crop',
    'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=300&h=300&fit=crop',
];

$offlineProjects = [
    ['id'=>1,'title'=>'Marina Heights Residences','caption'=>'Luxury Waterfront Living','location'=>'Dubai Marina, Dubai','type'=>'Apartment','status'=>'Off-Plan','tag'=>'Dubai','price'=>'From AED 1.2M','completion'=>'Q4 2026','developer'=>'Emaar Properties','img'=>'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&h=600&fit=crop'],
    ['id'=>2,'title'=>'Palm Skyline Villas','caption'=>'Exclusive Palm Island Retreat','location'=>'Palm Jumeirah, Dubai','type'=>'Villa','status'=>'Off-Plan','tag'=>'Dubai','price'=>'From AED 8.5M','completion'=>'Q2 2027','developer'=>'Nakheel','img'=>'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=400&h=600&fit=crop'],
    ['id'=>3,'title'=>'Yas Island Living','caption'=>'Urban Island Community','location'=>'Yas Island, Abu Dhabi','type'=>'Apartment','status'=>'Off-Plan','tag'=>'Abu Dhabi','price'=>'From AED 850K','completion'=>'Q1 2027','developer'=>'Aldar Properties','img'=>'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&h=600&fit=crop'],
    ['id'=>4,'title'=>'Downtown Business Hub','caption'=>'Premium Commercial Tower','location'=>'Downtown, Dubai','type'=>'Commercial','status'=>'Completed','tag'=>'Dubai','price'=>'From AED 2.8M','completion'=>'Completed','developer'=>'Meraas','img'=>'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=400&h=600&fit=crop'],
    ['id'=>5,'title'=>'Corniche Residences','caption'=>'Iconic Waterfront Tower','location'=>'Corniche, Abu Dhabi','type'=>'Apartment','status'=>'Off-Plan','tag'=>'Abu Dhabi','price'=>'From AED 1.5M','completion'=>'Q3 2026','developer'=>'Bloom Properties','img'=>'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=400&h=600&fit=crop'],
    ['id'=>6,'title'=>'Golf Course Villas','caption'=>'Serene Greens Community','location'=>'Dubai Hills, Dubai','type'=>'Villa','status'=>'Off-Plan','tag'=>'Dubai','price'=>'From AED 4.2M','completion'=>'Q4 2027','developer'=>'Emaar Properties','img'=>'https://images.unsplash.com/photo-1580674684081-7617fbf3d745?w=400&h=600&fit=crop'],
    ['id'=>7,'title'=>'City Walk Towers','caption'=>'Lifestyle & Retail Living','location'=>'City Walk, Dubai','type'=>'Apartment','status'=>'Completed','tag'=>'Dubai','price'=>'From AED 1.8M','completion'=>'Completed','developer'=>'Meraas','img'=>'https://images.unsplash.com/photo-1416331108676-a22ccb276e35?w=400&h=600&fit=crop'],
    ['id'=>8,'title'=>'Al Reem Residences','caption'=>'Abu Dhabi Island Retreat','location'=>'Al Reem Island, Abu Dhabi','type'=>'Apartment','status'=>'Off-Plan','tag'=>'Abu Dhabi','price'=>'From AED 700K','completion'=>'Q2 2026','developer'=>'Imkan Properties','img'=>'https://images.unsplash.com/photo-1476357471311-43c0db9fb2b4?w=400&h=600&fit=crop'],
    ['id'=>9,'title'=>'Business Bay Lofts','caption'=>'Urban Creative Spaces','location'=>'Business Bay, Dubai','type'=>'Commercial','status'=>'Off-Plan','tag'=>'Dubai','price'=>'From AED 1.1M','completion'=>'Q1 2028','developer'=>'Damac Properties','img'=>'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&h=600&fit=crop'],
    ['id'=>10,'title'=>'Saadiyat Cultural Villas','caption'=>'Art District Community','location'=>'Saadiyat Island, Abu Dhabi','type'=>'Villa','status'=>'Off-Plan','tag'=>'Abu Dhabi','price'=>'From AED 6.5M','completion'=>'Q3 2027','developer'=>'Aldar Properties','img'=>'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=400&h=600&fit=crop'],
];

$rePlayers = [
    ['id'=>1,'name'=>'Emaar Properties','caption'=>'UAE\'s #1 Developer','desc'=>'Premium residential & mixed-use developments','totalProjects'=>48,'location'=>'Dubai','type'=>'Developer','established'=>'1997','logo'=>'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=400&h=400&fit=crop'],
    ['id'=>2,'name'=>'Damac Properties','caption'=>'Luxury Real Estate','desc'=>'High-end luxury homes across the UAE','totalProjects'=>36,'location'=>'Dubai','type'=>'Developer','established'=>'2002','logo'=>'https://images.unsplash.com/photo-1549517045-bc93de199c10?w=400&h=400&fit=crop'],
    ['id'=>3,'name'=>'Aldar Properties','caption'=>'Abu Dhabi\'s Leading Developer','desc'=>'Iconic landmarks and communities in Abu Dhabi','totalProjects'=>42,'location'=>'Abu Dhabi','type'=>'Developer','established'=>'2004','logo'=>'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=400&h=400&fit=crop'],
    ['id'=>4,'name'=>'Nakheel','caption'=>'Creating Communities','desc'=>'Developers of Palm Jumeirah and major communities','totalProjects'=>55,'location'=>'Dubai','type'=>'Developer','established'=>'2000','logo'=>'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&h=400&fit=crop'],
    ['id'=>5,'name'=>'Meraas','caption'=>'Urban Destinations','desc'=>'City Walk, Bluewaters, and more iconic destinations','totalProjects'=>22,'location'=>'Dubai','type'=>'Developer','established'=>'2007','logo'=>'https://images.unsplash.com/photo-1416331108676-a22ccb276e35?w=400&h=400&fit=crop'],
    ['id'=>6,'name'=>'Sobha Realty','caption'=>'Self-Reliant Excellence','desc'=>'End-to-end luxury real estate development','totalProjects'=>18,'location'=>'Dubai','type'=>'Developer','established'=>'1976','logo'=>'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&h=400&fit=crop'],
    ['id'=>7,'name'=>'Bloom Properties','caption'=>'Abu Dhabi Specialists','desc'=>'Quality residential developments in Abu Dhabi','totalProjects'=>14,'location'=>'Abu Dhabi','type'=>'Developer','established'=>'2007','logo'=>'https://images.unsplash.com/photo-1476357471311-43c0db9fb2b4?w=400&h=400&fit=crop'],
    ['id'=>8,'name'=>'Betterhomes','caption'=>'UAE\'s Top Real Estate Agency','desc'=>'Leading residential and commercial brokerage','totalProjects'=>200,'location'=>'Dubai','type'=>'Agency','established'=>'1986','logo'=>'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=400&h=400&fit=crop'],
    ['id'=>9,'name'=>'CBRE Middle East','caption'=>'Global Property Services','desc'=>'Commercial real estate & investment advisory','totalProjects'=>150,'location'=>'Dubai','type'=>'Agency','established'=>'2008','logo'=>'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&h=400&fit=crop'],
    ['id'=>10,'name'=>'Imkan Properties','caption'=>'Mindful Destinations','desc'=>'Creating meaningful places across UAE','totalProjects'=>12,'location'=>'Abu Dhabi','type'=>'Developer','established'=>'2017','logo'=>'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&h=400&fit=crop'],
];

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <span class="logo-icon"><i class="fas fa-tags"></i></span>
    <h1>CLASSIFIEDS</h1>
</div>

<div class="page-search">
    <i class="fas fa-search search-icon"></i>
    <input type="text" placeholder="Search classifieds..."
           onkeypress="if(event.key==='Enter') window.location.href='classified_list.php?search='+this.value">
</div>

<!-- Offline Projects Section -->
<div style="margin-bottom:4px;">
    <div class="section-header">
        <h2>Offline Projects</h2>
        <a href="<?= SITE_URL ?>/pages/offline_projects.php">View all</a>
    </div>
    <div class="filter-tags" id="proj-filter-tags">
        <?php foreach (['All','Dubai','Abu Dhabi','Off-Plan','Completed','Villa','Apartment','Commercial'] as $kw): ?>
        <button class="filter-tag<?= $kw==='All'?' active':'' ?>" onclick="filterSlider('proj',this,'<?= $kw ?>')"><?= $kw ?></button>
        <?php endforeach; ?>
    </div>
    <div style="position:relative;">
        <div class="proj-slider-row" id="proj-slider">
            <?php foreach ($offlineProjects as $p): ?>
            <a href="<?= SITE_URL ?>/pages/offline_projects.php?id=<?= $p['id'] ?>"
               class="proj-card"
               data-tag="<?= e($p['tag']) ?>"
               data-type="<?= e($p['type']) ?>"
               data-status="<?= e($p['status']) ?>">
                <div class="proj-card-img-wrap">
                    <img src="<?= $p['img'] ?>" alt="<?= e($p['title']) ?>" class="proj-card-img">
                    <span class="proj-status-badge <?= $p['status']==='Completed'?'status-done':'status-plan' ?>"><?= e($p['status']) ?></span>
                </div>
                <div class="proj-card-body">
                    <div class="proj-card-title"><?= e($p['title']) ?></div>
                    <div class="proj-card-caption"><?= e($p['caption']) ?></div>
                    <div class="proj-card-location"><i class="fas fa-map-marker-alt"></i> <?= e($p['location']) ?></div>
                    <div class="proj-card-price"><?= e($p['price']) ?></div>
                    <div class="proj-card-meta"><?= e($p['developer']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <button class="proj-slider-btn proj-btn-left" id="proj-slider-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
        <button class="proj-slider-btn proj-btn-right" id="proj-slider-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<!-- Top Real Estate Players Section -->
<div style="margin-bottom:4px;">
    <div class="section-header">
        <h2>Top Real Estate Players</h2>
        <a href="<?= SITE_URL ?>/pages/real_estate_players.php">View all</a>
    </div>
    <div class="filter-tags" id="re-filter-tags">
        <?php foreach (['All','Developer','Agency','Dubai','Abu Dhabi','Residential','Commercial'] as $kw): ?>
        <button class="filter-tag<?= $kw==='All'?' active':'' ?>" onclick="filterSlider('re',this,'<?= $kw ?>')"><?= $kw ?></button>
        <?php endforeach; ?>
    </div>
    <div style="position:relative;">
        <div class="re-slider-row" id="re-slider">
            <?php foreach ($rePlayers as $p): ?>
            <a href="<?= SITE_URL ?>/pages/real_estate_players.php?id=<?= $p['id'] ?>"
               class="re-card"
               data-location="<?= e($p['location']) ?>"
               data-type="<?= e($p['type']) ?>">
                <div class="re-card-logo-wrap">
                    <img src="<?= $p['logo'] ?>" alt="<?= e($p['name']) ?>" class="re-card-logo">
                    <span class="re-type-badge <?= $p['type']==='Agency'?'re-type-agency':'re-type-dev' ?>"><?= e($p['type']) ?></span>
                </div>
                <div class="re-card-body">
                    <div class="re-card-name"><?= e($p['name']) ?></div>
                    <div class="re-card-caption"><?= e($p['caption']) ?></div>
                    <div class="re-card-projects">
                        <span class="re-card-projects-num"><?= $p['totalProjects'] ?></span>
                        <span class="re-card-projects-label">Total Projects</span>
                    </div>
                    <div class="re-card-location"><i class="fas fa-map-marker-alt"></i> <?= e($p['location']) ?> · Est. <?= e($p['established']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <button class="proj-slider-btn proj-btn-left" id="re-slider-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
        <button class="proj-slider-btn proj-btn-right" id="re-slider-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
    </div>
</div>

<!-- Classifieds Hero -->
<div style="position:relative;margin:16px;border-radius:20px;overflow:hidden;height:180px;box-shadow:0 8px 30px rgba(0,0,0,0.15);">
    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=360&fit=crop"
         alt="Classifieds UAE" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,184,148,.80),rgba(0,206,201,.65));display:flex;flex-direction:column;justify-content:center;padding:24px;">
        <div style="color:rgba(255,255,255,.8);font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">Marketplace</div>
        <div style="color:#fff;font-size:22px;font-weight:800;line-height:1.2;">Buy &amp; Sell Anything</div>
        <div style="color:rgba(255,255,255,.85);font-size:13px;margin-top:8px;">Thousands of items listed in UAE</div>
    </div>
</div>

<!-- Classified Category Icons -->
<div class="category-icons">
    <?php foreach ($clsCats as $cat): ?>
    <a href="<?= SITE_URL ?>/pages/classified_list.php?category=<?= $cat['id'] ?>" class="cat-icon-item">
        <div class="icon"><?= $cat['icon'] ?></div>
        <span><?= e($cat['name']) ?></span>
    </a>
    <?php endforeach; ?>
    <?php if (empty($clsCats)): ?>
    <?php foreach ([['📱','Mobiles'],['💻','Laptops'],['🚗','Cars'],['🏠','Real Estate'],['🛋️','Furniture'],['👔','Fashion'],['📷','Cameras'],['⌚','Watches']] as $dc): ?>
    <a href="<?= SITE_URL ?>/pages/classified_list.php" class="cat-icon-item">
        <div class="icon"><?= $dc[0] ?></div>
        <span><?= $dc[1] ?></span>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Featured Deals Banner -->
<div style="margin:8px 16px 16px;padding:18px 20px;border-radius:20px;background:linear-gradient(135deg,#6C5CE7,#8B5CF6);color:#fff;position:relative;overflow:hidden;display:flex;align-items:center;gap:16px;">
    <div style="position:absolute;top:-40%;right:-20%;width:160px;height:160px;background:rgba(255,255,255,.1);border-radius:50%;"></div>
    <div style="flex:1;position:relative;z-index:1;">
        <div style="font-size:11px;font-weight:600;letter-spacing:1px;opacity:.8;margin-bottom:4px;">TRENDING NOW</div>
        <div style="font-size:17px;font-weight:800;">Hot Deals This Week</div>
        <div style="font-size:12px;opacity:.9;margin-top:4px;">Up to 50% off on electronics</div>
    </div>
    <img src="https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=160&h=160&fit=crop"
         alt="Deals" style="width:80px;height:80px;border-radius:14px;object-fit:cover;flex-shrink:0;position:relative;z-index:1;">
</div>

<!-- Sections from DB -->
<?php foreach ($sections as $section):
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

<!-- Sample sections when DB is empty -->
<?php if (empty($sections) || !array_filter(array_map(fn($s) => $s['id'], $sections))): ?>
<?php
$sampleSections = [
    'Electronics' => [
        ['title'=>'iPhone 15 Pro Max','price'=>'4,200','img'=>'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=300&h=300&fit=crop'],
        ['title'=>'MacBook Pro M3','price'=>'8,500','img'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&h=300&fit=crop'],
        ['title'=>'Sony PS5 Bundle','price'=>'2,100','img'=>'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=300&h=300&fit=crop'],
        ['title'=>'Samsung QLED TV','price'=>'3,800','img'=>'https://images.unsplash.com/photo-1593359677879-a4bb92f829e1?w=300&h=300&fit=crop'],
    ],
    'Real Estate' => [
        ['title'=>'2BR Apartment Marina','price'=>'7,500','img'=>'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=300&h=300&fit=crop'],
        ['title'=>'Studio JBR Beach','price'=>'4,800','img'=>'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=300&h=300&fit=crop'],
        ['title'=>'Villa Palm Jumeirah','price'=>'35,000','img'=>'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=300&h=300&fit=crop'],
        ['title'=>'Office Space DIFC','price'=>'12,000','img'=>'https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=300&h=300&fit=crop'],
    ],
    'Vehicles' => [
        ['title'=>'BMW 5 Series 2022','price'=>'168,000','img'=>'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=300&h=300&fit=crop'],
        ['title'=>'Toyota Land Cruiser','price'=>'210,000','img'=>'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=300&h=300&fit=crop'],
        ['title'=>'Range Rover Sport','price'=>'195,000','img'=>'https://images.unsplash.com/photo-1617469767053-d3b523a0b982?w=300&h=300&fit=crop'],
        ['title'=>'Kawasaki Ninja 650','price'=>'28,000','img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop'],
    ],
];
foreach ($sampleSections as $secName => $sItems): ?>
<div class="section-header">
    <h2><?= $secName ?></h2>
    <a href="<?= SITE_URL ?>/pages/classified_list.php">View all</a>
</div>
<div class="classified-row">
    <?php foreach ($sItems as $si): ?>
    <a href="<?= SITE_URL ?>/pages/classified_list.php" class="classified-card">
        <img src="<?= $si['img'] ?>" alt="<?= $si['title'] ?>" class="card-img">
        <div class="card-body">
            <div class="price">AED <?= $si['price'] ?></div>
            <div class="card-title"><?= $si['title'] ?></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
window.addEventListener('load', function() {
    initAutoSlider('proj-slider', 152);
    initAutoSlider('re-slider', 172, 3400);
});

function filterSlider(prefix, btn, keyword) {
    var tags = document.getElementById(prefix + '-filter-tags');
    tags.querySelectorAll('.filter-tag').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var slider = document.getElementById(prefix + '-slider');
    slider.querySelectorAll('.' + prefix + '-card').forEach(function(card) {
        if (keyword === 'All') {
            card.style.display = '';
        } else {
            var tag = card.dataset.tag || '';
            var type = card.dataset.type || '';
            var status = card.dataset.status || '';
            var loc = card.dataset.location || '';
            var match = tag === keyword || type === keyword || status === keyword || loc.indexOf(keyword) !== -1;
            card.style.display = match ? '' : 'none';
        }
    });
    slider.scrollTo({ left: 0, behavior: 'smooth' });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
