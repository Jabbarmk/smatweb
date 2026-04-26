<nav class="bottom-nav">
    <a href="<?= SITE_URL ?>/" class="nav-item <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="<?= SITE_URL ?>/pages/classifieds.php" class="nav-item <?= ($currentPage ?? '') === 'classifieds' ? 'active' : '' ?>">
        <i class="fas fa-th-large"></i>
        <span>Classifieds</span>
    </a>
    <a href="<?= SITE_URL ?>/pages/offers.php" class="nav-item nav-center">
        <div class="nav-plus"><i class="fas fa-search"></i></div>
    </a>
    <a href="<?= SITE_URL ?>/pages/offers.php" class="nav-item <?= ($currentPage ?? '') === 'offers' ? 'active' : '' ?>">
        <i class="fas fa-tag"></i>
        <span>Offers</span>
    </a>
    <a href="<?= SITE_URL ?>/pages/jobs.php" class="nav-item <?= ($currentPage ?? '') === 'jobs' ? 'active' : '' ?>">
        <i class="fas fa-briefcase"></i>
        <span>Jobs</span>
    </a>
</nav>
