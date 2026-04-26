<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Offline Projects';
$currentPage = 'classifieds';

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

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1>Offline Projects</h1>
    <div class="right-actions">
        <button id="view-list-btn" onclick="setView('list')" style="background:none;border:none;cursor:pointer;font-size:18px;padding:4px 6px;" aria-label="List view">
            <i class="fas fa-list"></i>
        </button>
        <button id="view-grid-btn" onclick="setView('grid')" style="background:none;border:none;cursor:pointer;font-size:18px;padding:4px 6px;" aria-label="Grid view">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</div>

<div class="filter-tags" style="padding-top:12px;" id="proj-all-filter">
    <?php foreach (['All','Dubai','Abu Dhabi','Off-Plan','Completed','Villa','Apartment','Commercial'] as $kw): ?>
    <button class="filter-tag<?= $kw==='All'?' active':'' ?>" onclick="filterProjects(this,'<?= $kw ?>')"><?= $kw ?></button>
    <?php endforeach; ?>
</div>

<div style="padding:4px 16px 8px;color:var(--text-light);font-size:13px;" id="proj-count"><?= count($offlineProjects) ?> projects found</div>

<!-- List View -->
<div id="view-list" style="padding:0 16px;">
    <?php foreach ($offlineProjects as $p): ?>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="proj-list-item"
       data-tag="<?= e($p['tag']) ?>" data-type="<?= e($p['type']) ?>" data-status="<?= e($p['status']) ?>">
        <img src="<?= $p['img'] ?>" alt="<?= e($p['title']) ?>" class="proj-list-img">
        <div class="proj-list-info">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                <span class="proj-status-badge <?= $p['status']==='Completed'?'status-done':'status-plan' ?>" style="position:static;font-size:10px;"><?= e($p['status']) ?></span>
                <span style="font-size:10px;color:var(--text-light);"><?= e($p['type']) ?></span>
            </div>
            <div class="proj-list-title"><?= e($p['title']) ?></div>
            <div class="proj-list-caption"><?= e($p['caption']) ?></div>
            <div class="proj-list-location"><i class="fas fa-map-marker-alt"></i> <?= e($p['location']) ?></div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                <div class="proj-list-price"><?= e($p['price']) ?></div>
                <div style="font-size:10px;color:var(--text-light);">📅 <?= e($p['completion']) ?></div>
            </div>
            <div style="font-size:11px;color:var(--text-secondary);margin-top:4px;"><?= e($p['developer']) ?></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- Grid View -->
<div id="view-grid" class="proj-grid-view" style="display:none;">
    <?php foreach ($offlineProjects as $p): ?>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="proj-card"
       data-tag="<?= e($p['tag']) ?>" data-type="<?= e($p['type']) ?>" data-status="<?= e($p['status']) ?>">
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

<script>
var currentView = 'list';

function setView(v) {
    currentView = v;
    document.getElementById('view-list').style.display = v === 'list' ? '' : 'none';
    document.getElementById('view-grid').style.display = v === 'grid' ? '' : 'none';
    document.getElementById('view-list-btn').style.color = v === 'list' ? 'var(--primary)' : 'var(--text-light)';
    document.getElementById('view-grid-btn').style.color = v === 'grid' ? 'var(--primary)' : 'var(--text-light)';
    filterProjects(document.querySelector('#proj-all-filter .filter-tag.active'), document.querySelector('#proj-all-filter .filter-tag.active').textContent);
}

function filterProjects(btn, keyword) {
    document.querySelectorAll('#proj-all-filter .filter-tag').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var listItems = document.querySelectorAll('#view-list .proj-list-item');
    var gridItems = document.querySelectorAll('#view-grid .proj-card');
    var count = 0;
    function matchItem(el) {
        if (keyword === 'All') return true;
        return el.dataset.tag === keyword || el.dataset.type === keyword || el.dataset.status === keyword;
    }
    listItems.forEach(function(el) {
        var show = matchItem(el);
        el.style.display = show ? '' : 'none';
        if (show) count++;
    });
    gridItems.forEach(function(el) {
        el.style.display = matchItem(el) ? '' : 'none';
    });
    document.getElementById('proj-count').textContent = count + ' project' + (count !== 1 ? 's' : '') + ' found';
}

document.getElementById('view-list-btn').style.color = 'var(--primary)';
document.getElementById('view-grid-btn').style.color = 'var(--text-light)';
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
