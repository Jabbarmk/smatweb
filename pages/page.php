<?php
require_once __DIR__ . '/../includes/config.php';
$currentPage = '';

$db = getDB();
$slug = $_GET['slug'] ?? '';

$stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND is_active = 1");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    $pageTitle = 'Page Not Found';
    include __DIR__ . '/../includes/header.php';
    include __DIR__ . '/../includes/topbar.php';
    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Page not found</p></div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = $page['title'];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1><?= e($page['title']) ?></h1>
</div>

<div style="padding: 16px;">
    <div style="background: var(--white); border-radius: 14px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <?= nl2br(e($page['content'])) ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
