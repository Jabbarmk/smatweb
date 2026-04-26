<?php
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => $_POST['site_name'] ?? '',
        'site_tagline' => $_POST['site_tagline'] ?? '',
        'contact_phone' => $_POST['contact_phone'] ?? '',
        'contact_email' => $_POST['contact_email'] ?? '',
        'whatsapp' => $_POST['whatsapp'] ?? '',
    ];

    foreach ($settings as $key => $value) {
        $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }

    // Change password if provided
    if (!empty($_POST['new_password'])) {
        $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?")->execute([$hash, $_SESSION['admin_id']]);
    }

    $msg = 'Settings saved!';
}

$settings = [];
$rows = $db->query("SELECT * FROM site_settings")->fetchAll();
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Site Settings';
include __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="admin-form">
    <h2 style="margin-bottom: 16px;">Site Settings</h2>
    <form method="POST">
        <div class="form-row">
            <div class="form-group"><label>Site Name</label><input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>"></div>
            <div class="form-group"><label>Tagline</label><input type="text" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Contact Phone</label><input type="text" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '') ?>"></div>
            <div class="form-group"><label>Contact Email</label><input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>WhatsApp Number</label><input type="text" name="whatsapp" value="<?= e($settings['whatsapp'] ?? '') ?>"></div>

        <hr style="margin: 24px 0; border-color: #eee;">
        <h3 style="margin-bottom: 12px;">Change Admin Password</h3>
        <div class="form-group"><label>New Password (leave empty to keep current)</label><input type="password" name="new_password" autocomplete="new-password"></div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
