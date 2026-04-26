<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'SMARTUAE Jobs';
$currentPage = 'jobs';

$db = getDB();

$search = $_GET['search'] ?? '';

$query = "SELECT * FROM jobs WHERE is_active = 1";
$params = [];
if ($search) {
    $query .= " AND (title LIKE ? OR company LIKE ? OR description LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}
$query .= " ORDER BY is_featured DESC, posted_at DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$profile = $db->query("SELECT * FROM user_profiles WHERE is_active = 1 LIMIT 1")->fetch();

/* Company logo fallbacks — gradient colors cycle per job */
$logoColors = [
    ['#6C5CE7','#8B5CF6'], ['#00B894','#00CEC9'], ['#FD79A8','#FF6B6B'],
    ['#FDCB6E','#F39C12'], ['#0984E3','#74B9FF'], ['#E17055','#D63031'],
];

/* Avatar fallbacks */
$avatarFallbacks = [
    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
    'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face',
    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
];

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <h1>SMARTUAE <span class="gradient-text" style="font-style:italic;">JOBS</span></h1>
</div>

<!-- Jobs Hero -->
<div style="position:relative;margin:16px;border-radius:20px;overflow:hidden;height:180px;box-shadow:0 8px 30px rgba(0,0,0,0.15);">
    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=360&fit=crop"
         alt="Jobs UAE" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(139,92,246,0.80),rgba(108,92,231,0.65));display:flex;flex-direction:column;justify-content:center;padding:24px;">
        <div style="color:rgba(255,255,255,.8);font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;">UAE Job Market</div>
        <div style="color:#fff;font-size:22px;font-weight:800;line-height:1.2;">Find Your Dream Job</div>
        <div style="color:rgba(255,255,255,.85);font-size:13px;margin-top:8px;">Top companies hiring right now</div>
        <a href="<?= SITE_URL ?>/pages/profile.php" style="display:inline-block;margin-top:14px;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:#fff;padding:8px 20px;border-radius:50px;font-size:13px;font-weight:600;">Upload Your CV</a>
    </div>
</div>

<?php if ($profile): ?>
<div class="jobs-profile">
    <img src="<?= getImageUrl($profile['photo'], 'profiles') ?>"
         alt="<?= e($profile['full_name']) ?>"
         class="avatar"
         onerror="this.onerror=null;this.src='<?= $avatarFallbacks[0] ?>'">
    <div class="profile-info">
        <h2><?= e($profile['full_name']) ?></h2>
        <p><?= e($profile['title']) ?></p>
        <div class="profile-btns">
            <a href="<?= SITE_URL ?>/pages/profile.php?id=<?= $profile['id'] ?>" class="btn-outline">My CV</a>
            <a href="#" class="btn-filled">My Jobs</a>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Sample profile when DB empty -->
<div class="jobs-profile">
    <img src="<?= $avatarFallbacks[0] ?>" alt="Professional" class="avatar">
    <div class="profile-info">
        <h2>Your Profile</h2>
        <p>Create your Smart CV today</p>
        <div class="profile-btns">
            <a href="<?= SITE_URL ?>/pages/profile.php" class="btn-outline">Create CV</a>
            <a href="#" class="btn-filled">My Jobs</a>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="page-search">
    <i class="fas fa-search search-icon"></i>
    <input type="text" placeholder="Search jobs, companies..." value="<?= e($search) ?>"
           onkeypress="if(event.key==='Enter') window.location.href='?search='+this.value">
</div>

<div class="section-header">
    <h2>Featured Jobs</h2>
</div>

<?php if (!empty($jobs)): ?>
    <?php foreach ($jobs as $idx => $job):
        $badgeClass = 'badge-fulltime';
        if ($job['job_type'] === 'Part Time') $badgeClass = 'badge-parttime';
        elseif ($job['job_type'] === 'Contract') $badgeClass = 'badge-contract';
        elseif ($job['job_type'] === 'Freelance') $badgeClass = 'badge-freelance';
        $c = $logoColors[$idx % count($logoColors)];
    ?>
    <a href="<?= SITE_URL ?>/pages/job_detail.php?id=<?= $job['id'] ?>" class="job-card">
        <div class="job-logo-placeholder" style="background:linear-gradient(135deg,<?= $c[0] ?>,<?= $c[1] ?>);">
            <i class="fas fa-building"></i>
        </div>
        <div class="job-info">
            <h3><?= e($job['title']) ?></h3>
            <div class="company"><?= e($job['company']) ?></div>
            <div class="salary"><i class="fas fa-coins"></i> <?= e($job['currency']) ?> <?= number_format($job['salary_min']) ?> – <?= number_format($job['salary_max']) ?></div>
            <div class="job-location"><i class="fas fa-map-marker-alt"></i> <?= e($job['location']) ?></div>
            <div class="job-time"><i class="far fa-clock"></i> <?= date('d M Y', strtotime($job['posted_at'])) ?></div>
        </div>
        <span class="job-type-badge <?= $badgeClass ?>"><?= e($job['job_type']) ?></span>
    </a>
    <?php endforeach; ?>
<?php else: ?>
<!-- Sample jobs when DB is empty -->
<?php
$sampleJobs = [
    ['title'=>'Senior UI/UX Designer','company'=>'Emaar Properties','salary'=>'15,000 – 22,000','loc'=>'Dubai, UAE','type'=>'Full Time','badge'=>'badge-fulltime','icon'=>'fas fa-palette','clr'=>['#6C5CE7','#8B5CF6']],
    ['title'=>'React.js Developer','company'=>'Noon Digital','salary'=>'12,000 – 18,000','loc'=>'Abu Dhabi, UAE','type'=>'Full Time','badge'=>'badge-fulltime','icon'=>'fab fa-react','clr'=>['#00B894','#00CEC9']],
    ['title'=>'Marketing Manager','company'=>'DAMAC Group','salary'=>'18,000 – 28,000','loc'=>'Dubai Media City','type'=>'Contract','badge'=>'badge-contract','icon'=>'fas fa-chart-line','clr'=>['#FD79A8','#FF6B6B']],
    ['title'=>'Sales Executive','company'=>'Al-Futtaim Group','salary'=>'8,000 – 13,000','loc'=>'Sharjah, UAE','type'=>'Part Time','badge'=>'badge-parttime','icon'=>'fas fa-handshake','clr'=>['#FDCB6E','#F39C12']],
    ['title'=>'DevOps Engineer','company'=>'Careem Networks','salary'=>'20,000 – 30,000','loc'=>'Dubai Internet City','type'=>'Full Time','badge'=>'badge-fulltime','icon'=>'fas fa-server','clr'=>['#0984E3','#74B9FF']],
    ['title'=>'Graphic Designer','company'=>'MBC Group','salary'=>'7,000 – 11,000','loc'=>'Dubai Media City','type'=>'Freelance','badge'=>'badge-freelance','icon'=>'fas fa-pencil-ruler','clr'=>['#E17055','#D63031']],
];
foreach ($sampleJobs as $j): ?>
<div class="job-card">
    <div class="job-logo-placeholder" style="background:linear-gradient(135deg,<?= $j['clr'][0] ?>,<?= $j['clr'][1] ?>);">
        <i class="<?= $j['icon'] ?>"></i>
    </div>
    <div class="job-info">
        <h3><?= $j['title'] ?></h3>
        <div class="company"><?= $j['company'] ?></div>
        <div class="salary"><i class="fas fa-coins"></i> AED <?= $j['salary'] ?>/mo</div>
        <div class="job-location"><i class="fas fa-map-marker-alt"></i> <?= $j['loc'] ?></div>
        <div class="job-time"><i class="far fa-clock"></i> Today</div>
    </div>
    <span class="job-type-badge <?= $j['badge'] ?>"><?= $j['type'] ?></span>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Companies Hiring Banner -->
<div class="section-header" style="margin-top:8px;">
    <h2>Top Companies Hiring</h2>
</div>
<div class="gallery-row">
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1497366412874-3415097a27e7?w=320&h=240&fit=crop" alt="Tech Company">
        <div class="gallery-overlay">Tech Sector</div>
    </div>
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=320&h=240&fit=crop" alt="Finance">
        <div class="gallery-overlay">Finance & Banking</div>
    </div>
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1590736969955-71cc94901144?w=320&h=240&fit=crop" alt="Healthcare">
        <div class="gallery-overlay">Healthcare</div>
    </div>
    <div class="gallery-item" style="width:160px;">
        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=320&h=240&fit=crop" alt="Construction">
        <div class="gallery-overlay">Real Estate</div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
