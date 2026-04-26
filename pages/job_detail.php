<?php
require_once __DIR__ . '/../includes/config.php';
$currentPage = 'jobs';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) {
    header('Location: ' . SITE_URL . '/pages/jobs.php');
    exit;
}

$pageTitle = $job['title'] . ' - Jobs';
$profile   = $db->query("SELECT * FROM user_profiles WHERE is_active = 1 LIMIT 1")->fetch();

$badgeClass = 'badge-fulltime';
if ($job['job_type'] === 'Part Time') $badgeClass = 'badge-parttime';
elseif ($job['job_type'] === 'Contract') $badgeClass = 'badge-contract';
elseif ($job['job_type'] === 'Freelance') $badgeClass = 'badge-freelance';

/* Determine a hero image based on job title keywords */
$titleLower = strtolower($job['title'] ?? '');
if (str_contains($titleLower,'design') || str_contains($titleLower,'ux'))
    $heroBg = 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=320&fit=crop';
elseif (str_contains($titleLower,'develop') || str_contains($titleLower,'engineer'))
    $heroBg = 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&h=320&fit=crop';
elseif (str_contains($titleLower,'market') || str_contains($titleLower,'sales'))
    $heroBg = 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=320&fit=crop';
elseif (str_contains($titleLower,'finance') || str_contains($titleLower,'account'))
    $heroBg = 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800&h=320&fit=crop';
else
    $heroBg = 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=320&fit=crop';

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1>Job Details</h1>
    <div class="right-actions">
        <i class="fas fa-heart" style="color:var(--warm);"></i>
        <i class="fas fa-share-alt"></i>
    </div>
</div>

<!-- Company / Job Hero Image -->
<div style="position:relative;overflow:hidden;height:160px;">
    <img src="<?= $heroBg ?>" alt="Job" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(108,92,231,.75),rgba(13,27,42,.55));"></div>
    <!-- Company logo overlay -->
    <div style="position:absolute;bottom:16px;left:16px;display:flex;align-items:center;gap:12px;">
        <div style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg,var(--primary),#8B5CF6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;box-shadow:0 4px 14px rgba(0,0,0,.25);border:2px solid rgba(255,255,255,.3);">
            <i class="fas fa-building"></i>
        </div>
        <div>
            <div style="color:#fff;font-size:14px;font-weight:700;"><?= e($job['company']) ?></div>
            <div style="color:rgba(255,255,255,.75);font-size:12px;"><?= e($job['location']) ?></div>
        </div>
    </div>
</div>

<div class="job-detail-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
        <h1><?= e($job['title']) ?></h1>
        <span class="job-type-badge <?= $badgeClass ?>" style="position:static;flex-shrink:0;margin-left:8px;"><?= e($job['job_type']) ?></span>
    </div>
    <div class="company" style="margin-top:4px;"><?= e($job['company']) ?></div>
    <div class="meta" style="margin-top:12px;">
        <span><i class="fas fa-coins"></i> <?= e($job['currency']) ?> <?= number_format($job['salary_min']) ?> – <?= number_format($job['salary_max']) ?>/mo</span>
        <span><i class="fas fa-map-marker-alt"></i> <?= e($job['location']) ?></span>
        <span><i class="far fa-clock"></i> <?= date('d M Y', strtotime($job['posted_at'])) ?></span>
    </div>
</div>

<button class="apply-btn" onclick="alert('Application submitted! We will contact you soon.')">
    <i class="fas fa-paper-plane" style="margin-right:8px;"></i>Apply Now
</button>

<!-- Perks strip -->
<div style="display:flex;gap:8px;padding:0 16px 16px;overflow-x:auto;">
    <?php
    $perks = [
        ['icon'=>'fas fa-laptop-house','label'=>'Remote OK'],
        ['icon'=>'fas fa-heart','label'=>'Health Ins.'],
        ['icon'=>'fas fa-plane','label'=>'Air Ticket'],
        ['icon'=>'fas fa-graduation-cap','label'=>'Training'],
    ];
    foreach ($perks as $p): ?>
    <div style="flex-shrink:0;display:flex;align-items:center;gap:6px;padding:8px 14px;background:var(--white);border-radius:50px;border:1.5px solid var(--glass-border);font-size:12px;font-weight:600;color:var(--text-secondary);">
        <i class="<?= $p['icon'] ?>" style="color:var(--primary);font-size:13px;"></i><?= $p['label'] ?>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($job['description']): ?>
<div class="job-section">
    <h3><i class="fas fa-align-left" style="color:var(--primary);margin-right:8px;font-size:14px;"></i>Job Description</h3>
    <p><?= nl2br(e($job['description'])) ?></p>
</div>
<?php endif; ?>

<?php if ($job['requirements']): ?>
<div class="job-section">
    <h3><i class="fas fa-list-check" style="color:var(--primary);margin-right:8px;font-size:14px;"></i>Requirements</h3>
    <ul>
        <?php foreach (explode("\n", $job['requirements']) as $req): ?>
        <?php if (trim($req)): ?><li><?= e(trim($req)) ?></li><?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if ($job['benefits']): ?>
<div class="job-section">
    <h3><i class="fas fa-gift" style="color:var(--primary);margin-right:8px;font-size:14px;"></i>Benefits & Perks</h3>
    <ul>
        <?php foreach (explode("\n", $job['benefits']) as $ben): ?>
        <?php if (trim($ben)): ?><li><?= e(trim($ben)) ?></li><?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Company environment photo -->
<div style="margin:0 16px 12px;border-radius:20px;overflow:hidden;height:140px;position:relative;box-shadow:var(--shadow);">
    <img src="https://images.unsplash.com/photo-1497366412874-3415097a27e7?w=800&h=280&fit=crop"
         alt="Office" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(13,27,42,.6),transparent);display:flex;align-items:center;padding:20px;">
        <div style="color:#fff;">
            <div style="font-size:12px;opacity:.8;margin-bottom:4px;font-weight:600;">COMPANY CULTURE</div>
            <div style="font-size:16px;font-weight:800;">Great Place to Work</div>
        </div>
    </div>
</div>

<?php if ($profile): ?>
<div class="job-section" style="display:flex;align-items:center;gap:14px;">
    <img src="<?= getImageUrl($profile['photo'], 'profiles') ?>"
         alt="<?= e($profile['full_name']) ?>"
         style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid var(--primary-light);"
         onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop&crop=face'">
    <div>
        <h3 style="margin-bottom:2px;"><?= e($profile['full_name']) ?></h3>
        <p style="font-size:12px;color:var(--text-light);"><?= e($profile['title']) ?></p>
        <a href="<?= SITE_URL ?>/pages/profile.php?id=<?= $profile['id'] ?>" class="btn-filled" style="margin-top:8px;display:inline-block;font-size:12px;padding:6px 16px;">View My CV</a>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
