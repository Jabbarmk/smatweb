<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - SMARTUAE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/includes/admin.css">
</head>
<body>
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h2>SMART<span>UAE</span></h2>
            <small>Admin Panel</small>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="sliders.php" class="nav-link"><i class="fas fa-images"></i> Sliders</a>
            <a href="main_categories.php" class="nav-link"><i class="fas fa-layer-group"></i> Main Categories</a>
            <a href="popular_categories.php" class="nav-link"><i class="fas fa-star"></i> Popular Categories</a>
            <a href="business_categories.php" class="nav-link"><i class="fas fa-folder"></i> Business Categories</a>
            <a href="businesses.php" class="nav-link"><i class="fas fa-store"></i> Businesses</a>
            <a href="offers.php" class="nav-link"><i class="fas fa-tag"></i> Offers</a>
            <a href="classified_categories.php" class="nav-link"><i class="fas fa-tags"></i> Classified Categories</a>
            <a href="classified_sections.php" class="nav-link"><i class="fas fa-layer-group"></i> Classified Sections</a>
            <a href="classifieds.php" class="nav-link"><i class="fas fa-list"></i> Classifieds</a>
            <a href="jobs.php" class="nav-link"><i class="fas fa-briefcase"></i> Jobs</a>
            <a href="profiles.php" class="nav-link"><i class="fas fa-id-card"></i> Profiles / CVs</a>
            <a href="pages.php" class="nav-link"><i class="fas fa-file-alt"></i> Pages</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="nav-link" style="color:#E53935;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
            <div class="admin-user">
                <i class="fas fa-user-circle"></i> <?= e($_SESSION['admin_name'] ?? 'Admin') ?>
            </div>
        </div>
        <div class="admin-content">
