<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Smart CV';
$currentPage = 'profile';

$db = getDB();
$id = (int)($_GET['id'] ?? 1);

$stmt = $db->prepare("SELECT * FROM user_profiles WHERE id = ?");
$stmt->execute([$id]);
$profile = $stmt->fetch();

if (!$profile) {
    $profile = $db->query("SELECT * FROM user_profiles WHERE is_active = 1 LIMIT 1")->fetch();
}

$avatarFallback = 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face';

include __DIR__ . '/../includes/header.php';
?>

<div class="page-topbar">
    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    <h1>SMART <span class="gradient-text" style="font-style:italic;">CV</span></h1>
    <div class="right-actions">
        <span class="cv-tab active" style="font-size:12px;padding:4px 10px;">Applied</span>
        <span class="cv-tab" style="font-size:12px;padding:4px 10px;">Posted</span>
    </div>
</div>

<?php if ($profile): ?>

<!-- CV Header with gradient -->
<div class="cv-header">
    <img src="<?= getImageUrl($profile['photo'], 'profiles') ?>"
         alt="<?= e($profile['full_name']) ?>"
         class="avatar"
         onerror="this.onerror=null;this.src='<?= $avatarFallback ?>'">
    <h1><?= e($profile['full_name']) ?></h1>
    <p><?= e($profile['title']) ?></p>
    <div class="cv-contact">
        <?php if ($profile['linkedin']): ?><a href="<?= e($profile['linkedin']) ?>"><i class="fab fa-linkedin"></i></a><?php endif; ?>
        <?php if ($profile['email']): ?><a href="mailto:<?= e($profile['email']) ?>"><i class="fas fa-envelope"></i></a><?php endif; ?>
        <?php if ($profile['whatsapp']): ?><a href="https://wa.me/<?= e($profile['whatsapp']) ?>" class="whatsapp"><i class="fab fa-whatsapp"></i></a><?php endif; ?>
        <?php if ($profile['phone']): ?><a href="tel:<?= e($profile['phone']) ?>"><i class="fas fa-phone"></i></a><?php endif; ?>
        <!-- Always show LinkedIn and email icons even if empty for demo -->
        <?php if (!$profile['linkedin'] && !$profile['email']): ?>
        <a href="#"><i class="fab fa-linkedin"></i></a>
        <a href="#"><i class="fas fa-envelope"></i></a>
        <a href="#" class="whatsapp"><i class="fab fa-whatsapp"></i></a>
        <a href="#"><i class="fas fa-phone"></i></a>
        <?php endif; ?>
    </div>
    <?php if ($profile['phone']): ?>
    <div class="cv-phone"><i class="fas fa-phone"></i> <?= e($profile['phone']) ?></div>
    <?php endif; ?>
</div>

<!-- Experience & Education Summary -->
<div class="cv-section">
    <div class="cv-section-body" style="display:flex;gap:16px;align-items:center;">
        <div class="cv-exp-badge">
            <span class="num"><?= $profile['experience_years'] ?: '5' ?></span>
            <span class="txt">Years<br>Experience</span>
        </div>
        <div>
            <p><strong>Education:</strong> <?= e($profile['education'] ?: 'Bachelor\'s Degree') ?></p>
            <p style="margin-top:4px;"><strong>Currently at:</strong> <?= e($profile['current_company'] ?: 'Available') ?></p>
        </div>
    </div>
</div>

<!-- Professional Photo Banner -->
<div style="margin:0 16px 4px;border-radius:20px;overflow:hidden;height:120px;position:relative;box-shadow:var(--shadow);">
    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=240&fit=crop"
         alt="Professional" style="width:100%;height:100%;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(to right,rgba(108,92,231,.70),rgba(0,206,201,.45));display:flex;align-items:center;padding:20px;gap:14px;">
        <i class="fas fa-briefcase" style="color:#fff;font-size:32px;opacity:.9;"></i>
        <div style="color:#fff;">
            <div style="font-size:16px;font-weight:800;">Professional Profile</div>
            <div style="font-size:12px;opacity:.85;margin-top:3px;">Available for new opportunities</div>
        </div>
    </div>
</div>

<!-- Work Experience -->
<?php if ($profile['work_experience']): ?>
<div class="cv-section">
    <div class="cv-section-title orange"><i class="fas fa-briefcase" style="margin-right:8px;"></i>Work Experience</div>
    <div class="cv-section-body">
        <?php foreach (explode("\n", $profile['work_experience']) as $exp): ?>
        <?php if (trim($exp)):
            $parts = explode('|', $exp); ?>
        <div class="cv-exp-item">
            <div>
                <strong><?= e(trim($parts[0] ?? '')) ?></strong><br>
                <small style="color:var(--text-secondary);"><?= e(trim($parts[1] ?? '')) ?></small>
            </div>
            <div style="text-align:right;font-size:12px;color:var(--text-light);">
                <?= e(trim($parts[2] ?? '')) ?><br>
                <small><?= e(trim($parts[3] ?? '')) ?></small>
            </div>
        </div>
        <?php endif; endforeach; ?>
    </div>
</div>
<?php else: ?>
<!-- Demo work experience -->
<div class="cv-section">
    <div class="cv-section-title orange"><i class="fas fa-briefcase" style="margin-right:8px;"></i>Work Experience</div>
    <div class="cv-section-body">
        <?php
        $demoExp = [
            ['Senior Developer','Dubai Media City','Emaar Tech','2022 – Present'],
            ['UI/UX Designer','Business Bay, Dubai','DAMAC Group','2020 – 2022'],
            ['Junior Developer','Downtown Dubai','Noon Digital','2018 – 2020'],
        ];
        foreach ($demoExp as $e): ?>
        <div class="cv-exp-item">
            <div>
                <strong><?= $e[0] ?></strong><br>
                <small style="color:var(--text-secondary);"><?= $e[2] ?></small>
            </div>
            <div style="text-align:right;font-size:12px;color:var(--text-light);">
                <?= $e[3] ?><br><small><?= $e[1] ?></small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Technical Skills -->
<?php if ($profile['technical_skills']): ?>
<div class="cv-section">
    <div class="cv-section-title"><i class="fas fa-code" style="margin-right:8px;"></i>Technical Skills</div>
    <div class="cv-section-body">
        <div class="cv-skill-grid">
            <?php foreach (explode(',', $profile['technical_skills']) as $skill): ?>
            <div class="cv-skill-item"><?= e(trim($skill)) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Demo skills -->
<div class="cv-section">
    <div class="cv-section-title"><i class="fas fa-code" style="margin-right:8px;"></i>Technical Skills</div>
    <div class="cv-section-body">
        <div class="cv-skill-grid">
            <?php foreach (['PHP','JavaScript','React','MySQL','Laravel','Node.js','Python','Figma','Git','Docker','AWS','Linux'] as $sk): ?>
            <div class="cv-skill-item"><?= $sk ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Certifications -->
<?php if ($profile['certifications']): ?>
<div class="cv-section">
    <div class="cv-section-title green"><i class="fas fa-certificate" style="margin-right:8px;"></i>Certifications</div>
    <div class="cv-section-body">
        <p><?= nl2br(e($profile['certifications'])) ?></p>
    </div>
</div>
<?php else: ?>
<div class="cv-section">
    <div class="cv-section-title green"><i class="fas fa-certificate" style="margin-right:8px;"></i>Certifications</div>
    <div class="cv-section-body">
        <?php
        $certs = [
            ['AWS Certified Solutions Architect','Amazon Web Services · 2024','https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=60&h=60&fit=crop'],
            ['Google UX Design Professional','Google · 2023','https://images.unsplash.com/photo-1573804633927-bfcbcd909acd?w=60&h=60&fit=crop'],
            ['Meta Front-End Developer','Meta · 2023','https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?w=60&h=60&fit=crop'],
        ];
        foreach ($certs as $c): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--bg);">
            <img src="<?= $c[2] ?>" alt="cert" style="width:44px;height:44px;border-radius:10px;object-fit:cover;flex-shrink:0;">
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--dark);"><?= $c[0] ?></div>
                <div style="font-size:11px;color:var(--text-light);margin-top:2px;"><?= $c[1] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Education Details -->
<?php if ($profile['education_details']): ?>
<div class="cv-section">
    <div class="cv-section-title purple"><i class="fas fa-graduation-cap" style="margin-right:8px;"></i>Education</div>
    <div class="cv-section-body">
        <?php foreach (explode("\n", $profile['education_details']) as $edu): ?>
        <?php if (trim($edu)): ?><p><?= e(trim($edu)) ?></p><?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="cv-section">
    <div class="cv-section-title purple"><i class="fas fa-graduation-cap" style="margin-right:8px;"></i>Education</div>
    <div class="cv-section-body">
        <div style="display:flex;align-items:center;gap:12px;padding:8px 0;">
            <img src="https://images.unsplash.com/photo-1562774053-701939374585?w=80&h=80&fit=crop"
                 alt="University" style="width:52px;height:52px;border-radius:12px;object-fit:cover;flex-shrink:0;">
            <div>
                <div style="font-size:14px;font-weight:700;color:var(--dark);">BSc Computer Science</div>
                <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;">UAE University · 2015 – 2019</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Projects -->
<?php if ($profile['projects']): ?>
<div class="cv-section">
    <div class="cv-section-title teal"><i class="fas fa-rocket" style="margin-right:8px;"></i>Projects</div>
    <div class="cv-section-body">
        <p><?= nl2br(e($profile['projects'])) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Languages -->
<div class="cv-section">
    <div class="cv-section-title orange"><i class="fas fa-globe" style="margin-right:8px;"></i>Languages</div>
    <div class="cv-section-body">
        <?php
        $langs = $profile['languages'] ? explode(',', $profile['languages']) : ['Arabic – Native','English – Fluent','French – Intermediate'];
        ?>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            <?php foreach ($langs as $lang): ?>
            <div style="padding:8px 16px;background:linear-gradient(135deg,rgba(108,92,231,.08),rgba(0,206,201,.08));border-radius:50px;font-size:13px;font-weight:600;color:var(--text-secondary);">
                🌐 <?= e(trim($lang)) ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Download CV CTA -->
<div style="margin:12px 16px 8px;padding:20px;border-radius:20px;background:linear-gradient(135deg,var(--primary),#8B5CF6);color:#fff;text-align:center;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-30%;right:-20%;width:120px;height:120px;background:rgba(255,255,255,.1);border-radius:50%;"></div>
    <i class="fas fa-file-download" style="font-size:28px;margin-bottom:10px;display:block;position:relative;z-index:1;"></i>
    <div style="font-size:16px;font-weight:800;margin-bottom:4px;position:relative;z-index:1;">Download CV</div>
    <div style="font-size:12px;opacity:.9;margin-bottom:14px;position:relative;z-index:1;">Share your profile with employers</div>
    <a href="#" style="display:inline-block;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:#fff;padding:10px 28px;border-radius:50px;font-size:13px;font-weight:700;position:relative;z-index:1;">Download PDF</a>
</div>

<?php else: ?>
<!-- No profile — show create profile prompt with image -->
<div style="margin:20px 16px;border-radius:20px;overflow:hidden;position:relative;box-shadow:var(--shadow-lg);">
    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=480&h=280&fit=crop"
         alt="Profile" style="width:100%;height:200px;object-fit:cover;display:block;">
    <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent,rgba(13,27,42,.85));display:flex;align-items:flex-end;padding:20px;">
        <div style="color:#fff;">
            <div style="font-size:18px;font-weight:800;margin-bottom:6px;">Create Your Smart CV</div>
            <div style="font-size:13px;opacity:.85;margin-bottom:12px;">Build a professional profile that stands out</div>
            <a href="#" style="display:inline-block;background:var(--primary);color:#fff;padding:10px 24px;border-radius:50px;font-size:13px;font-weight:700;">Get Started</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
