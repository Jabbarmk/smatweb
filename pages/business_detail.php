<?php
require_once __DIR__ . '/../includes/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: businesses.php'); exit; }

$db = getDB();

$stmt = $db->prepare("SELECT b.*, bc.name AS category_name FROM businesses b LEFT JOIN business_categories bc ON b.category_id = bc.id WHERE b.id = ? AND b.is_active = 1");
$stmt->execute([$id]);
$biz = $stmt->fetch();
if (!$biz) { header('Location: businesses.php'); exit; }

function safeFetch(PDO $db, string $sql, array $p = []): array {
    try { $s = $db->prepare($sql); $s->execute($p); return $s->fetchAll(); }
    catch (PDOException $e) { return []; }
}

$gallery      = safeFetch($db, "SELECT * FROM business_gallery      WHERE business_id=? ORDER BY sort_order", [$id]);
$videos       = safeFetch($db, "SELECT * FROM business_videos       WHERE business_id=? ORDER BY sort_order", [$id]);
$reels        = safeFetch($db, "SELECT * FROM business_reels        WHERE business_id=? ORDER BY sort_order", [$id]);
$services     = safeFetch($db, "SELECT * FROM business_services     WHERE business_id=? ORDER BY sort_order", [$id]);
$testimonials = safeFetch($db, "SELECT * FROM business_testimonials WHERE business_id=? ORDER BY sort_order", [$id]);
$clients      = safeFetch($db, "SELECT * FROM business_clients      WHERE business_id=? ORDER BY sort_order", [$id]);

/* ---- Fallbacks ---- */
$galleryFallbacks = [
    ['src'=>'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=400&fit=crop','caption'=>'Ambiance'],
    ['src'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=400&fit=crop','caption'=>'Dining'],
    ['src'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=400&fit=crop','caption'=>'Interior'],
    ['src'=>'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=400&h=400&fit=crop','caption'=>'Facilities'],
    ['src'=>'https://images.unsplash.com/photo-1562243061-204550d8a2c9?w=400&h=400&fit=crop','caption'=>'Services'],
    ['src'=>'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400&h=400&fit=crop','caption'=>'Location'],
];
$servicesFallbacks = [
    ['icon'=>'🏗️','title'=>'Consultation',   'desc'=>'Expert consultation tailored to your specific needs and goals.'],
    ['icon'=>'🔧','title'=>'Installation',    'desc'=>'Professional setup by our certified technicians.'],
    ['icon'=>'🛡️','title'=>'Maintenance',     'desc'=>'Regular support to keep everything running smoothly.'],
    ['icon'=>'📋','title'=>'Project Planning','desc'=>'End-to-end planning and project management.'],
    ['icon'=>'🎯','title'=>'Strategy',         'desc'=>'Data-driven strategies for business growth.'],
    ['icon'=>'📊','title'=>'Analytics',        'desc'=>'Detailed reporting and performance insights.'],
];
$testimonialsFallbacks = [
    ['name'=>'Ahmed Al Rashid', 'company'=>'Tech Solutions LLC', 'rating'=>5,'review'=>'Exceptional service and professionalism. Highly recommend to anyone looking for quality in UAE.','avatar'=>'A'],
    ['name'=>'Sara Mohammed',   'company'=>'Dubai Ventures',      'rating'=>5,'review'=>'Outstanding experience from start to finish. The team was always available and knowledgeable.','avatar'=>'S'],
    ['name'=>'James Wilson',    'company'=>'Global Corp UAE',      'rating'=>4,'review'=>'Very professional, delivered exactly what was promised on time. Will definitely use again.','avatar'=>'J'],
    ['name'=>'Fatima Al Zaabi','company'=>'Emirates Group',       'rating'=>5,'review'=>'Absolutely fantastic! The quality of work exceeded our expectations. 10/10 would recommend.','avatar'=>'F'],
];
$reelsFallbacks = [
    ['thumb'=>'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&h=360&fit=crop','title'=>'Our Story',          'url'=>''],
    ['thumb'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=200&h=360&fit=crop','title'=>'Behind the Scenes','url'=>''],
    ['thumb'=>'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200&h=360&fit=crop','title'=>'Our Team',         'url'=>''],
    ['thumb'=>'https://images.unsplash.com/photo-1562243061-204550d8a2c9?w=200&h=360&fit=crop','title'=>'Our Services',       'url'=>''],
];
$clientsFallbacks = [
    ['name'=>'Emaar Properties', 'logo'=>null],
    ['name'=>'Dubai Holdings',   'logo'=>null],
    ['name'=>'Majid Al Futtaim','logo'=>null],
    ['name'=>'Al Futtaim Group', 'logo'=>null],
    ['name'=>'ADNOC Group',      'logo'=>null],
    ['name'=>'Emirates Group',   'logo'=>null],
];
$clientAvatarGrads = [
    'linear-gradient(135deg,#6C5CE7,#a29bfe)',
    'linear-gradient(135deg,#00CEC9,#00b5b0)',
    'linear-gradient(135deg,#E17055,#d35400)',
    'linear-gradient(135deg,#0984e3,#74b9ff)',
    'linear-gradient(135deg,#6c5ce7,#fd79a8)',
    'linear-gradient(135deg,#00b894,#55efc4)',
];

/* ---- Build slider images ---- */
$sliderImgs = !empty($gallery)
    ? array_map(fn($g) => ['src'=>getImageUrl($g['image'],'businesses'), 'caption'=>$g['caption']??''], $gallery)
    : [
        ['src'=>getImageUrl($biz['image'],'businesses'),                                                 'caption'=>$biz['name']],
        ['src'=>'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&h=500&fit=crop',       'caption'=>'Ambiance'],
        ['src'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=500&fit=crop',    'caption'=>'Interior'],
        ['src'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=500&fit=crop',    'caption'=>'Experience'],
    ];

$galleryItems = !empty($gallery)
    ? array_map(fn($g) => ['src'=>getImageUrl($g['image'],'businesses'), 'caption'=>$g['caption']??''], $gallery)
    : $galleryFallbacks;

$svcList    = !empty($services)     ? $services     : $servicesFallbacks;
$reelList   = !empty($reels)        ? $reels        : $reelsFallbacks;
$revList    = !empty($testimonials) ? $testimonials : $testimonialsFallbacks;
$clientList = !empty($clients)      ? $clients      : $clientsFallbacks;

/* ---- Helpers ---- */
function renderStars(float $r): string {
    $o = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $r)        $o .= '<i class="fas fa-star"></i>';
        elseif ($i-.5 <= $r) $o .= '<i class="fas fa-star-half-alt"></i>';
        else                 $o .= '<i class="far fa-star"></i>';
    }
    return $o;
}
function ytEmbed(string $u): string {
    preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $u, $m);
    return isset($m[1]) ? 'https://www.youtube.com/embed/'.$m[1].'?rel=0&modestbranding=1' : $u;
}
function ytThumb(string $u): ?string {
    preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $u, $m);
    return isset($m[1]) ? 'https://img.youtube.com/vi/'.$m[1].'/hqdefault.jpg' : null;
}

$avgRating  = $biz['rating'] > 0 ? (float)$biz['rating'] : 4.5;
$pageTitle  = e($biz['name']) . ' — SMARTUAE';
$aboutText  = !empty($biz['about']) ? $biz['about'] : ($biz['description'] ?? '');
if (!$aboutText) $aboutText = 'Welcome to ' . $biz['name'] . '. We are committed to providing exceptional quality and service to our valued customers across the UAE. Our experienced team ensures the best experience every time you visit. With years of industry expertise, we deliver solutions that exceed expectations and build lasting relationships with every client.';

include __DIR__ . '/../includes/header.php';
?>

<!-- TOP BAR -->
<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1 style="font-size:14px;max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($biz['name']) ?></h1>
    <button onclick="shareBiz()" style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--dark);padding:8px;"><i class="fas fa-share-alt"></i></button>
</div>

<!-- HERO SLIDER -->
<div class="bd-slider-wrap" id="bdWrap">
    <div class="bd-slider-track" id="bdTrack">
        <?php foreach ($sliderImgs as $img): ?>
        <div class="bd-slide">
            <img src="<?= e($img['src']) ?>" alt="<?= e($img['caption']) ?>"
                 onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&h=500&fit=crop'">
            <div class="bd-slide-overlay"></div>
            <?php if ($img['caption']): ?><div class="bd-slide-caption"><?= e($img['caption']) ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="bd-slide-counter" id="bdCounter">1 / <?= count($sliderImgs) ?></div>
    <div class="bd-zoom-hint"><i class="fas fa-search-plus"></i> Tap to zoom</div>
    <div class="bd-slider-dots" id="bdDots">
        <?php foreach ($sliderImgs as $i => $s): ?>
        <div class="dot <?= $i===0?'active':'' ?>" data-i="<?= $i ?>"></div>
        <?php endforeach; ?>
    </div>
</div>

<!-- BUSINESS PROFILE CARD -->
<div class="bd-header">
    <div class="bd-logo-row">
        <div class="bd-logo">
            <?php if (!empty($biz['logo'])): ?>
                <img src="<?= getImageUrl($biz['logo'],'businesses') ?>" alt="Logo">
            <?php else: ?>
                <?= mb_strtoupper(mb_substr($biz['name'],0,1,'UTF-8'),'UTF-8') ?>
            <?php endif; ?>
        </div>
        <div style="flex:1;min-width:0;">
            <div class="bd-name"><?= e($biz['name']) ?></div>
            <?php if ($biz['category_name']): ?><div class="bd-cat-label"><i class="fas fa-tag"></i> <?= e($biz['category_name']) ?></div><?php endif; ?>
            <?php if (!empty($biz['tagline'])): ?><div class="bd-tagline">"<?= e($biz['tagline']) ?>"</div><?php endif; ?>
        </div>
    </div>
    <div class="bd-meta-row">
        <?php if ($biz['rating'] > 0): ?>
        <div class="bd-rating-badge"><?= renderStars($avgRating) ?> <strong><?= number_format($avgRating,1) ?></strong></div>
        <?php endif; ?>
        <?php if ($biz['distance']): ?><div class="bd-distance"><i class="fas fa-location-arrow"></i> <?= e($biz['distance']) ?></div><?php endif; ?>
        <span class="bd-badge verified"><i class="fas fa-check-circle"></i> Verified</span>
        <span class="bd-badge open"><i class="fas fa-circle" style="font-size:7px;"></i> Open</span>
    </div>
    <?php if ($biz['address']): ?>
    <div class="bd-address-row"><i class="fas fa-map-marker-alt"></i> <?= e($biz['address']) ?></div>
    <?php endif; ?>
</div>

<!-- QUICK ACTIONS -->
<div class="bd-actions">
    <?php if ($biz['phone']): ?>
    <a href="tel:<?= e($biz['phone']) ?>" class="bd-action call">
        <div class="bd-action-icon"><i class="fas fa-phone"></i></div><span>Call</span>
    </a>
    <?php endif; ?>
    <?php if ($biz['whatsapp']): ?>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',e($biz['whatsapp'])) ?>" target="_blank" class="bd-action wa">
        <div class="bd-action-icon"><i class="fab fa-whatsapp"></i></div><span>WhatsApp</span>
    </a>
    <?php endif; ?>
    <?php if (!empty($biz['website'])): ?>
    <a href="<?= e($biz['website']) ?>" target="_blank" class="bd-action web">
        <div class="bd-action-icon"><i class="fas fa-globe"></i></div><span>Website</span>
    </a>
    <?php endif; ?>
    <?php if (!empty($biz['email'])): ?>
    <a href="mailto:<?= e($biz['email']) ?>" class="bd-action email">
        <div class="bd-action-icon"><i class="fas fa-envelope"></i></div><span>Email</span>
    </a>
    <?php endif; ?>
    <a href="https://maps.google.com/?q=<?= urlencode($biz['address']??$biz['name'].' UAE') ?>" target="_blank" class="bd-action map">
        <div class="bd-action-icon"><i class="fas fa-directions"></i></div><span>Directions</span>
    </a>
    <button onclick="shareBiz()" class="bd-action share">
        <div class="bd-action-icon"><i class="fas fa-share-alt"></i></div><span>Share</span>
    </button>
</div>

<div class="bs-divider"></div>
<div class="bs-page-wrap">

<!-- ===== ABOUT ===== -->
<div class="bs-section">
    <div class="bs-sh">
        <span class="bs-title">About Us</span>
    </div>
    <div class="bs-about-text" id="aboutText"><?= nl2br(e($aboutText)) ?></div>
    <button class="bs-rm-btn" id="aboutBtn" onclick="toggleAbout()">
        Read More <i class="fas fa-chevron-down" id="aboutChevron"></i>
    </button>
</div>

<!-- ===== KEY STATS ===== -->
<div class="bs-stats-row">
    <div class="bs-stat">
        <div class="bs-stat-num"><?= number_format($avgRating,1) ?></div>
        <div class="bs-stat-lbl">Rating</div>
    </div>
    <div class="bs-stat">
        <div class="bs-stat-num"><?= !empty($biz['established_year']) ? $biz['established_year'] : date('Y') - 5 ?></div>
        <div class="bs-stat-lbl">Est. Year</div>
    </div>
    <div class="bs-stat">
        <div class="bs-stat-num"><?= !empty($biz['employees']) ? $biz['employees'] : '50+' ?></div>
        <div class="bs-stat-lbl">Team Size</div>
    </div>
    <div class="bs-stat">
        <div class="bs-stat-num"><?= count($revList) ?>+</div>
        <div class="bs-stat-lbl">Reviews</div>
    </div>
</div>

<!-- ===== TAGS ===== -->
<div class="bs-tags">
    <span class="bs-tag"><?= e($biz['category_name'] ?: 'Business') ?></span>
    <span class="bs-tag">UAE</span>
    <span class="bs-tag"><i class="fas fa-check-circle"></i> Verified</span>
    <span class="bs-tag">Top Rated</span>
    <span class="bs-tag"><i class="fas fa-circle" style="font-size:7px;"></i> Open Now</span>
</div>

<div class="bs-divider"></div>

<!-- ===== SERVICES & SOLUTIONS ===== -->
<div class="bs-section">
    <div class="bs-sh">
        <span class="bs-title">Services &amp; Solutions</span>
        <span style="font-size:11px;color:var(--text-light);"><?= count($svcList) ?> offerings</span>
    </div>
    <div class="bs-svc-grid">
        <?php foreach ($svcList as $i => $svc): ?>
        <div class="bs-svc-card <?= $i >= 4 ? 'bs-hidden svc-extra' : '' ?>">
            <?php if (!empty($svc['image'])): ?>
                <img src="<?= getImageUrl($svc['image'],'businesses') ?>" alt="" class="bs-svc-img" onerror="this.style.display='none'">
            <?php elseif (!empty($svc['icon'])): ?>
                <div class="bs-svc-icon"><?= $svc['icon'] ?></div>
            <?php else: ?>
                <div class="bs-svc-icon">⚙️</div>
            <?php endif; ?>
            <div class="bs-svc-title"><?= e($svc['title']) ?></div>
            <?php $desc = $svc['description'] ?? $svc['desc'] ?? ''; if ($desc): ?>
            <div class="bs-svc-desc"><?= e($desc) ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($svcList) > 4): ?>
    <button class="bs-expand-btn" id="svcBtn" onclick="expandServices()">
        View All <?= count($svcList) ?> Services <i class="fas fa-chevron-down"></i>
    </button>
    <?php endif; ?>
</div>

<div class="bs-divider"></div>

<!-- ===== GALLERY ===== -->
<div class="bs-section">
    <div class="bs-sh">
        <span class="bs-title">Gallery</span>
        <?php if (count($galleryItems) > 6): ?>
        <a href="#" class="bs-view-all" id="galBtn" onclick="expandGallery(event)">
            View All <?= count($galleryItems) ?> <i class="fas fa-images"></i>
        </a>
        <?php endif; ?>
    </div>
    <div class="bs-gal-grid">
        <?php foreach ($galleryItems as $i => $img): ?>
        <div class="bs-gal-item <?= $i >= 6 ? 'bs-hidden gal-extra' : '' ?>" onclick="openLB(<?= $i ?>,galleryImgs)">
            <img src="<?= e($img['src']) ?>" alt="<?= e($img['caption']) ?>"
                 onerror="this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=400&fit=crop'">
            <div class="bs-gal-zoom"><i class="fas fa-search-plus"></i></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($galleryItems) > 6): ?>
    <button class="bs-expand-btn" id="galExpandBtn" onclick="expandGallery(event)" style="margin-top:10px;">
        View All <?= count($galleryItems) ?> Photos <i class="fas fa-chevron-down"></i>
    </button>
    <?php endif; ?>
</div>

<div class="bs-divider"></div>

<!-- ===== VIDEOS ===== -->
<?php
$vidList = !empty($videos) ? $videos : [
    ['title'=>'Company Overview','video_url'=>'https://www.youtube.com/watch?v=dQw4w9WgXcQ','thumbnail'=>null],
];
?>
<div class="bs-section">
    <div class="bs-sh"><span class="bs-title">Videos</span></div>
    <div class="bs-vid-scroll">
        <?php foreach ($vidList as $vid):
            $embed = ytEmbed($vid['video_url']);
            $thumb = !empty($vid['thumbnail']) ? getImageUrl($vid['thumbnail'],'businesses') : (ytThumb($vid['video_url']) ?? 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=460&h=260&fit=crop');
        ?>
        <div class="bs-vid-card">
            <div class="bs-vid-thumb" onclick="playBsVid(this,'<?= e($embed) ?>')">
                <img src="<?= e($thumb) ?>" alt="<?= e($vid['title']??'Video') ?>">
                <div class="bs-vid-play"><i class="fas fa-play-circle"></i></div>
            </div>
            <iframe class="bs-vid-iframe" allowfullscreen></iframe>
            <div class="bs-vid-info">
                <div class="bs-vid-title"><?= e($vid['title']??'Company Video') ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="bs-divider"></div>

<!-- ===== REELS ===== -->
<div class="bs-section">
    <div class="bs-sh"><span class="bs-title">Reels</span></div>
    <div class="bs-reels-scroll">
        <?php foreach ($reelList as $reel):
            $rThumb = !empty($reel['thumbnail']) ? getImageUrl($reel['thumbnail'],'businesses')
                    : (isset($reel['thumb']) ? $reel['thumb'] : (ytThumb($reel['video_url']??'') ?? 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=200&h=360&fit=crop'));
            $rUrl   = e($reel['video_url'] ?? $reel['url'] ?? '');
        ?>
        <div class="bs-reel" onclick="openReel('<?= $rUrl ?>')">
            <img src="<?= e($rThumb) ?>" alt="<?= e($reel['title']??'Reel') ?>">
            <div class="bs-reel-grad"></div>
            <div class="bs-reel-play"><i class="fas fa-play"></i></div>
            <?php if (!empty($reel['title'])): ?><div class="bs-reel-title"><?= e($reel['title']) ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <p style="font-size:11px;color:var(--text-light);text-align:center;margin-top:10px;">← Swipe for more →</p>
</div>

<div class="bs-divider"></div>

<!-- ===== CLIENTS & PARTNERS ===== -->
<div class="bs-section">
    <div class="bs-sh"><span class="bs-title">Clients &amp; Partners</span></div>
    <div class="bs-clients-scroll">
        <?php foreach ($clientList as $ci => $c):
            $initial = mb_strtoupper(mb_substr($c['name'],0,1,'UTF-8'),'UTF-8');
            $grad    = $clientAvatarGrads[$ci % count($clientAvatarGrads)];
        ?>
        <div class="bs-client-item">
            <?php if (!empty($c['logo'])): ?>
                <img src="<?= getImageUrl($c['logo'],'businesses') ?>" alt="<?= e($c['name']) ?>" class="bs-client-logo"
                     onerror="this.style.display='none'">
            <?php else: ?>
                <div class="bs-client-avatar" style="background:<?= $grad ?>"><?= $initial ?></div>
            <?php endif; ?>
            <div class="bs-client-name"><?= e($c['name']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="bs-divider"></div>

<!-- ===== TESTIMONIALS ===== -->
<div class="bs-section">
    <div class="bs-sh"><span class="bs-title">What Clients Say</span></div>

    <!-- Rating Summary -->
    <div class="bs-review-summary">
        <div class="bs-rev-avg">
            <div class="big-num"><?= number_format($avgRating,1) ?></div>
            <div class="stars"><?= renderStars($avgRating) ?></div>
            <div class="cnt"><?= count($revList) ?> reviews</div>
        </div>
        <div style="flex:1;">
            <?php foreach ([5=>72,4=>18,3=>6,2=>3,1=>1] as $s=>$pct): ?>
            <div class="bs-bar-row">
                <span class="n"><?= $s ?></span>
                <div class="bs-bar-track"><div class="bs-bar-fill" style="width:<?= $pct ?>%"></div></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Review Cards -->
    <?php foreach ($revList as $ri => $rev):
        $rName   = e($rev['client_name'] ?? $rev['name']   ?? 'Customer');
        $rCo     = e($rev['client_company'] ?? $rev['company'] ?? '');
        $rRating = (int)($rev['rating'] ?? 5);
        $rText   = e($rev['review'] ?? '');
        $rDate   = isset($rev['created_at']) ? date('M Y', strtotime($rev['created_at'])) : 'Recent';
        $rPhoto  = !empty($rev['client_photo']) ? getImageUrl($rev['client_photo'],'businesses') : null;
        $rAvatar = $rev['avatar'] ?? mb_strtoupper(mb_substr($rev['client_name']??$rev['name']??'C',0,1,'UTF-8'),'UTF-8');
    ?>
    <div class="bs-review-card <?= $ri >= 2 ? 'bs-hidden rev-extra' : '' ?>">
        <div class="bs-rev-head">
            <div class="bs-rev-avatar">
                <?php if ($rPhoto): ?><img src="<?= $rPhoto ?>" alt=""><?php else: ?><?= $rAvatar ?><?php endif; ?>
            </div>
            <div>
                <div class="bs-rev-name"><?= $rName ?></div>
                <?php if ($rCo): ?><div class="bs-rev-co"><?= $rCo ?></div><?php endif; ?>
            </div>
        </div>
        <div class="bs-rev-stars"><?= renderStars($rRating) ?></div>
        <div class="bs-rev-text"><?= $rText ?></div>
        <div class="bs-rev-date"><?= $rDate ?></div>
    </div>
    <?php endforeach; ?>

    <?php if (count($revList) > 2): ?>
    <button class="bs-expand-btn" id="revBtn" onclick="expandReviews()">
        Read All <?= count($revList) ?> Reviews <i class="fas fa-chevron-down"></i>
    </button>
    <?php endif; ?>
</div>

<div class="bs-divider"></div>

<!-- ===== CONTACT & LOCATION ===== -->
<div class="bs-section">
    <div class="bs-sh"><span class="bs-title">Contact &amp; Location</span></div>

    <?php if (!empty($biz['phone'])): ?>
    <a href="tel:<?= e($biz['phone']) ?>" class="bs-contact-item">
        <div class="bs-contact-icon call"><i class="fas fa-phone"></i></div>
        <div>
            <div class="bs-contact-lbl">Phone</div>
            <div class="bs-contact-val"><?= e($biz['phone']) ?></div>
        </div>
        <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--text-light);font-size:12px;"></i>
    </a>
    <?php endif; ?>

    <?php if (!empty($biz['whatsapp'])): ?>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',e($biz['whatsapp'])) ?>" target="_blank" class="bs-contact-item">
        <div class="bs-contact-icon wa"><i class="fab fa-whatsapp"></i></div>
        <div>
            <div class="bs-contact-lbl">WhatsApp</div>
            <div class="bs-contact-val"><?= e($biz['whatsapp']) ?></div>
        </div>
        <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--text-light);font-size:12px;"></i>
    </a>
    <?php endif; ?>

    <?php if (!empty($biz['email'])): ?>
    <a href="mailto:<?= e($biz['email']) ?>" class="bs-contact-item">
        <div class="bs-contact-icon mail"><i class="fas fa-envelope"></i></div>
        <div>
            <div class="bs-contact-lbl">Email</div>
            <div class="bs-contact-val"><?= e($biz['email']) ?></div>
        </div>
        <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--text-light);font-size:12px;"></i>
    </a>
    <?php endif; ?>

    <?php if (!empty($biz['website'])): ?>
    <a href="<?= e($biz['website']) ?>" target="_blank" class="bs-contact-item">
        <div class="bs-contact-icon web"><i class="fas fa-globe"></i></div>
        <div>
            <div class="bs-contact-lbl">Website</div>
            <div class="bs-contact-val"><?= e($biz['website']) ?></div>
        </div>
        <i class="fas fa-chevron-right" style="margin-left:auto;color:var(--text-light);font-size:12px;"></i>
    </a>
    <?php endif; ?>

    <!-- Map -->
    <?php if (!empty($biz['map_embed'])): ?>
    <iframe src="<?= e($biz['map_embed']) ?>" class="bs-map-embed" loading="lazy"></iframe>
    <?php else: ?>
    <a href="https://maps.google.com/?q=<?= urlencode($biz['address']??$biz['name'].' UAE') ?>" target="_blank" class="bs-map-btn">
        <div class="map-ic-box"><i class="fas fa-map-marker-alt"></i></div>
        <div>
            <div style="font-size:11px;color:var(--text-light);font-weight:600;text-transform:uppercase;letter-spacing:.4px;">Location</div>
            <div style="font-size:13px;font-weight:700;color:var(--dark);margin-top:2px;"><?= e($biz['address'] ?? 'Open in Google Maps') ?></div>
        </div>
        <i class="fas fa-external-link-alt" style="margin-left:auto;color:var(--text-light);font-size:12px;"></i>
    </a>
    <?php endif; ?>

    <!-- Social links -->
    <?php $hasSoc = !empty($biz['facebook'])||!empty($biz['instagram'])||!empty($biz['twitter'])||!empty($biz['youtube'])||!empty($biz['linkedin'])||!empty($biz['whatsapp']); ?>
    <?php if ($hasSoc): ?>
    <div style="margin-top:16px;">
        <div style="font-size:12px;font-weight:700;color:var(--text-secondary);margin-bottom:10px;">Follow Us</div>
        <div class="bs-socials">
            <?php if (!empty($biz['facebook'])): ?><a href="<?= e($biz['facebook']) ?>" target="_blank" class="bs-soc-btn fb"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
            <?php if (!empty($biz['instagram'])): ?><a href="<?= e($biz['instagram']) ?>" target="_blank" class="bs-soc-btn ig"><i class="fab fa-instagram"></i></a><?php endif; ?>
            <?php if (!empty($biz['twitter'])): ?><a href="<?= e($biz['twitter']) ?>" target="_blank" class="bs-soc-btn tw"><i class="fab fa-twitter"></i></a><?php endif; ?>
            <?php if (!empty($biz['youtube'])): ?><a href="<?= e($biz['youtube']) ?>" target="_blank" class="bs-soc-btn yt"><i class="fab fa-youtube"></i></a><?php endif; ?>
            <?php if (!empty($biz['linkedin'])): ?><a href="<?= e($biz['linkedin']) ?>" target="_blank" class="bs-soc-btn li"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
            <?php if (!empty($biz['whatsapp'])): ?><a href="https://wa.me/<?= preg_replace('/[^0-9]/','',e($biz['whatsapp'])) ?>" target="_blank" class="bs-soc-btn ws"><i class="fab fa-whatsapp"></i></a><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</div><!-- end .bs-page-wrap -->

<!-- LIGHTBOX -->
<div id="bd-lb">
    <button class="lb-close-btn" onclick="closeLB()"><i class="fas fa-times"></i></button>
    <div class="lb-cnt" id="lbCnt">1 / 1</div>
    <div class="lb-img-box"><img id="lbImg" src="" alt=""></div>
    <button class="lb-nav-btn lb-prev" onclick="lbNav(-1)"><i class="fas fa-chevron-left"></i></button>
    <button class="lb-nav-btn lb-next" onclick="lbNav(1)"><i class="fas fa-chevron-right"></i></button>
    <div class="lb-cap" id="lbCap"></div>
</div>

<!-- REEL MODAL -->
<div id="reel-modal">
    <button onclick="closeReel()" style="position:absolute;top:14px;right:14px;color:#fff;font-size:22px;cursor:pointer;width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.12);border-radius:50%;border:none;"><i class="fas fa-times"></i></button>
    <iframe id="reel-frame" width="100%" height="70%" style="border:none;max-width:400px;" allowfullscreen></iframe>
</div>

<script>
var SITE_URL = '<?= SITE_URL ?>';
var sliderImgs  = <?= json_encode($sliderImgs,  JSON_UNESCAPED_UNICODE) ?>;
var galleryImgs = <?= json_encode($galleryItems, JSON_UNESCAPED_UNICODE) ?>;

/* ---- Hero Slider ---- */
(function(){
    var track = document.getElementById('bdTrack');
    var dots  = document.querySelectorAll('#bdDots .dot');
    var cnt   = document.getElementById('bdCounter');
    var total = <?= count($sliderImgs) ?>;
    var cur   = 0;

    function go(i){
        cur = i;
        track.style.transform = 'translateX(-'+(cur*100)+'%)';
        dots.forEach(function(d,j){ d.classList.toggle('active', j===cur); });
        cnt.textContent = (cur+1)+' / '+total;
    }

    dots.forEach(function(d,i){ d.addEventListener('click', function(){ go(i); }); });
    var auto = setInterval(function(){ go((cur+1)%total); }, 4500);

    var sx = 0;
    track.addEventListener('touchstart', function(e){ sx=e.touches[0].clientX; },{passive:true});
    track.addEventListener('touchend',   function(e){
        var dx = sx-e.changedTouches[0].clientX;
        if(Math.abs(dx)>40){ clearInterval(auto); go(dx>0?Math.min(cur+1,total-1):Math.max(cur-1,0)); auto=setInterval(function(){ go((cur+1)%total); },4500); }
    },{passive:true});

    document.getElementById('bdWrap').addEventListener('click', function(){ openLB(cur, sliderImgs); });
})();

/* ---- Read More (About) ---- */
function toggleAbout(){
    var el  = document.getElementById('aboutText');
    var btn = document.getElementById('aboutBtn');
    var chv = document.getElementById('aboutChevron');
    var expanded = el.classList.toggle('show');
    btn.childNodes[0].textContent = expanded ? 'Read Less ' : 'Read More ';
    chv.className = expanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
}
/* Hide Read More btn if text isn't clamped */
(function(){
    var el = document.getElementById('aboutText');
    var btn = document.getElementById('aboutBtn');
    if(el && btn && el.scrollHeight <= el.clientHeight + 2){ btn.style.display='none'; }
})();

/* ---- Expand Services ---- */
function expandServices(){
    document.querySelectorAll('.svc-extra').forEach(function(c){ c.classList.remove('bs-hidden'); });
    var btn = document.getElementById('svcBtn');
    if(btn) btn.style.display='none';
}

/* ---- Expand Gallery ---- */
function expandGallery(e){
    if(e && e.preventDefault) e.preventDefault();
    document.querySelectorAll('.gal-extra').forEach(function(c){ c.classList.remove('bs-hidden'); });
    var btn = document.getElementById('galBtn');         if(btn) btn.style.display='none';
    var btn2= document.getElementById('galExpandBtn');   if(btn2) btn2.style.display='none';
}

/* ---- Expand Reviews ---- */
function expandReviews(){
    document.querySelectorAll('.rev-extra').forEach(function(c){ c.classList.remove('bs-hidden'); });
    var btn = document.getElementById('revBtn');
    if(btn) btn.style.display='none';
}

/* ---- Lightbox ---- */
var lbImgs=[], lbCur=0;
function openLB(i,imgs){ lbImgs=imgs; lbCur=i; showLB(); document.getElementById('bd-lb').classList.add('open'); document.body.style.overflow='hidden'; }
function closeLB(){ document.getElementById('bd-lb').classList.remove('open'); document.body.style.overflow=''; }
function showLB(){ var img=lbImgs[lbCur]; document.getElementById('lbImg').src=img.src||img; document.getElementById('lbCap').textContent=img.caption||''; document.getElementById('lbCnt').textContent=(lbCur+1)+' / '+lbImgs.length; }
function lbNav(d){ lbCur=(lbCur+d+lbImgs.length)%lbImgs.length; showLB(); }
(function(){
    var lb=document.getElementById('bd-lb'), sx=0;
    lb.addEventListener('touchstart', function(e){ sx=e.touches[0].clientX; },{passive:true});
    lb.addEventListener('touchend',   function(e){ var dx=sx-e.changedTouches[0].clientX; if(Math.abs(dx)>40) lbNav(dx>0?1:-1); },{passive:true});
    lb.addEventListener('click',      function(e){ if(e.target===lb) closeLB(); });
    document.addEventListener('keydown', function(e){ if(!document.getElementById('bd-lb').classList.contains('open')) return; if(e.key==='ArrowLeft') lbNav(-1); if(e.key==='ArrowRight') lbNav(1); if(e.key==='Escape') closeLB(); });
})();

/* ---- Video Player ---- */
function playBsVid(thumb, url){
    var card   = thumb.parentElement;
    var iframe = card.querySelector('.bs-vid-iframe');
    thumb.style.display  = 'none';
    iframe.src           = url+'&autoplay=1';
    iframe.style.display = 'block';
}

/* ---- Reel Modal ---- */
function openReel(url){
    if(!url) return;
    var m=url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/);
    document.getElementById('reel-frame').src = m ? 'https://www.youtube.com/embed/'+m[1]+'?autoplay=1&rel=0' : url;
    document.getElementById('reel-modal').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeReel(){
    document.getElementById('reel-modal').classList.remove('open');
    document.getElementById('reel-frame').src='';
    document.body.style.overflow='';
}

/* ---- Share ---- */
function shareBiz(){
    if(navigator.share){
        navigator.share({title:'<?= e($biz['name']) ?>',text:'Check out <?= e($biz['name']) ?> on SMARTUAE!',url:window.location.href});
    } else {
        if(navigator.clipboard) navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
