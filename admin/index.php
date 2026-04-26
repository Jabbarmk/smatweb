<?php
require_once __DIR__ . '/../includes/config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();

// Dashboard stats
$stats = [
    'sliders'     => $db->query("SELECT COUNT(*) FROM sliders")->fetchColumn(),
    'categories'  => $db->query("SELECT COUNT(*) FROM business_categories")->fetchColumn(),
    'businesses'  => $db->query("SELECT COUNT(*) FROM businesses")->fetchColumn(),
    'offers'      => $db->query("SELECT COUNT(*) FROM offers WHERE is_active=1")->fetchColumn(),
    'classifieds' => $db->query("SELECT COUNT(*) FROM classifieds")->fetchColumn(),
    'jobs'        => $db->query("SELECT COUNT(*) FROM jobs")->fetchColumn(),
    'profiles'    => $db->query("SELECT COUNT(*) FROM user_profiles")->fetchColumn(),
];

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-stats">
    <div class="stat-card" style="border-left: 4px solid #E53935;">
        <div class="stat-number"><?= $stats['sliders'] ?></div>
        <div class="stat-label">Sliders</div>
        <a href="sliders.php">Manage &rarr;</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid #1565C0;">
        <div class="stat-number"><?= $stats['categories'] ?></div>
        <div class="stat-label">Business Categories</div>
        <a href="business_categories.php">Manage &rarr;</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid #2E7D32;">
        <div class="stat-number"><?= $stats['businesses'] ?></div>
        <div class="stat-label">Businesses</div>
        <a href="businesses.php">Manage &rarr;</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid #E17055;">
        <div class="stat-number"><?= $stats['offers'] ?></div>
        <div class="stat-label">Active Offers</div>
        <a href="offers.php">Manage &rarr;</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid #E65100;">
        <div class="stat-number"><?= $stats['classifieds'] ?></div>
        <div class="stat-label">Classifieds</div>
        <a href="classifieds.php">Manage &rarr;</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid #6A1B9A;">
        <div class="stat-number"><?= $stats['jobs'] ?></div>
        <div class="stat-label">Jobs</div>
        <a href="jobs.php">Manage &rarr;</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid #00838F;">
        <div class="stat-number"><?= $stats['profiles'] ?></div>
        <div class="stat-label">Profiles / CVs</div>
        <a href="profiles.php">Manage &rarr;</a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
