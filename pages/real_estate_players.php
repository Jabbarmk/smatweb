<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Real Estate Players';
$currentPage = 'classifieds';

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
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1>Real Estate Players</h1>
    <div class="right-actions">
        <button id="view-list-btn" onclick="setView('list')" style="background:none;border:none;cursor:pointer;font-size:18px;padding:4px 6px;" aria-label="List view">
            <i class="fas fa-list"></i>
        </button>
        <button id="view-grid-btn" onclick="setView('grid')" style="background:none;border:none;cursor:pointer;font-size:18px;padding:4px 6px;" aria-label="Grid view">
            <i class="fas fa-th-large"></i>
        </button>
    </div>
</div>

<div class="filter-tags" style="padding-top:12px;" id="re-all-filter">
    <?php foreach (['All','Developer','Agency','Dubai','Abu Dhabi','Residential','Commercial'] as $kw): ?>
    <button class="filter-tag<?= $kw==='All'?' active':'' ?>" onclick="filterPlayers(this,'<?= $kw ?>')"><?= $kw ?></button>
    <?php endforeach; ?>
</div>

<div style="padding:4px 16px 8px;color:var(--text-light);font-size:13px;" id="re-count"><?= count($rePlayers) ?> players found</div>

<!-- List View -->
<div id="view-list" style="padding:0 16px;">
    <?php foreach ($rePlayers as $p): ?>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="re-list-item"
       data-location="<?= e($p['location']) ?>" data-type="<?= e($p['type']) ?>">
        <img src="<?= $p['logo'] ?>" alt="<?= e($p['name']) ?>" class="re-list-logo">
        <div class="re-list-info">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                <span class="re-type-badge <?= $p['type']==='Agency'?'re-type-agency':'re-type-dev' ?>" style="position:static;font-size:10px;"><?= e($p['type']) ?></span>
                <span style="font-size:10px;color:var(--text-light);">Est. <?= e($p['established']) ?></span>
            </div>
            <div class="re-list-name"><?= e($p['name']) ?></div>
            <div class="re-list-caption"><?= e($p['caption']) ?></div>
            <div style="font-size:12px;color:var(--text-secondary);margin:4px 0;"><?= e($p['desc']) ?></div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                <div style="display:flex;align-items:center;gap:4px;">
                    <span style="font-size:18px;font-weight:800;color:var(--primary);"><?= $p['totalProjects'] ?></span>
                    <span style="font-size:11px;color:var(--text-light);">Projects</span>
                </div>
                <div style="font-size:11px;color:var(--text-light);">
                    <i class="fas fa-map-marker-alt" style="color:var(--primary);margin-right:3px;"></i><?= e($p['location']) ?>
                </div>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- Grid View -->
<div id="view-grid" class="proj-grid-view" style="display:none;">
    <?php foreach ($rePlayers as $p): ?>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="re-card"
       data-location="<?= e($p['location']) ?>" data-type="<?= e($p['type']) ?>">
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
            <div class="re-card-location"><i class="fas fa-map-marker-alt"></i> <?= e($p['location']) ?></div>
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
    filterPlayers(document.querySelector('#re-all-filter .filter-tag.active'), document.querySelector('#re-all-filter .filter-tag.active').textContent);
}

function filterPlayers(btn, keyword) {
    document.querySelectorAll('#re-all-filter .filter-tag').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var listItems = document.querySelectorAll('#view-list .re-list-item');
    var gridItems = document.querySelectorAll('#view-grid .re-card');
    var count = 0;
    function matchItem(el) {
        if (keyword === 'All') return true;
        return el.dataset.location === keyword || el.dataset.type === keyword;
    }
    listItems.forEach(function(el) {
        var show = matchItem(el);
        el.style.display = show ? '' : 'none';
        if (show) count++;
    });
    gridItems.forEach(function(el) {
        el.style.display = matchItem(el) ? '' : 'none';
    });
    document.getElementById('re-count').textContent = count + ' player' + (count !== 1 ? 's' : '') + ' found';
}

document.getElementById('view-list-btn').style.color = 'var(--primary)';
document.getElementById('view-grid-btn').style.color = 'var(--text-light)';
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
